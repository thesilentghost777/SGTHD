<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->boolean('first_config')->default(false);
            $table->boolean('flag1')->default(false);
            $table->boolean('flag2')->default(false);
            $table->boolean('flag3')->default(false);
            $table->boolean('flag4')->default(false);
            $table->timestamps();
        });

        // Insérer un enregistrement par défaut avec toutes les valeurs à false (0)
        DB::table('configurations')->insert([
            'first_config' => false,
            'flag1' => false,
            'flag2' => false,
            'flag3' => false,
            'flag4' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
