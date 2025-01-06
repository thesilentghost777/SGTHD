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
        Schema::create('Depense', function (Blueprint $table) {
            $table->unsignedBigInteger('code_dep')->autoIncrement();
            $table->unsignedBigInteger('chef_production');
            $table->string('libelle', 255);
            $table->unsignedBigInteger('montant');
            $table->foreign('chef_production')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Depense');
    }
};
