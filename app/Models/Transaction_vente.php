<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction_vente extends Model
{
    use HasFactory;
    protected $fillable = [
        'produit',
        'serveur',
        'quantite',
        'prix',
        'total_ventes',
        'date_vente',
        'created_at',
        'updated_at'
    ];
}
