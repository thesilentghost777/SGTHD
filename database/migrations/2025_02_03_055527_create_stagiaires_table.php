<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stagiaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('ecole');
            $table->string('niveau_etude');
            $table->string('filiere');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('departement');
            $table->text('nature_travail');
            $table->decimal('remuneration', 10, 2)->default(0);
            $table->text('appreciation')->nullable();
            $table->enum('type_stage', ['academique', 'professionnel']);
            $table->boolean('rapport_genere')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stagiaires');
    }
};
