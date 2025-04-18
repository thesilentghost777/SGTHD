<?php

namespace App\Http\Controllers;

use App\Models\ProduitRecu1;
use App\Models\Produit_fixes;
use App\Models\ProduitStock;
use App\Models\Commande;
use App\Models\User;
use App\Http\Controllers\NotificationController;
use App\Traits\HistorisableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PointeurController extends Controller
{
    use HistorisableActions;
    protected $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    public function enregistrerProduit(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:Produit_fixes,code_produit',
            'quantite' => 'required|integer|min:1',
            'producteur_id' => 'required|exists:users,id',
            'remarques' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $produitId = $validated['produit_id'];
                $quantite = $validated['quantite'];
                $producteurId = $validated['producteur_id'];
                $pointeurId = auth()->id();

                // Vérifier si une entrée existe déjà pour ce produit aujourd'hui par ce producteur
                $aujourdhui = now()->startOfDay();
                $produitExistant = ProduitRecu1::where('produit_id', $produitId)
                    ->where('producteur_id', $producteurId)
                    ->whereDate('date_reception', $aujourdhui)
                    ->first();

                if ($produitExistant) {
                    // Mettre à jour l'entrée existante
                    $produitExistant->quantite += $quantite;
                    $produitExistant->remarques .= "\n" . ($validated['remarques'] ?? "Mise à jour le " . now()->format('d/m/Y H:i'));
                    $produitExistant->save();

                    $this->historiser("Mise à jour de la quantité du produit #$produitId : +$quantite unités", 'update', $produitExistant->id, 'produit_recu');
                } else {
                    // Créer une nouvelle entrée
                    $produitRecu = ProduitRecu1::create([
                        'produit_id' => $produitId,
                        'quantite' => $quantite,
                        'producteur_id' => $producteurId,
                        'pointeur_id' => $pointeurId,
                        'date_reception' => now(),
                        'remarques' => $validated['remarques'] ?? null
                    ]);

                    $this->historiser("Enregistrement du produit #$produitId : $quantite unités", 'create', $produitRecu->id, 'produit_recu');
                }

            });

            return redirect()->route('pointer.workspace')
                ->with('success', 'Produit enregistré avec succès et stock mis à jour');

        } catch (\Exception $e) {
            return redirect()->route('pointer.workspace')
                ->with('error', 'Erreur lors de l\'enregistrement du produit: ' . $e->getMessage());
        }
    }


    public function dashboard()
    {
        $user = auth()->user();
        $nom = $user->name;
        $secteur = $user->secteur;
        $produitsRecus = ProduitRecu1::with(['produit', 'producteur'])
            ->orderBy('date_reception', 'desc')
            ->take(5)
            ->get();

        $commandesEnAttente = Commande::where('valider', false)
            ->with('produit_fixe')
            ->orderBy('date_commande', 'desc')
            ->get();

        return view('pointeur.dashboard', compact('produitsRecus', 'commandesEnAttente', 'nom', 'secteur'));
    }


    public function validerCommande(Request $request, Commande $commande)
    {
        $stock = ProduitStock::where('id_produit', $commande->produit)->first();
        $produit = Produit_fixes::where('code_produit', $commande->produit)->first();
        if (!$stock) {
            // Créer une entrée de stock si elle n'existe pas
            $stock = ProduitStock::create([
                'id_produit' => $commande->produit,
                'quantite_en_stock' => 0,
                'quantite_invendu' => 0,
                'quantite_avarie' => 0
            ]);

            $this->historiser("Création d'une entrée de stock pour le produit #{$produit->nom}", 'create', $stock->id, 'produit_stock');
        }

        if ($stock->quantite_en_stock < $commande->quantite) {
            return back()->with('error', 'Stock insuffisant pour valider cette commande');
        }
        $user = auth()->user();

        try {
            DB::transaction(function () use ($commande, $stock, $request, $produit, $user) {
                // Valider la commande
                $commande->valider = true;
                $commande->save();

                // Mettre à jour le stock
                $stock->quantite_en_stock -= $commande->quantite;
                $stock->save();

                $this->historiser(
                    "Validation de la commande #{$commande->id}: {$commande->quantite} {$produit->nom} par {$user->name}",
                    'update',
                    $commande->id,
                    'commande'
                );
                #notifier tous les chef_production
                $chefs = User::where('role', 'chef_production')->get();
                $user = auth()->user();
                foreach ($chefs as $chef) {
                    $request->merge([
                        'recipient_id' => $chef->id,
                        'subject' => 'Validation de la commande',
                        'message' => 'La commande #' . $commande->id . ' a été validée avec succès par ' . $user->name,
                    ]);
                    // Appel de la méthode send
                    $this->notificationController->send($request);
                }
            });

            return back()->with('success', 'Commande validée avec succès et stock mis à jour');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la validation de la commande: ' . $e->getMessage());
        }
    }
}
