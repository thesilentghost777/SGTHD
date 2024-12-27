<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProduitRecu;
use App\Models\Produit_fixes;
use App\Models\Production;
use App\Models\User;
use App\Models\VersementCsg;
use App\Models\Transaction_vente;
use Carbon\Carbon;
class ServeurController extends Controller
{
    public function dashboard() {
         $employe = auth()->user();

         if (!$employe) {
           return redirect()->route('login')->with('error', 'Veuillez vous connecter');
         }
        $produits = ProduitRecu::latest()->get();
        $proV=Transaction_vente::latest()->get();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages/serveur/serveur_dashboard',['produits' => $produits,'proV'=>$proV,'heure_actuelle' => $heure_actuelle]);
    }
   public function store(Request $request){
    $validate=  $request->validate([
        'pointeur'=>'required',
        'produit'=>'required',
        'nom'=>'required',
        'prix'=>'required',
        'quantite'=>'required',
        
        ]);
        $produits=ProduitRecu::create($validate);
        
        return redirect()->route('serveur-dashboard')->with('Produit ajoute avec succes');

   }
    public function ajouterProduit_recu(){
        $Employe=User::where('role','pointeur')->get();
        $produitR=Produit_fixes::all();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
      return view('pages\serveur\ serveur-ajouterProduit_recu',compact('Employe','produitR','heure_actuelle'));
    }
    public function store_vendu(Request $request) {
         try { 
            $validated = $request->validate([ 
                'produit' => 'required|exists:produit_fixes,code_produit',
                 'quantite' => 'required|numeric|min:1', 
                 'prix' => 'required|numeric|min:0', 
                ]); 
            $produitR = Produit_fixes::find($request->code_produit); 
            if($produitR){
            $quantiteDisponible = $produitR->productions()->sum('quantite');
             if($quantiteDisponible <= $request->quantite) {
                 return back()->with('error', 'Stock insuffisant pour la vente'); }
            }else{
                return back()->with('error','Produit introuvable');
            }    
                    $quantiteRestante = $request->quantite;
                     foreach($produitR->productions as $production) { 
                        if($quantiteRestante <= 0) break;
                         $reduction = min($production->quantite, $quantiteRestante); 
                         $production->quantite -= $reduction;
                          $production->save(); 
                          $quantiteRestante -= $reduction; }
                          $proV= Transaction_vente::create([
                           'produit'=>$produitR->produit,
                            'serveur'=>auth()->user()->id,
                            'quantite'=>$request->quantite,
                            'prix'=>$request->prix,
                            'total_ventes'=>$request->quantite * $request->prix ,
                            'date_vente'=>Carbon::now(),
                        ]);
                                return redirect()->route('serveur-dashboard')->with('success', 'Vente enregistrée avec succès'); }
                                 catch(\Exception $e) { 
                                    return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage());
                                 }
                                 } 
    public function enrProduit_vendu(){
        $produitR=Produit_fixes::all();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages\serveur\ serveur-enrProduit_vendu',compact('produitR','heure_actuelle'));
    }

}
