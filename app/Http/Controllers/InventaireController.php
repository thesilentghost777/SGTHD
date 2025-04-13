<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Inventaire;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventaireController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventaire::with(['produit', 'user']);

        // Filtrer par date
        if ($request->filled('date_debut')) {
            $query->whereDate('date_inventaire', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_inventaire', '<=', $request->date_fin);
        }

        $inventaires = $query->latest('date_inventaire')->paginate(20);
        return view('stock.inventaires.index', compact('inventaires'));
    }

    public function nouveau()
    {
        // Récupérer uniquement les produits de type boisson pour l'inventaire
        $produits = Produit::where('type', 'boisson')->orderBy('nom')->get();
        return view('stock.inventaires.nouveau', compact('produits'));
    }

    public function calculerManquants(Request $request)
    {
        $validated = $request->validate([
            'inventaire' => 'required|array',
            'inventaire.*.produit_id' => 'required|exists:produits,id',
            'inventaire.*.quantite_physique' => 'required|integer|min:0'
        ]);

        $resultats = [];
        $dateInventaire = Carbon::now()->format('Y-m-d');

        foreach ($validated['inventaire'] as $item) {
            $produit = Produit::find($item['produit_id']);
            $quantite_theorique = $produit->quantite;
            $quantite_physique = $item['quantite_physique'];
            $difference = $quantite_theorique - $quantite_physique;

            // Si la quantité physique est inférieure à la quantité théorique,
            // il y a un manquant à enregistrer
            if ($difference > 0) {
                $valeur_manquant = $difference * $produit->prix_unitaire;

                // Créer l'enregistrement d'inventaire
                Inventaire::create([
                    'date_inventaire' => $dateInventaire,
                    'produit_id' => $produit->id,
                    'quantite_theorique' => $quantite_theorique,
                    'quantite_physique' => $quantite_physique,
                    'valeur_manquant' => $valeur_manquant,
                    'user_id' => auth()->id()
                ]);

                // Ajouter aux résultats pour l'affichage
                $resultats[] = [
                    'produit' => $produit->nom,
                    'manquant' => $difference,
                    'valeur' => $valeur_manquant
                ];

                // Mettre à jour le stock du produit pour qu'il corresponde à la quantité physique
                $produit->update(['quantite' => $quantite_physique]);
            }
            // Si la quantité physique est supérieure à la quantité théorique,
            // nous mettons à jour le stock sans créer d'entrée d'inventaire avec manquant
            elseif ($difference < 0) {
                // Mettre à jour le stock du produit pour qu'il corresponde à la quantité physique
                $produit->update(['quantite' => $quantite_physique]);

                // Créer un mouvement de stock pour tracer l'ajustement (optionnel)
                $produit->mouvements()->create([
                    'type' => 'entree',
                    'quantite' => abs($difference),
                    'user_id' => auth()->id(),
                    'motif' => 'Ajustement suite à inventaire'
                ]);
            }
        }

        return response()->json($resultats);
    }
}
