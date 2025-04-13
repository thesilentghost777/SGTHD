<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\AvanceSalaire;
use Illuminate\Support\Facades\DB;

class DgController extends Controller
{
    public function dashboard() {
        return view('pages/dg/dg-dashboard');
    }
    public function rapports() {
        return view('pages/dg/dg_rapports');
    }
    public function index()
    {
        // Mois actuel et mois précédent
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Chiffre d'affaires
        $currentRevenue = Transaction::where('type', 'income')
            ->whereMonth('date', $currentMonth->month)
            ->sum('amount');

        $lastMonthRevenue = Transaction::where('type', 'income')
            ->whereMonth('date', $lastMonth->month)
            ->sum('amount');

        $revenueGrowth = $this->calculateGrowth($currentRevenue, $lastMonthRevenue);

        // Dépenses
        $currentExpenses = Transaction::where('type', 'outcome')
            ->whereMonth('date', $currentMonth->month)
            ->sum('amount');

        $lastMonthExpenses = Transaction::where('type', 'outcome')
            ->whereMonth('date', $lastMonth->month)
            ->sum('amount');

        $expensesGrowth = $this->calculateGrowth($currentExpenses, $lastMonthExpenses);

        // Bénéfice net
        $currentProfit = $currentRevenue - $currentExpenses;
        $lastMonthProfit = $lastMonthRevenue - $lastMonthExpenses;
        $profitGrowth = $this->calculateGrowth($currentProfit, $lastMonthProfit);

        // Effectif total et stabilité
        $currentStaff = DB::table('users')->count();
        $lastMonthStaff = DB::table('users')
            ->where('created_at', '<', $lastMonth->endOfMonth())
            ->count();

        $staffStability = $currentStaff === $lastMonthStaff ? 'Stable' : 'Instable';

        // Données pour le graphique de revenus (12 derniers mois)
        $revenueChart = Transaction::where('type', 'income')
            ->where('date', '>=', Carbon::now()->subMonths(12))
            ->groupBy(DB::raw('MONTH(date)'))
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('MONTH(date) as month')
            )
            ->get();

        // Demandes d'avance de salaire en attente
        $pendingRequests = AvanceSalaire::where('flag', false)
            ->with('employe')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $nom = auth()->user()->name;
        return view('dashboard.index', [
            'revenue' => [
                'current' => $currentRevenue,
                'growth' => $revenueGrowth
            ],
            'profit' => [
                'current' => $currentProfit,
                'growth' => $profitGrowth
            ],
            'expenses' => [
                'current' => $currentExpenses,
                'growth' => $expensesGrowth
            ],
            'staff' => [
                'total' => $currentStaff,
                'stability' => $staffStability,
                'nom' => $nom
            ],
            'revenueChart' => $revenueChart,
            'pendingRequests' => $pendingRequests
        ]);
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return 100;
        return round((($current - $previous) / $previous) * 100, 2);
    }
}
