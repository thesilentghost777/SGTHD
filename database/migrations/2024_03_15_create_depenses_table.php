<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*depense faite par le chef de production*/
    public function up()
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auteur');
            $table->string('nom');
            $table->decimal('prix', 15, 2);
            $table->enum('type', ['achat_matiere', 'livraison_matiere', 'reparation', 'depense_fiscale', 'autre']);
            $table->unsignedBigInteger('idm')->nullable();
            $table->date('date');
            $table->boolean('valider')->default(true);
            $table->timestamps();
            $table->foreign('auteur')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('idm')->references('id')->on('Matiere')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('depenses');
    }
};
