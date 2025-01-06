<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Utilisation extends Model
{
    protected $table = 'Utilisation';

    protected $fillable = [
        'produit',
        'matierep',
        'producteur',
        'quantite_produit',
        'quantite_matiere'
    ];

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matierep', 'id');
    }

    public function produitFixe(): BelongsTo
    {
        return $this->belongsTo(Produit_fixes::class, 'produit', 'code_produit');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'producteur', 'id');
    }
}
