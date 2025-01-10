<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model {
    protected $table = 'Commande';  // Spécifiez le nom de la table en minuscules

    protected $fillable = [
        'libelle',
        'produit',
        'quantite',
        'date_commande',
        'categorie',
        'valider'
    ];

    protected $casts = [
        'date_commande' => 'datetime',
        'valider' => 'boolean'
    ];

    public function produitFixe()
    {
        return $this->belongsTo(Produit_fixes::class, 'produit', 'code_produit');
    }

}
