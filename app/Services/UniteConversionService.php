<?php

namespace App\Services;

use App\Enums\UniteMinimale;

class UniteConversionService
{
    private array $conversions = [
        'kg' => ['base' => 'g', 'facteur' => 1000],
        'g' => ['base' => 'g', 'facteur' => 1],
        'l' => ['base' => 'ml', 'facteur' => 1000],
        'dl' => ['base' => 'ml', 'facteur' => 100],
        'cl' => ['base' => 'ml', 'facteur' => 10],
        'ml' => ['base' => 'ml', 'facteur' => 1],
        'cc' => ['base' => 'ml', 'facteur' => 5],
        'cs' => ['base' => 'ml', 'facteur' => 15],
        'pincee' => ['base' => 'g', 'facteur' => 1.5],
        'unite' => ['base' => 'unite', 'facteur' => 1]
    ];

    public function convertir(float $quantite, $uniteSource, $uniteCible): float
    {
        // Convertir les unités en objets d'énumération si ce sont des chaînes
        if (is_string($uniteSource)) {
            $uniteSource = UniteMinimale::from($uniteSource);
        }

        if (is_string($uniteCible)) {
            $uniteCible = UniteMinimale::from($uniteCible);
        }

        // Convertir en chaînes (valeurs des énumérations) pour utilisation dans le tableau de conversion
        $uniteSourceString = $uniteSource->value;
        $uniteCibleString = $uniteCible->value;

        // Vérification si les unités existent dans les conversions
        if (!isset($this->conversions[$uniteSourceString]) || !isset($this->conversions[$uniteCibleString])) {
            throw new \InvalidArgumentException("Les unités spécifiées ne sont pas reconnues.");
        }

        // Si les unités sont identiques, aucune conversion n'est nécessaire
        if ($uniteSourceString === $uniteCibleString) {
            return $quantite;
        }

        // Conversion en unité de base
        $quantiteBase = $quantite * $this->conversions[$uniteSourceString]['facteur'];

        // Vérification de la compatibilité des bases
        if ($this->conversions[$uniteSourceString]['base'] !== $this->conversions[$uniteCibleString]['base']) {
            throw new \InvalidArgumentException("Les unités source et cible ne sont pas compatibles.");
        }

        // Conversion de l'unité de base vers l'unité cible
        return $quantiteBase / $this->conversions[$uniteCibleString]['facteur'];
    }
    public function obtenirConversions(): array
{
    return $this->conversions;
}

}
