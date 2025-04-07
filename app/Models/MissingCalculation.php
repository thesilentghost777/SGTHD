<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_group_id',
        'user_id',
        'date',
        'title',
        'status', // 'open', 'closed'
        'total_amount'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function missingItems()
    {
        return $this->hasMany(MissingItem::class);
    }
}
