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
        Schema::create('Porter', function (Blueprint $table) {
            $table->unsignedBigInteger('produit');
            $table->unsignedBigInteger('facture');
            $table->tinyInteger('quantite');
            $table->primary(['produit', 'facture']);
            $table->foreign('produit')->references('code_produit')->on('Produit');
            $table->foreign('facture')->references('code_fac')->on('Facture');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Porter');
    }
};
