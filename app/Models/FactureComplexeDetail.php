<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactureComplexeDetail extends Model
{
    protected $table = 'facture_complexe_details';

    protected $fillable = [
        'facture_id',
        'matiere_id',
        'quantite',
        'unite',
        'prix_unitaire',
        'assignation_id',
        'montant',
    ];

    protected $casts = [
        'quantite' => 'decimal:3',
        'prix_unitaire' => 'decimal:2',
        'montant' => 'decimal:2',
    ];

    public function facture(): BelongsTo
    {
        return $this->belongsTo(FactureComplexe::class, 'facture_id');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }
}
