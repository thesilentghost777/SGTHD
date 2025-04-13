<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recipe_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->integer('step_number');
            $table->text('instruction');
            $table->text('tips')->nullable();
            $table->integer('time_required')->nullable(); // in minutes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipe_steps');
    }
};
