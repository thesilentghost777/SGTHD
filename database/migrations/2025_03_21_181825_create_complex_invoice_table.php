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
        Schema::create('factures_complexe', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('producteur_id');
            $table->foreign('producteur_id')->references('id')->on('users');
            $table->string('id_lot')->nullable();
            $table->decimal('montant_total', 10, 2);
            $table->string('statut')->default('en_attente'); // en_attente, validee, annulee
            $table->date('date_creation');
            $table->date('date_validation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('facture_complexe_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facture_id');
            $table->foreign('facture_id')->references('id')->on('factures_complexe')->onDelete('cascade');
            $table->unsignedBigInteger('matiere_id');
            $table->foreign('matiere_id')->references('id')->on('Matiere');
            $table->decimal('quantite', 10, 3);
            $table->string('unite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('montant', 10, 2);
            $table->timestamps();
        });

        Schema::create('matiere_complexe', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('matiere_id');
            $table->decimal('prix_complexe', 10, 2)->nullable(); // prix d'une unité dans le complexe
            $table->timestamps();

            $table->foreign('matiere_id')->references('id')->on('Matiere')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_complexe_details');
        Schema::dropIfExists('factures_complexe');
        Schema::dropIfExists('matiere_complexe');
    }
};