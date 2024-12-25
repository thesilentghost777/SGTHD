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
        Schema::create('Production_suggerer_par_jour', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('produit');
            $table->integer('quantity');
            $table->foreign('produit')->references('code_produit')->on('Produit_fixes');
            $table->string('day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
