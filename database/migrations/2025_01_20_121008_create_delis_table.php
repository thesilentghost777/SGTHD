<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delis', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description');
            $table->decimal('montant', 10, 2);
            $table->timestamps();
        });

        Schema::create('deli_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deli_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date_incident');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deli_user');
        Schema::dropIfExists('delis');
    }
};
