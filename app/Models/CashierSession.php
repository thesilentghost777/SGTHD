<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'initial_cash',
        'initial_change',
        'initial_mobile_balance',
        'final_cash',
        'final_change',
        'final_mobile_balance',
        'cash_remitted',
        'total_withdrawals',
        'discrepancy',
        'notes',
        'end_notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'initial_cash' => 'float',
        'initial_change' => 'float',
        'initial_mobile_balance' => 'float',
        'final_cash' => 'float',
        'final_change' => 'float',
        'final_mobile_balance' => 'float',
        'cash_remitted' => 'float',
        'total_withdrawals' => 'float',
        'discrepancy' => 'float',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the withdrawals for this session.
     */
    public function withdrawals(): HasMany
    {
        return $this->hasMany(CashWithdrawal::class);
    }

    /**
     * Check if the session is active.
     */
    public function isActive(): bool
    {
        return $this->end_time === null;
    }

    /**
     * Calculate session duration
     */
    public function getDurationAttribute()
    {
        if ($this->end_time) {
            return $this->start_time->diffForHumans($this->end_time, true);
        }

        return $this->start_time->diffForHumans(now(), true);
    }
}
