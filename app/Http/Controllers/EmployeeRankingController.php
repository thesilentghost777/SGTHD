<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evaluation;
use App\Models\TransactionVente;
use App\Models\ProduitRecu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeRankingController extends Controller
{
    public function index()
    {
        $regularEmployees = $this->getRegularEmployeeRankings();
        $serverRankings = $this->getServerRankings();

        return view('rankings.index', compact('regularEmployees', 'serverRankings'));
    }

    private function getRegularEmployeeRankings()
    {
        return User::whereNotIn('role', ['serveur'])
            ->with(['evaluation' => function($query) {
                $query->whereMonth('created_at', Carbon::now()->month);
            }])
            ->get()
            ->map(function($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'average_rating' => $employee->evaluation->avg('note') ?? 0,
                ];
            })
            ->sortByDesc('average_rating')
            ->values();
    }

    private function getServerRankings()
    {
        $currentMonth = Carbon::now()->month;

        $servers = User::where('role', 'serveur')->get();

        return $servers->map(function($server) use ($currentMonth) {
            // Calculate sales
            $sales = TransactionVente::where('serveur', $server->id)
                ->whereMonth('date_vente', $currentMonth)
                ->sum(DB::raw('quantite * prix'));

            // Calculate average rating
            $avgRating = Evaluation::where('user_id', $server->id)
                ->whereMonth('created_at', $currentMonth)
                ->avg('note') ?? 0;

            // Calculate missing items
            $missing = $this->calculateMissingItems($server->id, $currentMonth);

            return [
                'id' => $server->id,
                'name' => $server->name,
                'sales' => $sales,
                'average_rating' => $avgRating,
                'missing_items' => $missing,
                'total_score' => $this->calculateTotalScore($sales, $avgRating, $missing)
            ];
        })
        ->sortByDesc('total_score')
        ->values();
    }

    private function calculateMissingItems($serverId, $month)
    {
        $totalMissing = 0;

        // Group by date and product to calculate daily missing items
        $dailyProducts = ProduitRecu::where('pointeur', $serverId)
            ->whereMonth('date', $month)
            ->get()
            ->groupBy(['date', 'produit']);

        foreach ($dailyProducts as $date => $products) {
            foreach ($products as $productId => $receivedProducts) {
                $received = $receivedProducts->sum('quantite');

                $sold = TransactionVente::where('serveur', $serverId)
                    ->where('produit', $productId)
                    ->where('date_vente', $date)
                    ->sum('quantite');

                // Note: You'll need to add logic for remaining and damaged products
                // This is a simplified version
                $remaining = 0; // Get from inventory
                $damaged = 0;   // Get from damage reports

                $missing = ($received - $sold + $remaining + $damaged) * $receivedProducts->first()->prix;
                $totalMissing += max(0, $missing);
            }
        }

        return $totalMissing;
    }

    private function calculateTotalScore($sales, $rating, $missing)
    {
        // Customize this formula based on your business rules
        // This is just an example
        $salesScore = $sales / 1000; // Points per 1000 in sales
        $ratingScore = $rating * 10;  // Points per rating point
        $missingPenalty = $missing / 100; // Penalty per 100 in missing items

        return $salesScore + $ratingScore - $missingPenalty;
    }
}
