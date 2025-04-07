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
    protected $casts = [
        'date' => 'date',
    ];

    public function verseur()
    {
        return $this->belongsTo(User::class, 'verseur');
    }

    public function encaisseur()
    {
        return $this->belongsTo(User::class, 'encaisseur');
    }

    // In App\Models\VersementCsg.php
public function verseur_user()
{
    return $this->belongsTo(User::class, 'verseur');
}

public function encaisseur_user()
{
    return $this->belongsTo(User::class, 'encaisseur');
}
}

