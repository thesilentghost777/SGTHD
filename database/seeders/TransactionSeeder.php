<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        // S'assurer qu'il y a des catégories
        $categories = Category::all();

        if ($categories->isEmpty()) {
            // Créer quelques catégories par défaut si aucune n'existe
            $defaultCategories = [
                ['name' => 'Salaires'],
                ['name' => 'Ventes'],
                ['name' => 'Loyer'],
                ['name' => 'Fournitures'],
                ['name' => 'Équipement'],
                ['name' => 'Services'],
                ['name' => 'Marketing'],
                ['name' => 'Maintenance'],
            ];

            foreach ($defaultCategories as $category) {
                Category::create($category);
            }

            $categories = Category::all();
        }

        // Générer 150 transactions
        for ($i = 0; $i < 150; $i++) {
            $type = fake()->randomElement(['income', 'outcome']);
            $date = Carbon::now()->subDays(rand(0, 365));

            // Montants plus réalistes selon le type
            $amount = $type === 'income'
                ? fake()->randomFloat(2, 500, 10000)  // Revenus entre 500 et 10000
                : fake()->randomFloat(2, 50, 5000);   // Dépenses entre 50 et 5000

            Transaction::create([
                'type' => $type,
                'category_id' => $categories->random()->id,
                'amount' => $amount,
                'date' => $date,
                'description' => fake()->sentence(rand(3, 8)),
            ]);
        }
    }
}
