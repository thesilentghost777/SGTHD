<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProduitRecu;
use App\Models\Produit;
use App\Models\User;
use Carbon\Carbon;
class ServeurController extends Controller
{
    public function dashboard() {
        //  $employe = auth()->user();

        //  if (!$employe) {
        //    return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        //  }
        $produits = ProduitRecu::latest()->get();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages/serveur/serveur_dashboard',['produits' => $produits],['heure_actuelle' => $heure_actuelle]);
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
        $produitR=Produit::all();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
      return view('pages\serveur\ serveur-ajouterProduit_recu',compact('Employe','produitR','heure_actuelle'));
    }
}
