<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransactionVente;
use App\Models\User;
use App\Models\ProduitFixe;

class TransactionVenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        TransactionVente::factory(50)->create(); // Génère 50 transactions de vente
    }
}
