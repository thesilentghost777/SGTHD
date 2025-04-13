<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'bill_amount',
        'initial_coin_amount',
        'final_coin_amount',
        'deposited_amount',
        'sales_amount',
        'missing_amount',
        'status',
        'notes',
        'closed_by',
        'closed_at'
    ];

    protected $casts = [
        'date' => 'date',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function calculateMissingAmount()
    {
        // (Somme ventes + Billets initiaux + (Monnaie initiale - Monnaie finale)) - Versement
        if ($this->final_coin_amount !== null && $this->deposited_amount !== null) {
            $expectedAmount = $this->sales_amount + $this->bill_amount +
                             ($this->initial_coin_amount - $this->final_coin_amount);

            $this->missing_amount = max(0, $expectedAmount - $this->deposited_amount);
            return $this->missing_amount;
        }

        return null;
    }
}
