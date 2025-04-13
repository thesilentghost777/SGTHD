<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduitRecu1 extends Model
{
    protected $table = 'produits_recu_1';

    protected $fillable = [
        'produit_id',
        'quantite',
        'producteur_id',
        'pointeur_id',
        'date_reception',
        'remarques'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit_fixes::class, 'produit_id', 'code_produit');
    }

    public function producteur()
    {
        return $this->belongsTo(User::class, 'producteur_id');
    }

    public function pointeur()
    {
        return $this->belongsTo(User::class, 'pointeur_id');
    }
}
