<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class VersementCsg extends Model
{
    use HasFactory;
    protected $table = 'Versement_csg';
    protected $primaryKey='code_vcsg';
    protected $fillable = [
        'libelle',
        'date',
        'somme',
        'verseur',
        'encaisseur',
        'created_at',
        'updated_at'
    ];
}

