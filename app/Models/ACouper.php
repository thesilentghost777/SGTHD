<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ACouper extends Model
{
    use HasFactory;

    protected $table = 'Acouper';

    protected $fillable = [
        'id_employe',
        'manquants',
        'remboursement',
        'pret',
        'caisse_sociale',
        'date'
    ];

    protected $casts = [
        'date' => 'date',
        'manquants' => 'integer',
        'remboursement' => 'integer',
        'pret' => 'integer',
        'caisse_sociale' => 'integer'
    ];

    public function employe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_employe');
    }
}
