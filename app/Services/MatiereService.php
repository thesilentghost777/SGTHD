<?php
namespace App\Services;

use App\Models\Matiere;
use App\Enums\UniteMinimale;
use App\Enums\UniteClassique;

class MatiereService
{
    public function calculerPrixParUniteMinimale(float $prix, float $quantite, string $uniteClassique, string $uniteMinimale): float
    {
        if ($quantite <= 0) return 0;

        // Get base unit for the classical unit (ml for litre, g for kg)
        $baseUnit = UniteClassique::getBaseUnit($uniteClassique);

        // First convert to base unit
        $quantiteEnBaseUnit = $quantite;
        if ($uniteClassique === 'litre') {
            $quantiteEnBaseUnit *= 1000; // 1 litre = 1000 ml
        } elseif ($uniteClassique === 'kg') {
            $quantiteEnBaseUnit *= 1000; // 1 kg = 1000 g
        }

        // Then convert from base unit to target minimal unit if needed
        if ($baseUnit !== $uniteMinimale) {
            $tauxConversion = UniteMinimale::getConversionRate($uniteMinimale, $baseUnit) ?: 1;
            $quantiteEnBaseUnit = $quantiteEnBaseUnit / $tauxConversion;
        }

        return $prix / $quantiteEnBaseUnit;
    }
}
