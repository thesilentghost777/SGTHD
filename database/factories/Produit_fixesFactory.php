<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produit_fixes>
 */
class Produit_fixesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code_produit' => fake()->unique()->numberBetween(1,500),
            'nom' => fake()->randomElement([
                'beignets',
                'beignets au lait',
                'beignets soufler',
                'camard',
                'cake',
                'haburger',
                'pizza',
                'chouquette',
                'sqndwicth',
                'pain',
                'pain au lait',
                'pain tradition',
                'pain complet',
                'pouding',
            ]),
            'prix' => fake()->numberBetween(100, 5000), // Prix entre 100 et 5000
            'categorie' => fake()->randomElement([
                'patisserie',
                'boulangerie',
            ]),
            'created_at' => fake()->dateTime(), // Date
            'updated_at' => fake()->dateTime(), //
        ];
    }
}
