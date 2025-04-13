<?php

namespace App\Http\Controllers;

use App\Models\AvanceSalaire;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvanceSalaireController extends Controller
{
    /**
     * Affiche le tableau de bord des avances sur salaire
     */
    public function dashboard()
    {
        // Statistiques du mois courant
        $currentMonth = Carbon::now()->format('Y-m');

        // Nombre total de demandes pour le mois courant
        $totalDemandes = AvanceSalaire::whereYear('mois_as', Carbon::now()->year)
                        ->whereMonth('mois_as', Carbon::now()->month)
                        ->count();

        // Montant total des demandes pour le mois courant
        $montantTotal = AvanceSalaire::whereYear('mois_as', Carbon::now()->year)
                        ->whereMonth('mois_as', Carbon::now()->month)
                        ->sum('sommeAs');

        // Nombre de demandes en attente
        $demandesEnAttente = AvanceSalaire::where('retrait_demande', true)
                            ->where('retrait_valide', false)
                            ->whereYear('mois_as', Carbon::now()->year)
                            ->whereMonth('mois_as', Carbon::now()->month)
                            ->count();

        // Montant des demandes en attente
        $montantEnAttente = AvanceSalaire::where('retrait_demande', true)
                            ->where('retrait_valide', false)
                            ->whereYear('mois_as', Carbon::now()->year)
                            ->whereMonth('mois_as', Carbon::now()->month)
                            ->sum('sommeAs');

        // Demandes validées
        $demandesValidees = AvanceSalaire::where('retrait_valide', true)
                            ->whereYear('mois_as', Carbon::now()->year)
                            ->whereMonth('mois_as', Carbon::now()->month)
                            ->count();

        // Montant des demandes validées
        $montantValide = AvanceSalaire::where('retrait_valide', true)
                        ->whereYear('mois_as', Carbon::now()->year)
                        ->whereMonth('mois_as', Carbon::now()->month)
                        ->sum('sommeAs');

        // Liste des demandes d'avance du mois courant avec le nom de l'employé
        $avances = AvanceSalaire::select(
                'avance_salaires.*',
                'users.name as employe_nom',
                'users.email as employe_email'
            )
            ->join('users', 'avance_salaires.id_employe', '=', 'users.id')
            ->whereYear('mois_as', Carbon::now()->year)
            ->whereMonth('mois_as', Carbon::now()->month)
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistiques par mois (pour le graphique)
        $statistiquesMensuelles = AvanceSalaire::select(
                DB::raw('DATE_FORMAT(mois_as, "%Y-%m") as mois'),
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(sommeAs) as montant')
            )
            ->where('mois_as', '>=', Carbon::now()->subMonths(6))
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        return view('avance-salaires.dashboard', compact(
            'totalDemandes',
            'montantTotal',
            'demandesEnAttente',
            'montantEnAttente',
            'demandesValidees',
            'montantValide',
            'avances',
            'statistiquesMensuelles',
            'currentMonth'
        ));
    }

    /**
     * API: Obtenir les statistiques pour les avances sur salaire
     */
    public function getStats()
    {
        // Statistiques du mois courant
        $currentMonth = Carbon::now()->format('Y-m');

        $stats = [
            'totalDemandes' => AvanceSalaire::whereYear('mois_as', Carbon::now()->year)
                                ->whereMonth('mois_as', Carbon::now()->month)
                                ->count(),

            'montantTotal' => AvanceSalaire::whereYear('mois_as', Carbon::now()->year)
                            ->whereMonth('mois_as', Carbon::now()->month)
                            ->sum('sommeAs'),

            'demandesEnAttente' => AvanceSalaire::where('retrait_demande', true)
                                ->where('retrait_valide', false)
                                ->whereYear('mois_as', Carbon::now()->year)
                                ->whereMonth('mois_as', Carbon::now()->month)
                                ->count(),

            'montantEnAttente' => AvanceSalaire::where('retrait_demande', true)
                                ->where('retrait_valide', false)
                                ->whereYear('mois_as', Carbon::now()->year)
                                ->whereMonth('mois_as', Carbon::now()->month)
                                ->sum('sommeAs'),

            'demandesValidees' => AvanceSalaire::where('retrait_valide', true)
                                ->whereYear('mois_as', Carbon::now()->year)
                                ->whereMonth('mois_as', Carbon::now()->month)
                                ->count(),

            'montantValide' => AvanceSalaire::where('retrait_valide', true)
                            ->whereYear('mois_as', Carbon::now()->year)
                            ->whereMonth('mois_as', Carbon::now()->month)
                            ->sum('sommeAs'),
        ];

        return response()->json($stats);
    }
}
