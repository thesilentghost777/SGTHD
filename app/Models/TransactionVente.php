<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionVente extends Model
{
    use HasFactory;

    protected $table = 'transaction_ventes';

    protected $fillable = [
        'produit',
        'serveur',
        'quantite',
        'prix',
        'total_ventes',
        'date_vente',
        'type',
        'monnaie',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date_vente' => 'date',
    ];

    /**
     * Obtenir le produit associé à cette transaction.
     */
    public function produit()
    {
        return $this->belongsTo(Produit_fixes::class, 'produit', 'code_produit');
    }

    /**
     * Relation vers l'utilisateur (compatibilité avec l'ancien code).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir le vendeur (serveur) associé à cette transaction.
     */
    public function vendeur()
    {
        return $this->belongsTo(User::class, 'serveur', 'id');
    }
}
