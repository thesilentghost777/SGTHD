<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('type_taules', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('formule_farine')->nullable();
            $table->string('formule_eau')->nullable();
            $table->string('formule_huile')->nullable();
            $table->string('formule_autres')->nullable();
            $table->timestamps();
        });

        Schema::create('taules_inutilisees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producteur_id')->constrained('users');
            $table->foreignId('type_taule_id')->constrained('type_taules');
            $table->integer('nombre_taules');
            $table->foreignId('matiere_creee_id')->nullable()->constrained('Matiere');
            $table->boolean('recuperee')->default(false);
            $table->foreignId('recuperee_par')->nullable()->constrained('users');
            $table->dateTime('date_recuperation')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('taules_inutilisees');
        Schema::dropIfExists('type_taules');
    }
};
