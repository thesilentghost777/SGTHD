<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Reservations_mp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producteur_id')->constrained('users');
            $table->foreignId('matiere_id')->constrained('Matiere');
            $table->decimal('quantite_demandee', 10, 3);
            $table->string('unite_demandee');
            $table->enum('statut', ['en_attente', 'approuvee', 'refusee'])->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Reservations_mp');
    }
};
