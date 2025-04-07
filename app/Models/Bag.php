<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock_quantity',
        'alert_threshold',
    ];

    /**
     * Récupérer toutes les assignations pour ce sac.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(BagAssignment::class);
    }

    /**
     * Vérifier si le stock est bas (en dessous ou égal au seuil d'alerte).
     *
     * @return bool
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->alert_threshold;
    }

    /**
     * Augmenter le stock de sacs.
     *
     * @param int $quantity
     * @return void
     */
    public function increaseStock(int $quantity): void
    {
        $this->stock_quantity += $quantity;
        $this->save();
    }
}
