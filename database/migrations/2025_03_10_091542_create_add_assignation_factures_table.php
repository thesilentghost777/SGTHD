<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assignation_factures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignation_id');
            $table->foreign('assignation_id')->references('id')->on('assignations_matiere')->onDelete('cascade');
            $table->unsignedBigInteger('facture_id');
            $table->foreign('facture_id')->references('id')->on('factures_complexe')->onDelete('cascade');
            $table->timestamps();

            // Index unique pour éviter les doublons
            $table->unique(['assignation_id', 'facture_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignation_factures');
    }
};
