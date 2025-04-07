<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BagSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'bag_reception_id',
        'quantity_sold',
        'quantity_unsold',
        'notes',
        'is_recovered',
    ];

    protected $casts = [
        'is_recovered' => 'boolean',
    ];

    /**
     * Récupérer la réception associée à cette vente.
     */
    public function reception(): BelongsTo
    {
        return $this->belongsTo(BagReception::class, 'bag_reception_id');
    }

    /**
     * Récupérer le sac associé à cette vente via la réception et l'assignation.
     */
    public function getBagAttribute()
    {
        return $this->reception->assignment->bag;
    }
}
