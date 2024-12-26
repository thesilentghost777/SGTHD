<?php

namespace Database\Factories;
use App\Models\Produit_fixes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commande>
 */
class CommandeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'libelle' => $this->faker->text(50), 
            'date_commande' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'produit' => Produit_fixes::inRandomOrder()->first()->code_produit, 
            'quantite' => fake()->numberBetween(100, 5000),
            'created_at' => now(),
            'updated_at' => now(),
            'categorie' => fake()->randomElement([
                'patisserie',
                'boulangerie',
            ]),
        ];
    }
}
