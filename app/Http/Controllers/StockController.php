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

        // Récupérer les matières premières
        $matieres = Matiere::where('nom', 'not like', 'Taule%')
            ->orderBy('nom')
            ->get();

        // Récupérer les stocks de produits
        $produits = DB::table('produit_stocks')
            ->select(
                'Produit_fixes.code_produit',
                'Produit_fixes.nom',
                'Produit_fixes.categorie',
                'produit_stocks.quantite_en_stock as quantite_totale',
                'produit_stocks.quantite_invendu',
                'produit_stocks.quantite_avarie',
                DB::raw('(SELECT prix FROM Produit_recu WHERE produit = Produit_fixes.code_produit ORDER BY date DESC LIMIT 1) as prix_recent')
            )
            ->join('Produit_fixes', 'produit_stocks.id_produit', '=', 'Produit_fixes.code_produit')
            ->orderBy('quantite_en_stock', 'desc')
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
            return $produit->quantite_totale * ($produit->prix_recent ?? 0);
        });

        // Préparation des données pour les graphiques avec limitation aux top 10
        $produits_for_chart = $produits->take(10);

        $data_matieres = [
            'labels' => $matieres->take(10)->pluck('nom'),
            'data' => $matieres->take(10)->pluck('quantite'),
        ];

        $data_produits = [
            'labels' => $produits_for_chart->pluck('nom'),
            'data' => $produits_for_chart->pluck('quantite_totale'),
        ];

        // Ajout des statistiques supplémentaires
        $stats = [
            'produits_par_categorie' => $produits->groupBy('categorie')
                ->map(function($group) {
                    return [
                        'quantite_totale' => $group->sum('quantite_totale'),
                        'valeur_totale' => $group->sum(function($item) {
                            return $item->quantite_totale * ($item->prix_recent ?? 0);
                        }),
                        'nombre_produits' => $group->count()
                    ];
                }),
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
            'role',
            'stats'
        ));
    }

    public function adjustProduitQuantity(Request $request, $produit)
    {
        $validated = $request->validate([
            'quantite' => 'required|numeric',
            'operation' => 'required|in:add,subtract'
        ]);

        // Récupérer ou créer le stock du produit
        $stock = ProduitStock::firstOrCreate(
            ['id_produit' => $produit],
            ['quantite_en_stock' => 0, 'quantite_invendu' => 0, 'quantite_avarie' => 0]
        );

        // Calculer la nouvelle quantité
        $adjustmentQuantity = $validated['quantite'];
        $newQuantity = $validated['operation'] === 'add'
            ? $stock->quantite_en_stock + $adjustmentQuantity
            : $stock->quantite_en_stock - $adjustmentQuantity;

        if ($newQuantity < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuffisant pour cette opération'
            ], 422);
        }

        // Mettre à jour le stock
        $stock->update(['quantite_en_stock' => $newQuantity]);

        // Enregistrer le mouvement dans ProduitRecu pour l'historique
        ProduitRecu::create([
            'produit' => $produit,
            'nom' => ProduitFixe::where('code_produit', $produit)->value('nom'),
            'quantite' => $validated['operation'] === 'add' ? $adjustmentQuantity : -$adjustmentQuantity,
            'date' => now(),
            'prix' => ProduitRecu::where('produit', $produit)
                ->orderBy('date', 'desc')
                ->value('prix') ?? 0
        ]);

        return response()->json(['success' => true]);
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
