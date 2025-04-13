<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RationClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date_reclamation',
        'montant',
        'heure_reclamation',
    ];

    protected $casts = [
        'date_reclamation' => 'date',
        'heure_reclamation' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
