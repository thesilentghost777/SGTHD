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
        Schema::create('Daily_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chef_production');
            $table->unsignedBigInteger('producteur');
            $table->unsignedBigInteger('produit');
            $table->integer('expected_quantity');
            $table->foreign('produit')->references('code_produit')->on('Produit_fixes');
            $table->foreign('chef_production')->references('id')->on('users');
            $table->foreign('producteur')->references('id')->on('users');
            $table->date('assignment_date');
            $table->Integer('status');
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
