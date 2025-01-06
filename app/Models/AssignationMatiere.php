<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignationMatiere extends Model
{
    protected $table = 'assignations_matiere';

    protected $fillable = [
        'producteur_id',
        'matiere_id',
        'quantite_assignee',
        'unite_assignee',
        'utilisee',
        'date_limite_utilisation'
    ];

    protected $casts = [
        'utilisee' => 'boolean',
        'date_limite_utilisation' => 'datetime'
    ];

    public function producteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'producteur_id');
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }
}
