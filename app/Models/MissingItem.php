<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'missing_calculation_id',
        'product_id',
        'expected_quantity',
        'actual_quantity',
        'missing_quantity',
        'amount'
    ];

    public function missingCalculation()
    {
        return $this->belongsTo(MissingCalculation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
