<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'preparation_time',
        'cooking_time',
        'rest_time',
        'yield_quantity',
        'difficulty_level',
        'category_id',
        'user_id',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function steps()
    {
        return $this->hasMany(RecipeStep::class)->orderBy('step_number');
    }

    public function getTotalTimeAttribute()
    {
        return ($this->preparation_time ?? 0) + ($this->cooking_time ?? 0) + ($this->rest_time ?? 0);
    }
}
