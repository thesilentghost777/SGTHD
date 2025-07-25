<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model

{

    public $timestamps = false;

    protected $fillable = ['name'];

    public function transactions(): HasMany

    {

        return $this->hasMany(Transaction::class);

    }

}
