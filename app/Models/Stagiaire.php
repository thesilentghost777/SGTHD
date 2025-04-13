<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stagiaire extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'ecole',
        'niveau_etude',
        'filiere',
        'date_debut',
        'date_fin',
        'departement',
        'nature_travail',
        'remuneration',
        'appreciation',
        'type_stage',
        'rapport_genere',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'rapport_genere' => 'boolean',
    ];
}
