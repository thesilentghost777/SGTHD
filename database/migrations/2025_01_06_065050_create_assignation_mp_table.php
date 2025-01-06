<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignations_matiere', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producteur_id')->constrained('users');
            $table->foreignId('matiere_id')->constrained('Matiere');
            $table->decimal('quantite_assignee', 10, 3);
            $table->string('unite_assignee');
            $table->boolean('utilisee')->default(false);
            $table->timestamp('date_limite_utilisation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignations_matiere');
    }
};
