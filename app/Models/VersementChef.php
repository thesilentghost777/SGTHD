<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersementChef extends Model
{
    protected $table = 'Versement_chef';
    protected $primaryKey = 'code_vc';

    protected $fillable = [
        'chef_production',
        'libelle',
        'montant',
        'date',
        'status' // 0: En attente, 1: Validé
    ];
    protected $casts = [
        'date' => 'date',
        'status' => 'boolean',
    ];


    public function chefProduction()
    {
        return $this->belongsTo(User::class, 'chef_production');
    }
}
