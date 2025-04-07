<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VenteController extends Controller
{
    /**
     * Affiche la liste détaillée de toutes les opérations de vente
     */
    public function index()
    {
        $ventes = DB::table('transaction_ventes as tv')
            ->select('tv.*', 'p.nom as nom_produit', 'u.name as nom_serveur')
            ->leftJoin('Produit_fixes as p', 'tv.produit', '=', 'p.code_produit')
            ->leftJoin('users as u', 'tv.serveur', '=', 'u.id')
            ->orderBy('tv.date_vente', 'desc')
            ->get();

        return view('ventes.index', compact('ventes'));
    }

    /**
     * Affiche la comparaison des vendeurs
     */
    public function compareVendeurs()
    {
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
        $labels = $statsVendeurs->pluck('nom_serveur')->toArray();
        $dataVentes = $statsVendeurs->pluck('total_ventes')->toArray();
        $dataBenefices = $statsVendeurs->pluck('benefice')->toArray();
        $dataInvendus = $statsVendeurs->pluck('total_invendus')->toArray();
        $dataAvaries = $statsVendeurs->pluck('total_avaries')->toArray();

        // Données pour les graphiques
        $chartData = [
            'labels' => $labels,
            'dataVentes' => $dataVentes,
            'dataBenefices' => $dataBenefices,
            'dataInvendus' => $dataInvendus,
            'dataAvaries' => $dataAvaries
        ];

        $moisActuel = Carbon::now()->locale('fr')->isoFormat('MMMM YYYY');

        return view('ventes.compare', compact('statsVendeurs', 'chartData', 'moisActuel'));
    }
}
