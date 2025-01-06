<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Produit_fixes;
use App\Models\Matiere;
use App\Models\Production;
use App\Models\Utilisation;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Trouver le producteur avec l'ID 17
        $producer = User::where('id', 17)->first();

        if (!$producer) {
            throw new \Exception('Producteur avec l\'ID 17 introuvable');
        }

        // Récupérer tous les produits
        $products = Produit_fixes::all();

        // Récupérer toutes les matières premières
        $materials = Matiere::all();

        // Créer des productions pour l'année 2025
        foreach ($products as $product) {
            for ($month = 1; $month <= 12; $month++) {
                $numProductions = rand(2, 5); // 2-5 productions par mois

                for ($i = 0; $i < $numProductions; $i++) {
                    $date = Carbon::create(2025, $month, rand(1, 28));

                    // Créer une production
                    $production = Production::create([
                        'produit' => $product->code_produit,
                        'producteur' => $producer->id,
                        'quantite' => rand(10, 100),
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    // Créer des utilisations pour cette production
                    foreach ($materials as $material) {
                        Utilisation::create([
                            'produit' => $product->code_produit,
                            'matierep' => $material->code_mp,
                            'producteur' => $producer->id,
                            'quantite_produit' => $production->quantite,
                            'quantite_matiere' => rand(1, 10),
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]);
                    }
                }
            }
        }
    }
}
