<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use Illuminate\Http\Request;

class PointeurController extends Controller
{
    public function dashboard() {
        return view('pages/pointeur/pointeur_dashboard');
    }
    
=======
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
>>>>>>> 0d622d32b651f385f8c862b621043c3966ba0a8c

public function store(Request $request)
{
    $employe = auth()->user();
    if (!$employe) {       
        return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }
    $validated = $request->validate([
        'produit' => 'required|exists:produit_fixes,code_produit',
        'prix' => 'required|numeric',
        'quantite' => 'required|numeric',
    ]);
    $produitRecu = ProduitRecu::create([
        'pointeur' => $employe->id,
        'produit' => $request->produit,
        'prix' => $request->prix,
        'quantite' => $request->quantite,
    ]);
    $produit = Produit_fixes::where('code_produit',$request->produit)->first();
    foreach($produit->productions as $production) { 
        $production->quantite += $request->quantite;
        $production->save(); 
    }

<<<<<<< HEAD
    return redirect()->route('pointeur-dashboard')->with('success', 'Produit ajouté avec succès');
    }
    public function ajouterProduit_recu(){
      $employe = auth()->user();

      if (!$employe) {
        return redirect()->route('login')->with('error', 'Veuillez vous connecter');
      }
      $Employe=User::where('role','pointeur')->get();
      $produitR=Produit_fixes::all();
      $produits = \DB::table('produit_recus')
      ->join('produit_fixes', 'produit_recus.produit', '=', 'produit_fixes.code_produit')
      ->join('users', 'produit_recus.pointeur', '=', 'users.id')
      ->select('produit_recus.*',
             'produit_fixes.nom as nom',      
              'produit_fixes.prix as prix',
              'users.name as pointeur'
      )
      ->get();
      
      $heure_actuelle = now();
      $heure_actuelle->setTimezone('UTC');
      return view('pages\pointeur\ pointeur-enrProduit_recu',compact('Employe','produitR','produits','heure_actuelle')); 
  }
  public function afficheProduit(){
    $produits = \DB::table('produit_recus')
    ->join('produit_fixes', 'produit_recus.produit', '=', 'produit_fixes.code_produit')
    ->join('users', 'produit_recus.pointeur', '=', 'users.id')
    ->select('produit_recus.*',
           'produit_fixes.nom as nom',      
            'produit_fixes.prix as prix',
            'users.name as pointeur'
    )
    ->get();
    return view('pages/pointeur/pointeur-afficheProduit',compact('produits'));

  }

  public function showNonValide(){
         
        $commandes = \DB::table('commande')
        ->join('produit_fixes', 'commande.produit', '=', 'produit_fixes.code_produit')
        ->select('commande.*',
               'produit_fixes.nom as nom',      
                'produit_fixes.prix as prix',
        )
        ->where('commande.valider',false)
        ->get();
        return view('pages/pointeur/pointeur_commandes',compact('commandes'));
  }
public function validerCommande($id){
       
    $commande = Commande::findOrFail($id);
    $commande->valider = true;
    $commande->save();

    return redirect()->route('valider-commandes')->with('success', 'Commande validée avec succès');

}
public function   comparaison()
    {
        // Requête SQL pour calculer les manquants par pointeur
        $classement = \DB::table('produit_recus as pr')
            ->join('utilisation as u', 'pr.produit', '=', 'u.produit')
            ->join('produit_fixes as pf', 'pr.produit', '=', 'pf.code_produit')
            ->join('users as us', 'pr.pointeur', '=', 'us.id')
            ->select('us.name as pointeur', \DB::raw('SUM((pr.quantite - u.quantite_produit) * pf.prix) as manquants'))
            ->where('us.role','pointeur')
            ->whereColumn('pr.produit', 'u.produit')
            ->groupBy('pr.pointeur', 'us.name')
            ->orderBy('manquants', 'desc')
            ->get();

        return view('pages/pointeur/pointeur_classement', compact('classement'));
    }
    public function edit($produit)
{
    $produit = \DB::table('produit_recus')
        ->join('produit_fixes', 'produit_recus.produit', '=', 'produit_fixes.code_produit')
        ->select('produit_recus.*', 'produit_fixes.nom as nom')
        ->where('produit_recus.produit', $produit)
        ->first();

    if (!$produit) {
        return redirect()->route('pointeur-dashboard')->with('error', 'Produit introuvable');
    }

    return view('pages/pointeur/pointeur-edit', compact('produit'));
}

public function update(Request $request, $produit)
{
    $request->validate([
        'quantite' => 'required|numeric',
        'prix' => 'required|numeric',
    ]);

    \DB::table('produit_recus')
        ->where('produit', $produit)
        ->update([
            'quantite' => $request->quantite,
            'prix' => $request->prix,
        ]);

    return redirect()->route('pointeur-dashboard')->with('success', 'Produit modifié avec succès');
}
public function statistique()
{
    $employe = auth()->user();

    if (!$employe) {
      return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }
    // Calcul des manquants par produit pour un pointeur spécifique
    $stats = \DB::table('produit_recus as pr')
        ->join('utilisation as u', 'pr.produit', '=', 'u.produit')
        ->join('produit_fixes as pf', 'pr.produit', '=', 'pf.code_produit')
        ->join('users as us', 'pr.pointeur', '=', 'us.id')
        ->select('pf.nom as produit', \DB::raw('SUM((u.quantite_produit - pr.quantite) ) as manquants'))
        ->where('us.role', 'pointeur')
        ->where('pr.pointeur',$employe->id)
        ->whereColumn('pr.produit', 'u.produit')
        ->groupBy('pr.produit', 'pf.nom')
        ->orderBy('manquants', 'desc')
        ->get();

    
    $topProduit = $stats->first();

    return view('pages/pointeur/pointeur-stats', compact('stats', 'topProduit'));
}

    
=======

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
>>>>>>> 0d622d32b651f385f8c862b621043c3966ba0a8c
}
