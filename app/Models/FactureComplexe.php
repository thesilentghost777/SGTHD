<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactureComplexe extends Model
{
    protected $table = 'factures_complexe';

    protected $fillable = [
        'reference',
        'producteur_id',
        'id_lot',
        'montant_total',
        'statut',
        'date_creation',
        'date_validation',
        'notes',
    ];

    protected $casts = [
        'date_creation' => 'date',
        'date_validation' => 'date',
        'montant_total' => 'decimal:2',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(FactureComplexeDetail::class, 'facture_id');
    }

    public function producteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'producteur_id');
    }

    // Générer une référence unique pour la facture
    public static function genererReference(): string
    {
        $prefix = 'FC-';
        $dateCode = date('Ymd');
        $lastFacture = self::where('reference', 'like', $prefix . $dateCode . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = '001';

        if ($lastFacture) {
            $lastSequence = substr($lastFacture->reference, -3);
            $sequence = str_pad((int)$lastSequence + 1, 3, '0', STR_PAD_LEFT);
        }

        return $prefix . $dateCode . '-' . $sequence;
    }
}
