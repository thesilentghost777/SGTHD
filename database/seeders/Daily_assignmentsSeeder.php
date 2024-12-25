<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Daily_assignments;
use App\Models\User;
use App\Models\Produit_fixes;

class Daily_assignmentsSeeder extends Seeder
{
    public function run()
    {
        Daily_assignments::factory()->count(50)->create();
    }
}
