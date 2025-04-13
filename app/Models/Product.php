<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'price',
        'product_group_id'
    ];

    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function missingItems()
    {
        return $this->hasMany(MissingItem::class);
    }
}
