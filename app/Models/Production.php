<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Produit_fixes;

class Production extends Model
{
    use HasFactory;
    protected $table = 'Production';

    protected $fillable = [
        'produit',
        'producteur',
        'quantite'
    ];

    public function produitFixe(): BelongsTo
    {
        return $this->belongsTo(Produit_fixes::class, 'produit', 'code_produit');
    }

    public function utilisations(): HasMany
    {
        return $this->hasMany(Utilisation::class, 'produit', 'produit');
    }

    public function producteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'producteur', 'id');
    }
}
