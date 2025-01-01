<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_employe',
        'somme',
        'somme_effective_mois'
    ];

    public function employe()
    {
        return $this->belongsTo(User::class, 'id_employe');
    }
}