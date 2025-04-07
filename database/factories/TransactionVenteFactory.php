<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Produit_fixes;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionVente>
 */
class TransactionVenteFactory extends Factory
{
    protected $model = \App\Models\TransactionVente::class;

    public function definition()
    {
        return [
            'produit' => Produit_fixes::inRandomOrder()->first()->code_produit ?? Produit_fixes::factory(),
            'serveur' => User::inRandomOrder()->first()->id ?? User::factory(),
            'quantite' => $this->faker->numberBetween(1, 100), // Quantité aléatoire
            'prix' => $this->faker->numberBetween(100, 1000), // Prix aléatoire
            'date_vente' => $this->faker->dateTimeThisYear()->format('Y-m-d'), // Date dans l'année
            'type' => $this->faker->randomElement(['Vente','Produit invendu','Produit Avarie']),
            'monnaie' => $this->faker->randomElement(['USD', 'EUR', 'XAF']), // Exemple de devises
        ];
    }
}
