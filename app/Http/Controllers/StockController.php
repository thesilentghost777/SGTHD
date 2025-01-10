<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\ProduitRecu;
use App\Models\ProduitFixe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockController extends Controller
{
    public function index()
    {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login');
        }
        $nom = $employe->name;
        $role = $employe->role;
        // Récupérer les stocks
        $matieres = Matiere::orderBy('quantite', 'desc')->get();
        $produits = ProduitRecu::select(
            'produit',
            'nom',
            DB::raw('SUM(quantite) as quantite_totale'),
            'prix'
        )
        ->groupBy('produit', 'nom', 'prix')
        ->orderBy('quantite_totale', 'desc')
        ->get();

        // Statistiques matières premières
        $matiere_max = $matieres->first();
        $total_matieres = $matieres->count();
        $valeur_stock_matieres = $matieres->sum(function($matiere) {
            return $matiere->quantite * $matiere->prix_unitaire;
        });

        // Statistiques produits
        $produit_max = $produits->first();
        $total_produits = $produits->count();
        $valeur_stock_produits = $produits->sum(function($produit) {
            return $produit->quantite_totale * $produit->prix;
        });

        // Données pour les graphiques
        $data_matieres = [
            'labels' => $matieres->pluck('nom'),
            'data' => $matieres->pluck('quantite'),
        ];

        $data_produits = [
            'labels' => $produits->pluck('nom'),
            'data' => $produits->pluck('quantite_totale'),
        ];

        return view('stock.index', compact(
            'matieres',
            'produits',
            'matiere_max',
            'produit_max',
            'total_matieres',
            'total_produits',
            'valeur_stock_matieres',
            'valeur_stock_produits',
            'data_matieres',
            'data_produits',
            'nom',
            'role'
        ));
    }

    public function searchMatiere(Request $request)
    {
        $query = $request->get('query');
        $matieres = Matiere::where('nom', 'LIKE', "%{$query}%")->get();
        return response()->json($matieres);
    }

    public function searchProduit(Request $request)
    {
        $query = $request->get('query');
        $produits = ProduitRecu::where('nom', 'LIKE', "%{$query}%")
            ->select('produit', 'nom', DB::raw('SUM(quantite) as quantite_totale'), 'prix')
            ->groupBy('produit', 'nom', 'prix')
            ->get();
        return response()->json($produits);
    }

    public function updateMatiere(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'quantite' => 'required|numeric|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'quantite_par_unite' => 'required|numeric|min:0'
        ]);

        $matiere->update([
            'quantite' => $validated['quantite'],
            'prix_unitaire' => $validated['prix_unitaire'],
            'quantite_par_unite' => $validated['quantite_par_unite'],
            'prix_par_unite_minimale' => $validated['prix_unitaire'] / $validated['quantite_par_unite']
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteMatiere(Matiere $matiere)
    {
        $matiere->delete();
        return response()->json(['success' => true]);
    }

    public function deleteProduit($produit)
    {
        ProduitRecu::where('produit', $produit)->delete();
        return response()->json(['success' => true]);
    }

    public function adjustMatiereQuantity(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'quantite' => 'required|numeric',
            'operation' => 'required|in:add,subtract'
        ]);

        $newQuantity = $validated['operation'] === 'add'
            ? $matiere->quantite + $validated['quantite']
            : $matiere->quantite - $validated['quantite'];

        if ($newQuantity < 0) {
            return response()->json([
                'success' => false,
                'message' => 'La quantité ne peut pas être négative'
            ], 422);
        }

        $matiere->update(['quantite' => $newQuantity]);
        return response()->json(['success' => true]);
    }

    public function adjustProduitQuantity(Request $request, $produit)
    {
        $validated = $request->validate([
            'quantite' => 'required|numeric',
            'operation' => 'required|in:add,subtract'
        ]);

        $currentQuantity = ProduitRecu::where('produit', $produit)
            ->sum('quantite');

        $newQuantity = $validated['operation'] === 'add'
            ? $currentQuantity + $validated['quantite']
            : $currentQuantity - $validated['quantite'];

        if ($newQuantity < 0) {
            return response()->json([
                'success' => false,
                'message' => 'La quantité ne peut pas être négative'
            ], 422);
        }

        ProduitRecu::where('produit', $produit)
            ->update(['quantite' => $newQuantity]);

        return response()->json(['success' => true]);
    }
    public function updateProduit(Request $request, $produit)
    {
        $validated = $request->validate([
            'quantite' => 'required|numeric|min:0',
            'prix' => 'required|numeric|min:0'
        ]);

        ProduitRecu::where('produit', $produit)->update([
            'quantite' => $validated['quantite'],
            'prix' => $validated['prix']
        ]);

        return response()->json(['success' => true]);
    }
    public function getProduit($produit)
{
    $produit = ProduitRecu::where('produit', $produit)
        ->select(
            'produit',
            'nom',
            DB::raw('SUM(quantite) as quantite_totale'),
            'prix'
        )
        ->groupBy('produit', 'nom', 'prix')
        ->first();

    if (!$produit) {
        return response()->json(['error' => 'Produit non trouvé'], 404);
    }

    return response()->json($produit);
}
}
