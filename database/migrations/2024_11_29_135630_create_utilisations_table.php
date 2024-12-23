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
        Schema::create('Utilisation', function (Blueprint $table) {
            $table->unsignedBigInteger('produit');
            $table->unsignedBigInteger('matierep');
            $table->primary(['produit', 'matierep']);
            $table->foreign('produit')->references('code_produit')->on('Produit');
            $table->foreign('matierep')->references('code_mp')->on('Matiere');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Utilisation');
    }
};
