<?php

namespace App\Http\Controllers;
use App\Models\Produit;
use Illuminate\Http\Request;

class ProducteurController extends Controller
{
    //gestion des affichage filtrer et save
    /*
    public function index() {
        $employe = auth()->user();
        $produits2 = Produit::where('producteur',$employe->code_employe)->get();
        return view('produits',compact('produits2'));
    }
        */
    public function store(Request $request){
        /*$employe = auth()->user();*/
        $produit = new Produit();
        /*$produit->producteur = $employe->code_producteur;*/
        $produit->producteur = $request->producteur;
        $produit->nom = $request->nom;
        $produit->prix = $request->prix;
        $produit->quantite = $request->qte;
        $produit->created_at = now();
        $produit->updated_at = null;
       $produit->save();
        return redirect()->route('producteur-produit');
    }

    public function producteur() {
        $produits = Produit::latest()->get();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
       
     return view('pages/producteur/producteur-produit',['produits' => $produits],['heure_actuelle' => $heure_actuelle]);
    }

    public function reserverMp() {
        return view('pages/producteur/producteur-reserverMp');
    }
}