<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('avance_salaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_employe')->constrained('users')->onDelete('cascade');
            $table->decimal('sommeAs', 10, 2);
            $table->boolean('flag')->default(false);
            $table->boolean('retrait_demande')->default(false);
            $table->boolean('retrait_valide')->default(false);
            $table->date('mois_as')->default(now());
            $table->timestamps();
            
            // Un employé ne peut avoir qu'une seule avance en cours
            $table->unique('id_employe');
        });
    }

    public function down()
    {
        Schema::dropIfExists('avance_salaires');
    }
};