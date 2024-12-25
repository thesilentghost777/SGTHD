<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $fillable = [
        'producteur',
        'produit',
        'quantite',
        'created_at',
        'updated_at'
    ];
    protected $table = 'Production';
    use HasFactory;
}
