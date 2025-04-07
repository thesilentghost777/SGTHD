<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Produit_fixes;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProduitRecu>
 */
class ProduitRecuFactory extends Factory
{
    protected $model = \App\Models\ProduitRecu::class;

    public function definition()
    {
        return [
            'pointeur' => User::inRandomOrder()->first()->id ?? User::factory(),
            'produit' => Produit_fixes::inRandomOrder()->first()->code_produit ?? Produit_fixes::factory(),
            'nom' => $this->faker->word,
            'prix' => $this->faker->numberBetween(10, 100), // Prix aléatoire entre 10 et 100
            'quantite' => $this->faker->numberBetween(1, 50), // Quantité aléatoire entre 1 et 50
        ];
    }
}
