<?php

namespace App\Services;

use App\Models\User;
use App\Models\Utilisation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Services\ProductionStatsService;
class ProducteurComparisonService
{
    private $productionStatsService;

    public function __construct(ProductionStatsService $productionStatsService)
    {
        $this->productionStatsService = $productionStatsService;
    }

    public function compareProducteurs(string $critere, string $periode, ?string $dateDebut = null, ?string $dateFin = null): Collection
{
    // Modifier cette ligne pour inclure les rôles patissier et boulanger
    $producteurs = User::whereIn('role', ['patissier', 'boulanger'])->get();

    $comparaisons = collect();

    foreach ($producteurs as $producteur) {
        $stats = $this->getProducteurStats($producteur->id, $periode, $dateDebut, $dateFin);
        $comparaisons->push([
            'id' => $producteur->id,
            'nom' => $producteur->name,
            'secteur' => $producteur->secteur,
            'stats' => $stats
        ]);
    }

    return $this->trierParCritere($comparaisons, $critere);
}

    private function getProducteurStats(int $producteurId, string $periode, ?string $dateDebut, ?string $dateFin): array
    {
        $query = Utilisation::with(['produitFixe', 'matiere'])
            ->where('producteur', $producteurId);

        // Appliquer le filtre de période
        switch ($periode) {
            case 'jour':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'semaine':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'mois':
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'personnalise':
                if ($dateDebut && $dateFin) {
                    $query->whereBetween('created_at', [$dateDebut, $dateFin]);
                }
                break;
        }

        $productions = $query->get();

        // Calculer les statistiques
        $quantite_totale = $productions->sum('quantite_produit');
        $cout_total = $this->calculerCoutTotal($productions);
        $revenu_total = $this->calculerRevenuTotal($productions);
        $benefice = $revenu_total - $cout_total;
        $nombre_produits = $productions->groupBy('produit')->count();
        $efficacite = $quantite_totale > 0 ? ($benefice / $quantite_totale) : 0;

        return [
            'quantite_totale' => $quantite_totale,
            'cout_total' => $cout_total,
            'revenu_total' => $revenu_total,
            'benefice' => $benefice,
            'nombre_produits' => $nombre_produits,
            'efficacite' => $efficacite,
            'moyenne_journaliere' => $this->calculerMoyenneJournaliere($productions)
        ];
    }

    private function calculerCoutTotal(Collection $productions): float
    {
        return $productions->sum(function ($production) {
            return $production->quantite_matiere * $production->matiere->prix_par_unite_minimale;
        });
    }

    private function calculerRevenuTotal(Collection $productions): float
    {
        return $productions->sum(function ($production) {
            return $production->quantite_produit * $production->produitFixe->prix;
        });
    }

    private function calculerMoyenneJournaliere(Collection $productions): float
    {
        if ($productions->isEmpty()) return 0;

        $jours_production = $productions->groupBy(function ($production) {
            return $production->created_at->format('Y-m-d');
        })->count();

        return $jours_production > 0 ? $productions->sum('quantite_produit') / $jours_production : 0;
    }

    private function trierParCritere(Collection $comparaisons, string $critere): Collection
    {
        return $comparaisons->sortByDesc(function ($item) use ($critere) {
            switch ($critere) {
                case 'quantite':
                    return $item['stats']['quantite_totale'];
                case 'benefice':
                    return $item['stats']['benefice'];
                case 'efficacite':
                    return $item['stats']['efficacite'];
                case 'diversite':
                    return $item['stats']['nombre_produits'];
                default:
                    return $item['stats']['benefice'];
            }
        })->values();
    }
}
