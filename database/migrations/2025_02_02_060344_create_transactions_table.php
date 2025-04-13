<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Colonne 'id' (clé primaire auto-incrémentée)
            $table->string('name'); // Colonne 'name' (nom de la catégorie) (sac,salaire,as,materiel,reparation,matiere_premiere,transport,supermarche,vente,autre)
    });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Colonne 'id' (clé primaire auto-incrémentée)
            $table->enum('type', ['income', 'outcome']); // Colonne 'type' (ENUM pour 'income' ou 'outcome')
            $table->unsignedBigInteger('category_id'); // Colonne 'category_id' (clé étrangère)
            $table->decimal('amount', 10, 2); // Colonne 'amount' (DECIMAL avec 10 chiffres au total et 2 décimales)
            $table->dateTime('date'); // Colonne 'date' (DATETIME)
            $table->text('description')->nullable(); // Colonne 'description' (TEXT, facultative)
            $table->timestamps(); // Colonnes 'created_at' et 'updated_at' (TIMESTAMP)

            // Clé étrangère pour 'category_id' référençant la table 'categories'
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
    });


}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('transactions');
    }
};
