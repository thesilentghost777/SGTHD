<?php

namespace App\Services;

use App\Models\Utilisation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdvancedProductionStatsService
{
    /**
     * Vérifie si l'utilisateur est autorisé à accéder au lot
     */
    private function verifyLotAccess(string $lotId, int $userId): bool
    {
        return Utilisation::where('id_lot', $lotId)
            ->where('producteur', $userId)
            ->exists();
    }

    /**
     * Get comprehensive production statistics
     */
    public function getStats($userId)
    {
        return [
            'daily' => $this->getDailyStats($userId),
            'monthly' => $this->getMonthlyStats($userId),
            'yearly' => $this->getYearlyStats($userId),
            'products' => $this->getDetailedProductStats($userId)
        ];
    }

    /**
     * Get detailed product statistics including materials
     */
    private function getDetailedProductStats($userId): Collection
    {
        // Première étape : identifier les productions uniques avec sécurité
        $uniqueProductions = Utilisation::select(
            'produit',
            'producteur',
            'id_lot',
            DB::raw('DATE(created_at) as production_date'),
            'quantite_produit'
        )
        ->where('producteur', $userId)
        ->whereIn('id_lot', function($query) use ($userId) {
            $query->select('id_lot')
                  ->from('Utilisation')
                  ->where('producteur', $userId)
                  ->distinct();
        })
        ->groupBy('produit', 'producteur', 'id_lot', DB::raw('DATE(created_at)'), 'quantite_produit')
        ->get();

        return $uniqueProductions
            ->groupBy('produit')
            ->map(function ($productions) use ($userId) {
                $firstProduction = $productions->first();

                // Sécurise l'accès aux données du produit
                $produit = Utilisation::where('produit', $firstProduction->produit)
                    ->where('producteur', $userId)
                    ->first()
                    ->produitFixe;

                $totalQuantity = $productions->sum('quantite_produit');
                $revenue = $totalQuantity * $produit->prix;

                // Récupérer les matières premières de manière sécurisée
                $materials = collect();
                foreach ($productions as $production) {
                    if ($this->verifyLotAccess($production->id_lot, $userId)) {
                        $productionMaterials = Utilisation::where([
                            'produit' => $production->produit,
                            'producteur' => $userId,
                            'id_lot' => $production->id_lot,
                            'quantite_produit' => $production->quantite_produit
                        ])
                        ->whereDate('created_at', $production->production_date)
                        ->with('matiere')
                        ->get();

                        foreach ($productionMaterials as $material) {
                            $existingMaterial = $materials->firstWhere('matiere_id', $material->matierep);

                            if ($existingMaterial) {
                                $existingMaterial['quantite_totale'] += $material->quantite_matiere;
                                $existingMaterial['cout_total'] += $material->quantite_matiere * $material->matiere->prix_par_unite_minimale;
                            } else {
                                $materials->push([
                                    'matiere_id' => $material->matierep,
                                    'nom' => $material->matiere->nom,
                                    'quantite_totale' => $material->quantite_matiere,
                                    'unite' => $material->matiere->unite_minimale,
                                    'cout_unitaire' => $material->matiere->prix_par_unite_minimale,
                                    'cout_total' => $material->quantite_matiere * $material->matiere->prix_par_unite_minimale
                                ]);
                            }
                        }
                    }
                }

                $totalCost = $materials->sum('cout_total');

                return [
                    'produit' => [
                        'nom' => $produit->nom,
                        'prix_unitaire' => $produit->prix,
                        'quantite_totale' => $totalQuantity,
                        'nombre_productions' => $productions->count(),
                        'revenu_total' => $revenue
                    ],
                    'matieres_premieres' => $materials->values(),
                    'cout_total_mp' => $totalCost,
                    'benefice' => $revenue - $totalCost,
                    'marge' => $revenue > 0 ? (($revenue - $totalCost) / $revenue) * 100 : 0
                ];
            });
    }

    /**
     * Get daily production statistics
     */
    private function getDailyStats($userId): array
    {
        $days = collect(range(0, 6))->map(function ($day) {
            return Carbon::now()->subDays($day)->format('Y-m-d');
        });

        $stats = Utilisation::select(
                'produit',
                'id_lot',
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(DISTINCT CONCAT(produit, id_lot, DATE(created_at))) as productions'),
                DB::raw('SUM(DISTINCT quantite_produit) as total_quantity')
            )
            ->where('producteur', $userId)
            ->whereIn(DB::raw('DATE(created_at)'), $days)
            ->whereIn('id_lot', function($query) use ($userId) {
                $query->select('id_lot')
                      ->from('Utilisation')
                      ->where('producteur', $userId)
                      ->distinct();
            })
            ->groupBy('produit', 'id_lot', DB::raw('DATE(created_at)'))
            ->get()
            ->groupBy('date');

        return [
            'labels' => $days->map(fn($day) => Carbon::parse($day)->format('d/m')),
            'quantities' => $days->map(function ($day) use ($stats) {
                return $stats->get($day, collect())->sum('total_quantity');
            })
        ];
    }

    /**
     * Get monthly production statistics
     */
    private function getMonthlyStats($userId): array
    {
        $months = collect(range(0, 11))->map(function ($month) {
            return Carbon::now()->subMonths($month)->format('Y-m');
        });

        $stats = Utilisation::select(
                'produit',
                'id_lot',
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(DISTINCT CONCAT(produit, id_lot, DATE(created_at))) as productions'),
                DB::raw('SUM(DISTINCT quantite_produit) as total_quantity')
            )
            ->where('producteur', $userId)
            ->whereYear('created_at', '>=', Carbon::now()->subYear())
            ->whereIn('id_lot', function($query) use ($userId) {
                $query->select('id_lot')
                      ->from('Utilisation')
                      ->where('producteur', $userId)
                      ->distinct();
            })
            ->groupBy('produit', 'id_lot', DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->get()
            ->groupBy('month');

        return [
            'labels' => $months->map(fn($month) => Carbon::parse($month)->format('M Y')),
            'quantities' => $months->map(function ($month) use ($stats) {
                return $stats->get($month, collect())->sum('total_quantity');
            })
        ];
    }

    /**
     * Get yearly production statistics
     */
    private function getYearlyStats($userId): array
    {
        $years = collect(range(0, 4))->map(function ($year) {
            return Carbon::now()->subYears($year)->format('Y');
        });

        $stats = Utilisation::select(
                'produit',
                'id_lot',
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(DISTINCT CONCAT(produit, id_lot, DATE(created_at))) as productions'),
                DB::raw('SUM(DISTINCT quantite_produit) as total_quantity')
            )
            ->where('producteur', $userId)
            ->whereYear('created_at', '>=', Carbon::now()->subYears(4))
            ->whereIn('id_lot', function($query) use ($userId) {
                $query->select('id_lot')
                      ->from('Utilisation')
                      ->where('producteur', $userId)
                      ->distinct();
            })
            ->groupBy('produit', 'id_lot', DB::raw('YEAR(created_at)'))
            ->get()
            ->groupBy('year');

        return [
            'labels' => $years,
            'quantities' => $years->map(function ($year) use ($stats) {
                return $stats->get($year, collect())->sum('total_quantity');
            })
        ];
    }
}
