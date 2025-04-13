<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solde_cp', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 15, 2)->default(0);
            $table->date('derniere_mise_a_jour');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insertion d'un solde initial
        DB::table('solde_cp')->insert([
            'montant' => 0,
            'derniere_mise_a_jour' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solde_cp');
    }
};
