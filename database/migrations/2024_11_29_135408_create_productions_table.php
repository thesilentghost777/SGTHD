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
        Schema::create('Production', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produit');
            $table->unsignedBigInteger('producteur');
            $table->smallInteger('quantite');
            $table->timestamps();
        
            $table->foreign('producteur')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('produit')
                  ->references('code_produit')
                  ->on('Produit_fixes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Produit');
    }
};
