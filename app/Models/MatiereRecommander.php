<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatiereRecommander extends Model
{
    protected $table = 'Matiere_recommander';

    protected $fillable = [
        'produit',
        'matierep',
        'quantitep',
        'quantite',
        'unite'
    ];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit_fixes::class, 'produit', 'code_produit');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matierep');
    }
}
