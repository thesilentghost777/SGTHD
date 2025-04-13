<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasOne;

class BagReception extends Model

{

    use HasFactory;

    protected $fillable = [

        'bag_assignment_id',

        'quantity_received',

        'notes',

    ];

    /**

     * Récupérer l'assignation associée à cette réception.

     */

    public function assignment(): BelongsTo

    {

        return $this->belongsTo(BagAssignment::class, 'bag_assignment_id');

    }

    /**

     * Récupérer la vente associée à cette réception.

     */

    public function sale(): HasOne

    {

        return $this->hasOne(BagSale::class, 'bag_reception_id');

    }

    /**

     * Vérifier si cette réception a une vente associée.

     */

    public function getHasSaleAttribute(): bool

    {

        return $this->sale()->exists();

    }

}
