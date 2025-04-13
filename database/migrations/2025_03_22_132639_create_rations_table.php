<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rations', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant_defaut', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('employee_rations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->boolean('personnalise')->default(false);
            $table->timestamps();
        });

        Schema::create('ration_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->date('date_reclamation');
            $table->decimal('montant', 10, 2);
            $table->dateTime('heure_reclamation');
            $table->unique(['employee_id', 'date_reclamation']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ration_claims');
        Schema::dropIfExists('employee_rations');
        Schema::dropIfExists('rations');
    }
};
