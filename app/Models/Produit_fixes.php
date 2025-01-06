<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Produit_fixes extends Model
{
    use HasFactory;
    protected $table = 'Produit_fixes';
    protected $primaryKey = 'code_produit';

    protected $fillable = [
        'nom',
        'prix',
        'categorie',
    ];

    public function utilisations(): HasMany
    {
        return $this->hasMany(Utilisation::class, 'produit', 'code_produit');
    }
    public function matiereRecommandee()
{
    return $this->hasMany(MatiereRecommander::class, 'produit', 'code_produit');
}

}

