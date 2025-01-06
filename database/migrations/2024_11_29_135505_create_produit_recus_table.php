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
        Schema::create('Produit_recu', function (Blueprint $table) {
            $table->unsignedBigInteger('code_produit')->autoIncrement();
            $table->unsignedBigInteger('pointeur');
            $table->unsignedBigInteger('produit');
            $table->string('nom', 50);
            $table->smallInteger('prix');
            $table->smallInteger('quantite');
            $table->foreign('pointeur')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('produit')->references('code_produit')->on('Produit_fixes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Produit_recu');
    }
};
