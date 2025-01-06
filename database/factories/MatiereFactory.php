<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Matiere>
 */
class MatiereFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'nom' => fake()->randomElement([
                'Farine',
                'Sucre',
                'Sel',
                'Levure',
                'Huile',
                'Lait',
                'Oeufs',
                'Beurre',
                'Chocolat',
                'Vanille',
                'Miel',
                'Cacao',
                'Amandes',
                'Noix',
                'Cannelle'
            ]),
            'prix' => fake()->numberBetween(100, 5000), // Prix entre 100 et 5000
            'quantite' => fake()->numberBetween(10, 1000), // Quantité entre 10 et 1000
            'created_at' => fake()->unique()->dateTime(), // Date
            'updated_at' => fake()->unique()->dateTime(), //
        ];
    }
}
