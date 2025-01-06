<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ACouper;

class ACouperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Fixe le montant de la caisse sociale pour chaque employé
        $caisse_sociale_montant = 500;

        // Générer les données pour chaque employé
        \App\Models\User::all()->each(function ($user) use ($caisse_sociale_montant) {
            ACouper::factory()->create([
                'id_employe' => $user->id,
                'manquants' => fake()->numberBetween(0, 1000),
                'remboursement' => fake()->numberBetween(0, 1000),
                'pret' => fake()->numberBetween(0, 2000),
                'date' => 'now',
                'caisse_sociale' => $caisse_sociale_montant,
            ]);
        });
    }
}
