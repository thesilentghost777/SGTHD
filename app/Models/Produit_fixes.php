<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produit_fixes extends Model
{
    use HasFactory;
    protected $table = 'Produit_fixes';
    protected $primaryKey = 'code_produit';

    protected $fillable = [
        'nom',
        'prix',
        'categorie'
    ];

    public function utilisations(): HasMany
    {
        return $this->hasMany(Utilisation::class, 'produit', 'code_produit');
    }

    public function matiereRecommandee(): HasMany
    {
        return $this->hasMany(MatiereRecommander::class, 'produit', 'code_produit');
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class, 'produit', 'code_produit');
    }

    public function receptions(): HasMany
    {
        return $this->hasMany(ProduitRecu::class, 'produit_id', 'code_produit');
    }

    public function ventes(): HasMany
    {
        return $this->hasMany(TransactionVente::class, 'produit', 'code_produit');
    }

    public function stock(): HasOne
    {
        return $this->hasOne(ProduitStock::class, 'id_produit', 'code_produit');
    }
}
