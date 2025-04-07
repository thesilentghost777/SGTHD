<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('Extra', function (Blueprint $table) {
            $table->id();
            $table->string('secteur', 50);
            $table->time('heure_arriver_adequat');
            $table->time('heure_depart_adequat');
            $table->decimal('salaire_adequat', 10, 2);
            $table->text('interdit')->nullable();
            $table->text('regles')->nullable();
            $table->integer('age_adequat');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('EXTRA');
    }
};
