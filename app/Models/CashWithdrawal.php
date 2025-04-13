<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashWithdrawal extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'cashier_session_id',
        'amount',
        'reason',
        'withdrawn_by',
        'created_at'
    ];

    protected $casts = [
        'amount' => 'float',
        'created_at' => 'datetime',
    ];

    /**
     * Get the session that owns the withdrawal.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(CashierSession::class, 'cashier_session_id');
    }
}
