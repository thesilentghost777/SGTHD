<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionVente;
use App\Models\Produit_fixes;
use Carbon\Carbon;

class VenteApiController extends Controller
{
    /**
     * Liste détaillée des transactions de vente
     */
    public function index()
    {
        try {
            $ventes = DB::table('transaction_ventes as tv')
                ->select('tv.*', 'p.nom as nom_produit', 'u.name as nom_serveur')
                ->leftJoin('Produit_fixes as p', 'tv.produit', '=', 'p.code_produit')
                ->leftJoin('users as u', 'tv.serveur', '=', 'u.id')
                ->orderBy('tv.date_vente', 'desc')
                ->get();
                
            return response()->json([
                'status' => 'success',
                'data' => $ventes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Comparaison des vendeurs pour le mois en cours
     */
    public function compareVendeurs()
    {
        try {
            // Définir le début et la fin du mois courant
            $dateDebut = Carbon::now()->startOfMonth()->toDateString();
            $dateFin = Carbon::now()->endOfMonth()->toDateString();
            
            // Récupérer les statistiques des vendeurs pour le mois courant
            $statsVendeurs = DB::table('transaction_ventes as tv')
                ->select(
                    'u.id as serveur_id',
                    'u.name as nom_serveur',
                    DB::raw('SUM(CASE WHEN tv.type = "Vente" THEN tv.quantite ELSE 0 END) as total_ventes'),
                    DB::raw('SUM(CASE WHEN tv.type = "Vente" THEN tv.quantite * tv.prix ELSE 0 END) as benefice'),
                    DB::raw('SUM(CASE WHEN tv.type = "Produit invendu" THEN tv.quantite ELSE 0 END) as total_invendus'),
                    DB::raw('SUM(CASE WHEN tv.type = "Produit Avarie" THEN tv.quantite ELSE 0 END) as total_avaries')
                )
                ->leftJoin('users as u', 'tv.serveur', '=', 'u.id')
                ->whereBetween('tv.date_vente', [$dateDebut, $dateFin])
                ->groupBy('u.id', 'u.name')
                ->get();
                
            // Préparer les données pour les graphiques
            $chartData = [
                'labels' => $statsVendeurs->pluck('nom_serveur')->toArray(),
                'dataVentes' => $statsVendeurs->pluck('total_ventes')->toArray(),
                'dataBenefices' => $statsVendeurs->pluck('benefice')->toArray(),
                'dataInvendus' => $statsVendeurs->pluck('total_invendus')->toArray(),
                'dataAvaries' => $statsVendeurs->pluck('total_avaries')->toArray()
            ];
            
            $moisActuel = Carbon::now()->locale('fr')->isoFormat('MMMM YYYY');
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'statsVendeurs' => $statsVendeurs,
                    'chartData' => $chartData,
                    'moisActuel' => $moisActuel
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Liste tous les produits disponibles
     */
    public function getProduits()
    {
        try {
            $produits = Produit_fixes::with('stock')
                ->orderBy('nom')
                ->get();
                
            return response()->json([
                'status' => 'success',
                'data' => $produits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
