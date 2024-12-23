<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produit>
 */
class ProduitFactory extends Factory
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
            'producteur' => User::inRandomOrder()->first()->id,
            'nom' => fake()->unique()->randomElement([
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
            'quantite' => fake()->numberBetween(10, 1000), // Quantité entre 10 et 1000
            'created_at' => fake()->unique()->dateTime(), // Date
            'updated_at' => fake()->unique()->dateTime(), //
        ];
    }
}
