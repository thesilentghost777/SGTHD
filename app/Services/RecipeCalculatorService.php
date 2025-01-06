<?php

namespace App\Services;

class RecipeCalculatorService
{
    public function calculateIngredientsForQuantity(float $baseQuantity, float $targetQuantity, float $ingredientQuantity): float
    {
        return ($targetQuantity * $ingredientQuantity) / $baseQuantity;
    }

    public function calculateAllIngredientsForRecipe(array $recipe, float $targetQuantity): array
    {
        $baseQuantity = $recipe['quantitep'];
        $ingredients = [];

        foreach ($recipe['ingredients'] as $ingredient) {
            $newQuantity = $this->calculateIngredientsForQuantity(
                $baseQuantity,
                $targetQuantity,
                $ingredient['quantite']
            );

            $ingredients[] = [
                'nom' => $ingredient['nom'],
                'quantite' => $newQuantity,
                'unite' => $ingredient['unite']
            ];
        }

        return $ingredients;
    }
}
