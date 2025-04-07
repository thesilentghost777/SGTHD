<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduitStock extends Model
{
    protected $fillable = [
        'id_produit',
        'quantite_en_stock',
        'quantite_invendu',
        'quantite_avarie'
    ];

    public function produitFixe(): BelongsTo
    {
        return $this->belongsTo(ProduitFixe::class, 'id_produit', 'code_produit');
    }
}
