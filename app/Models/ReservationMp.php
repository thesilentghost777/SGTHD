<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationMp extends Model
{
    protected $table = 'Reservations_mp';

    protected $fillable = [
        'producteur_id',
        'matiere_id',
        'quantite_demandee',
        'unite_demandee',
        'statut',
        'commentaire'
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
