<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model

{

    protected $fillable = [

        'type',

        'category_id',

        'amount',

        'date',

        'description'

    ];

    protected $casts = [

        'date' => 'datetime',

        'amount' => 'decimal:2'

    ];

    public function category(): BelongsTo

    {

        return $this->belongsTo(Category::class);

    }

}
