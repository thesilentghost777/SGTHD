<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PointeurController extends Controller
{
    public function dashboard() {
        return view('pages/pointeur/pointeur_dashboard');
    }
    

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

    
}
