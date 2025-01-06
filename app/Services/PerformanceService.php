<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Utilisation;
use Illuminate\Support\Collection;

class PerformanceService
{
    public function calculateMonthlyPerformance(int $employeId): array
    {
        $currentMonth = $this->getMonthStats($employeId, Carbon::now());
        $lastMonth = $this->getMonthStats($employeId, Carbon::now()->subMonth());

        return [
            'evolution' => $this->calculateEvolution($currentMonth, $lastMonth),
            'current_month' => $currentMonth,
            'last_month' => $lastMonth
        ];
    }

    private function getMonthStats(int $employeId, Carbon $date): array
    {
        $productions = Utilisation::where('producteur', $employeId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->get()
            ->groupBy('id_lot');

        return [
            'total_lots' => $productions->count(),
            'total_quantity' => $productions->sum(function ($lot) {
                return $lot->first()->quantite_produit;
            }),
            'average_per_lot' => $productions->avg(function ($lot) {
                return $lot->first()->quantite_produit;
            })
        ];
    }

    private function calculateEvolution(array $current, array $last): array
    {
        return [
            'lots' => $this->calculatePercentageChange($current['total_lots'], $last['total_lots']),
            'quantity' => $this->calculatePercentageChange($current['total_quantity'], $last['total_quantity']),
            'efficiency' => $this->calculatePercentageChange($current['average_per_lot'], $last['average_per_lot'])
        ];
    }

    private function calculatePercentageChange($current, $previous): float
    {
        if ($previous == 0) return 0;
        return (($current - $previous) / $previous) * 100;
    }
}
