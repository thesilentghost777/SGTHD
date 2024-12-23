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
        Schema::create('Facture', function (Blueprint $table) {
            $table->unsignedBigInteger('code_fac')->autoIncrement();
            $table->unsignedBigInteger('produit')->nullable();
            $table->unsignedBigInteger('chef_production');
            $table->dateTime('date');
            $table->foreign('produit')->references('code_produit')->on('Produit');
            $table->foreign('chef_production')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Facture');
    }
};
