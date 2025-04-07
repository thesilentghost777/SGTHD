<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('preparation_time')->nullable(); // in minutes
            $table->integer('cooking_time')->nullable(); // in minutes
            $table->integer('rest_time')->nullable(); // in minutes
            $table->integer('yield_quantity')->nullable(); // how many items this recipe produces
            $table->string('difficulty_level')->nullable(); // e.g. easy, medium, hard
            $table->foreignId('category_id')->nullable()->constrained('recipe_categories')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // who created this recipe
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipes');
    }
};
