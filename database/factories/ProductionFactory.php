<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Produit_fixes;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produit>
 */
class ProductionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->unique()->numberBetween(1,500),
            'produit' => Produit_fixes::inRandomOrder()->first()->code_produit,
            'producteur' => User::inRandomOrder()->first()->id,
            'quantite' => fake()->numberBetween(10, 1000), // Quantité entre 10 et 1000
            'created_at' => fake()->unique()->dateTime(), // Date
            'updated_at' => fake()->unique()->dateTime(), //
        ];
    }
}
