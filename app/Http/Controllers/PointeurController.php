<?php

namespace App\Http\Controllers;

use App\Models\ProduitRecu1;
use App\Models\Produit_fixes;
use App\Models\ProduitStock;
use App\Models\Commande;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointeurController extends Controller
{
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

    public function enregistrerProduit(Request $request)
    {
        $validated = $request->validate([
            'produit_id' => 'required|exists:Produit_fixes,code_produit',
            'quantite' => 'required|integer|min:1',
            'producteur_id' => 'required|exists:users,id',
            'remarques' => 'nullable|string'
        ]);

        // Vérifier si le produit a une entrée dans produit_stock
        $stock = ProduitStock::where('id_produit', $validated['produit_id'])->first();

        if (!$stock) {
            return redirect()->route('pointeur.dashboard')
                ->with('error', 'Ce produit n\'a pas d\'entrée dans le stock. Veuillez contacter l\'administrateur.');
        }

        DB::transaction(function () use ($validated, $request) {
            // Créer l'entrée dans produits_recus
            ProduitRecu1::create([
                'produit_id' => $validated['produit_id'],
                'quantite' => $validated['quantite'],
                'producteur_id' => $validated['producteur_id'],
                'pointeur_id' => auth()->id(),
                'date_reception' => now(),
                'remarques' => $validated['remarques']
            ]);

            // Mettre à jour le stock
            $stock = ProduitStock::where('id_produit', $validated['produit_id'])->first();
            $stock->quantite_en_stock += $validated['quantite'];
            $stock->save();
        });

        return redirect()->route('pointer.workspace')
            ->with('success', 'Produit enregistré avec succès');
    }

    public function validerCommande(Request $request, Commande $commande)
    {
        $stock = ProduitStock::where('id_produit', $commande->produit)->first();

        if (!$stock) {
            return back()->with('error', 'Ce produit n\'a pas d\'entrée dans le stock. Impossible de valider la commande.');
        }

        /*if ($stock->quantite_en_stock < $commande->quantite) {
            return back()->with('error', 'Stock insuffisant pour valider cette commande');
        }*/

        DB::transaction(function () use ($commande, $stock) {
            $commande->valider = true;
            $commande->save();

            $stock->quantite_en_stock -= $commande->quantite;
            $stock->save();
        });

        return back()->with('success', 'Commande validée avec succès');
    }
}
