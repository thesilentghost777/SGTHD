<?php

namespace App\Http\Controllers;

use App\Models\Ration;
use App\Models\EmployeeRation;
use App\Models\RationClaim;
use App\Models\Horaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RationController extends Controller
{
    // Admin functions
    public function index()
    {
        $ration = Ration::first();
        $employees = User::all();
        $employeeRations = EmployeeRation::with('employee')->get();

        return view('rations.admin.index', compact('ration', 'employees', 'employeeRations'));
    }

    public function updateDefaultRation(Request $request)
    {
        $request->validate([
            'montant_defaut' => 'required|numeric|min:0',
        ]);

        $ration = Ration::first() ?? new Ration();
        $ration->montant_defaut = $request->montant_defaut;
        $ration->save();

        // Mettre à jour les employés qui n'ont pas de ration personnalisée
        EmployeeRation::where('personnalise', false)
            ->update(['montant' => $request->montant_defaut]);

        return redirect()->route('rations.admin.index')->with('success', 'Montant de ration par défaut mis à jour avec succès');
    }

    public function updateEmployeeRation(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'montant' => 'required|numeric|min:0',
            'personnalise' => 'sometimes|boolean',
        ]);

        $personnalise = $request->has('personnalise');

        $employeeRation = EmployeeRation::firstOrNew(['employee_id' => $request->employee_id]);
        $employeeRation->montant = $request->montant;
        $employeeRation->personnalise = $personnalise;
        $employeeRation->save();

        return redirect()->route('rations.admin.index')->with('success', 'Ration de l\'employé mise à jour avec succès');
    }

    public function statistics()
    {
        // Statistiques générales
        $statistiques = [
            'total_rations' => EmployeeRation::sum('montant'),
            'nb_employes_avec_ration' => EmployeeRation::count(),
            'ration_moyenne' => EmployeeRation::avg('montant'),
            'ration_min' => EmployeeRation::min('montant'),
            'ration_max' => EmployeeRation::max('montant'),
            'nb_rations_personnalisees' => EmployeeRation::where('personnalise', true)->count(),
        ];

        // Statistiques des réclamations
        $today = Carbon::today();
        $currentMonth = Carbon::now()->startOfMonth();

        $rationsJour = RationClaim::whereDate('date_reclamation', $today)->sum('montant');
        $rationsMois = RationClaim::whereMonth('date_reclamation', $today->month)
            ->whereYear('date_reclamation', $today->year)
            ->sum('montant');

        // Calculer les rations non réclamées
        $employesPresentsAujourdhui = Horaire::whereDate('arrive', $today)
            ->whereNotNull('arrive')
            ->pluck('employe')
            ->unique();

        $rationsRecuperees = RationClaim::whereDate('date_reclamation', $today)
            ->pluck('employee_id')
            ->unique();

        $employesSansRation = $employesPresentsAujourdhui->diff($rationsRecuperees);

        $rationPerdue = 0;
        foreach ($employesSansRation as $employeId) {
            $rationEmploye = EmployeeRation::where('employee_id', $employeId)->first();
            $rationPerdue += $rationEmploye ? $rationEmploye->montant : 0;
        }

        // Statistiques par jour pour le mois en cours
        $statistiquesJournalieres = RationClaim::select(
                DB::raw('date_reclamation as date'),
                DB::raw('COUNT(*) as nombre'),
                DB::raw('SUM(montant) as montant_total')
            )
            ->whereMonth('date_reclamation', $today->month)
            ->whereYear('date_reclamation', $today->year)
            ->groupBy('date_reclamation')
            ->orderBy('date_reclamation')
            ->get();

        // Trouver les employés qui prennent rarement leur ration
        $employesRarement = User::withCount(['rationClaims' => function($query) {
                $query->whereMonth('date_reclamation', Carbon::now()->month);
            }])
            ->orderBy('ration_claims_count')
            ->limit(5)
            ->get();

        return view('rations.admin.statistics', compact(
            'statistiques',
            'rationsJour',
            'rationsMois',
            'rationPerdue',
            'statistiquesJournalieres',
            'employesRarement'
        ));
    }

    // Employee functions
    public function claimForm()
    {
        $employee = Auth::user();
        $today = Carbon::today();

        // Vérifier si l'employé est présent aujourd'hui
        $horaire = Horaire::where('employe', $employee->id)
            ->whereDate('arrive', $today)
            ->whereNotNull('arrive')
            ->first();

        $canClaimRation = $horaire !== null;

        // Vérifier si l'employé a déjà réclamé sa ration aujourd'hui
        $alreadyClaimed = RationClaim::where('employee_id', $employee->id)
            ->whereDate('date_reclamation', $today)
            ->exists();

        // Obtenir la ration de l'employé
        $ration = EmployeeRation::where('employee_id', $employee->id)->first();
        $montantRation = $ration ? $ration->montant : 0;

        if (!$ration) {
            // Si aucune ration spécifique n'est définie, utiliser la ration par défaut
            $rationDefaut = Ration::first();
            $montantRation = $rationDefaut ? $rationDefaut->montant_defaut : 0;
        }

        // Historique des réclamations
        $historique = RationClaim::where('employee_id', $employee->id)
            ->orderBy('date_reclamation', 'desc')
            ->take(30)
            ->get();

        return view('rations.employee.claim', compact(
            'employee',
            'canClaimRation',
            'alreadyClaimed',
            'montantRation',
            'historique'
        ));
    }

    public function claim(Request $request)
    {
        $employee = Auth::user();
        $today = Carbon::today();

        // Vérifier si l'employé est présent aujourd'hui
        $horaire = Horaire::where('employe', $employee->id)
            ->whereDate('arrive', $today)
            ->whereNotNull('arrive')
            ->first();

        if (!$horaire) {
            return redirect()->route('employee.claim')->with('error', 'Vous n\'êtes pas marqué comme présent aujourd\'hui');
        }

        // Vérifier si l'employé a déjà réclamé sa ration aujourd'hui
        $alreadyClaimed = RationClaim::where('employee_id', $employee->id)
            ->whereDate('date_reclamation', $today)
            ->exists();

        if ($alreadyClaimed) {
            return redirect()->route('employee.claim')->with('error', 'Vous avez déjà réclamé votre ration pour aujourd\'hui');
        }

        // Obtenir la ration de l'employé
        $ration = EmployeeRation::where('employee_id', $employee->id)->first();
        $montantRation = $ration ? $ration->montant : 0;

        if (!$ration) {
            // Si aucune ration spécifique n'est définie, utiliser la ration par défaut
            $rationDefaut = Ration::first();
            $montantRation = $rationDefaut ? $rationDefaut->montant_defaut : 0;
        }

        // Créer la réclamation
        RationClaim::create([
            'employee_id' => $employee->id,
            'date_reclamation' => $today,
            'montant' => $montantRation,
            'heure_reclamation' => Carbon::now()
        ]);

        return redirect()->route('employee.claim')->with('success', 'Ration réclamée avec succès');
    }
}
