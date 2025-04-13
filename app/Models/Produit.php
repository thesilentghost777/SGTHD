<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $fillable = [
        'nom',
        'reference',
        'type',
        'quantite',
        'prix_unitaire',
        'seuil_alerte'
    ];

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function inventaires()
    {
        return $this->hasMany(Inventaire::class);
    }
}
