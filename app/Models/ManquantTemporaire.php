<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManquantTemporaire extends Model
{
    protected $table = 'manquant_temporaire';

    protected $fillable = [
        'employe_id',
        'montant',
        'explication',
        'statut',
        'commentaire_dg',
        'valide_par'
    ];

    protected $casts = [
        'montant' => 'integer',
    ];

    public function employe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employe_id');
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }
}
