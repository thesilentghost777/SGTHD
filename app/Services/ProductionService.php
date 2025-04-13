<?php

namespace App\Services;

use App\Models\Utilisation;
use App\Models\Produit_fixes;
use App\Models\Daily_assignments;
use App\Models\Production_suggerer_par_jour;
use App\Models\MatiereRecommander;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    public function getTodayProductions(int $employeId): Collection
    {
        $productIds = $this->getTodayProductIds($employeId);
        $produits = Produit_fixes::whereIn('code_produit', $productIds)->get();
        $utilisations = $this->getTodayUtilisations($employeId, $productIds);

        return $this->buildProductionsCollection($produits, $utilisations);
    }

    public function getExpectedProductions(int $employeId): Collection
    {
        $assignments = Daily_assignments::where('producteur', $employeId)
        ->whereDate('assignment_date', now())
        ->get();
        return $this->buildExpectedProductionsCollection($assignments, $employeId);
    }

    public function getRecommendedProductions(): Collection
    {
        $jour_actuel = strtolower(Carbon::now()->locale('fr')->dayName);
        $suggestions = Production_suggerer_par_jour::where('day', $jour_actuel)->get();

        return $this->buildRecommendedProductionsCollection($suggestions);
    }

    private function getTodayProductIds(int $employeId): array
    {
        return Utilisation::where('producteur', $employeId)
            ->whereDate('created_at', Carbon::now()->toDateString())
            ->select('produit')
            ->groupBy('produit')
            ->pluck('produit')
            ->toArray();
    }

    private function getTodayUtilisations(int $employeId, array $productIds): Collection
    {
        return Utilisation::with('matiere')
            ->select(
                'produit',
                'matierep',
                'producteur',
                'quantite_produit',
                'quantite_matiere',
                'unite_matiere',
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:%s") as production_timestamp')
            )
            ->whereIn('produit', $productIds)
            ->where('producteur', $employeId)
            ->whereDate('created_at', Carbon::now()->toDateString())
            ->get();
    }

    private function buildProductionsCollection(Collection $produits, Collection $utilisations): Collection
    {
        $productions = collect();

        foreach ($produits as $produit) {
            $productUtilisations = $utilisations->where('produit', $produit->code_produit);

            // Identifier les productions uniques par timestamp
            $uniqueProductions = $productUtilisations
                ->groupBy('production_timestamp')
                ->map(function ($group) {
                    return [
                        'quantite' => $group->first()->quantite_produit,
                        'matieres' => $group
                    ];
                });

            $quantite_totale = $uniqueProductions->sum('quantite');

            if ($quantite_totale > 0) {
                // Agréger les matières premières de toutes les productions
                $matieres_utilisees = [];

                foreach ($uniqueProductions as $production) {
                    foreach ($production['matieres'] as $utilisation) {
                        $matiere_key = $utilisation->matierep;

                        if (!isset($matieres_utilisees[$matiere_key])) {
                            $matieres_utilisees[$matiere_key] = [
                                'nom' => $utilisation->matiere->nom,
                                'quantite' => 0,
                                'unite' => $utilisation->unite_matiere
                            ];
                        }

                        $matieres_utilisees[$matiere_key]['quantite'] += $utilisation->quantite_matiere;
                    }
                }

                $productions->push([
                    'nom' => $produit->nom,
                    'prix' => $produit->prix,
                    'quantite' => $quantite_totale,
                    'nombre_productions' => $uniqueProductions->count(),
                    'matieres_premieres' => collect($matieres_utilisees)->values()
                ]);
            }
        }

        return $productions;
    }

    private function buildExpectedProductionsCollection(Collection $assignments, int $employeId): Collection
    {
        $productions = collect();

        foreach ($assignments as $assignment) {
            $produit = Produit_fixes::where('code_produit', $assignment->produit)->first();
            if ($produit) {
                $productionStats = $this->getTodayProductionStats($employeId, $assignment->produit);
                $quantite_produite = $productionStats['quantite_totale'];

                if ($quantite_produite >= $assignment->expected_quantity) {
                    $assignment->status = 1;
                    $assignment->save();
                }

                $productions->push([
                    'nom' => $produit->nom,
                    'quantite_attendue' => $assignment->expected_quantity,
                    'quantite_produite' => $quantite_produite,
                    'nombre_productions' => $productionStats['nombre_productions'],
                    'prix' => $produit->prix,
                    'status' => $assignment->status == 1 ? "Terminé" : "En attente",
                    'progression' => min(($quantite_produite / $assignment->expected_quantity) * 100, 100)
                ]);
            }
        }

        return $productions;
    }

    private function buildRecommendedProductionsCollection(Collection $suggestions): Collection
    {
        return $suggestions->map(function ($suggestion) {
            $produit = Produit_fixes::where('code_produit', $suggestion->produit)->first();
            if ($produit) {
                return [
                    'nom' => $produit->nom,
                    'quantite_recommandee' => $suggestion->quantity,
                    'prix' => $produit->prix
                ];
            }
        })->filter();
    }

    private function getTodayProductionStats(int $employeId, string $produitId): array
    {
        $productions = Utilisation::where('producteur', $employeId)
            ->where('produit', $produitId)
            ->whereDate('created_at', Carbon::now()->toDateString())
            ->select(
                'quantite_produit',
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:%s") as production_timestamp')
            )
            ->get()
            ->groupBy('production_timestamp');

        return [
            'quantite_totale' => $productions->sum(function ($group) {
                return $group->first()->quantite_produit;
            }),
            'nombre_productions' => $productions->count()
        ];
    }
}
