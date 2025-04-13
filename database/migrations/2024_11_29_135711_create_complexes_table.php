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
        Schema::create('Complexe', function (Blueprint $table) {
            $table->unsignedBigInteger('id_comp');
            $table->string('nom', 50);
            $table->string('localisation', 50);
            $table->bigInteger('revenu_mensuel')->default(0);
            $table->bigInteger('revenu_annuel')->default(0);
            $table->bigInteger('solde')->default(0);
            $table->$table->bigInteger('caisse_sociale')->nullable()->default(0);
            $table->$table->bigInteger('valeur_caisse_sociale')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Complexe');
    }
};
