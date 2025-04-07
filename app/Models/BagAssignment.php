<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;

class BagAssignment extends Model

{

    use HasFactory;

    protected $fillable = [

        'bag_id',

        'user_id',

        'quantity_assigned',

        'notes',

    ];

    /**

     * Récupérer le sac associé à cette assignation.

     */

    public function bag(): BelongsTo

    {

        return $this->belongsTo(Bag::class);

    }

    /**

     * Récupérer l'utilisateur (serveur) associé à cette assignation.

     */

    public function user(): BelongsTo

    {

        return $this->belongsTo(User::class);

    }

    /**

     * Récupérer toutes les réceptions pour cette assignation.

     */

    public function receptions(): HasMany

    {

        return $this->hasMany(BagReception::class);

    }

    /**

     * Calculer la quantité totale reçue pour cette assignation.

     */

    public function getTotalReceivedAttribute(): int

    {

        return $this->receptions()->sum('quantity_received');

    }

    /**

     * Calculer la différence entre la quantité assignée et la quantité reçue.

     */

    public function getDiscrepancyAttribute(): int

    {

        return $this->quantity_assigned - $this->total_received;

    }

}
