<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('manquant_temporaire', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employe_id');
            $table->foreign('employe_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('montant')->default(0);
            $table->text('explication')->nullable();
            $table->enum('statut', ['en_attente', 'ajuste', 'valide'])->default('en_attente');
            $table->text('commentaire_dg')->nullable();
            $table->unsignedBigInteger('valide_par')->nullable();
            $table->foreign('valide_par')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('manquant_temporaire');
    }
};
