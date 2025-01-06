<?php

namespace App\Services;

use App\Models\Utilisation;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ProductionStatsService
{
    /**
     * Récupère les productions par lot pour un utilisateur donné dans une plage de dates.
     *
     * @param int $userId
     * @param Carbon $debut
     * @param Carbon $fin
     * @return Collection
     */
    public function getProductionsByLot(int $userId, Carbon $debut, Carbon $fin): Collection
    {
        return Utilisation::where('producteur', $userId)
            ->whereBetween('created_at', [$debut, $fin])
            ->get()
            ->groupBy('id_lot'); // Groupement par lot
    }

    /**
     * Calcule les statistiques globales des productions par lot.
     *
     * @param Collection $productionsByLot
     * @return array
     */
    public function calculateGlobalStats(Collection $productionsByLot): array
    {
        $globalStats = [
            'total_quantite' => 0,
            'total_revenu' => 0,
            'total_cout' => 0,
            'total_benefice' => 0,
            'lots' => []
        ];

        foreach ($productionsByLot as $idLot => $productions) {
            $lotStats = $this->calculateProductStats($productions);

            $globalStats['total_quantite'] += $lotStats['quantite_totale'];
            $globalStats['total_revenu'] += $lotStats['revenu_total'];
            $globalStats['total_cout'] += $lotStats['cout_total'];
            $globalStats['total_benefice'] += $lotStats['benefice'];
            $globalStats['lots'][$idLot] = $lotStats;
        }

        return $globalStats;
    }

    /**
     * Calcule les statistiques pour un lot donné en regroupant les produits.
     *
     * @param Collection $productions
     * @return array
     */
    private function calculateProductStats(Collection $productions): array
    {
        $statsParProduit = $productions->groupBy('produit')
            ->map(function ($groupedProductions) {
                $produit = $groupedProductions->first()->produitFixe;

                $quantiteTotale = $groupedProductions->sum('quantite_produit');
                $coutTotal = $groupedProductions->sum(function ($production) {
                    $quantiteConvertie = app('UniteConversionService')->convertToMinimalUnit(
                        $production->quantite_matiere,
                        $production->unite_matiere,
                        $production->matiere->unite_minimale
                    );
                    return $quantiteConvertie * $production->matiere->prix_par_unite_minimale;
                });

                $revenuTotal = $quantiteTotale * $produit->prix;
                $benefice = $revenuTotal - $coutTotal;

                return [
                    'nom_produit' => $produit->nom,
                    'code_produit' => $produit->code_produit,
                    'quantite_totale' => $quantiteTotale,
                    'cout_total' => $coutTotal,
                    'revenu_total' => $revenuTotal,
                    'benefice' => $benefice,
                ];
            });

        return [
            'quantite_totale' => $statsParProduit->sum('quantite_totale'),
            'cout_total' => $statsParProduit->sum('cout_total'),
            'revenu_total' => $statsParProduit->sum('revenu_total'),
            'benefice' => $statsParProduit->sum('benefice'),
            'stats_par_produit' => $statsParProduit->values()->toArray(),
        ];
    }
}
