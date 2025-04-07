<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatiereComplexe extends Model
{
    protected $table = 'matiere_complexe';

    protected $fillable = [
        'matiere_id',
        'prix_complexe',
    ];

    protected $casts = [
        'prix_complexe' => 'decimal:2',
    ];

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }
}