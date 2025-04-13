<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TauleInutilisee extends Model
{
    use HasFactory;

    protected $table = 'taules_inutilisees';

    protected $fillable = [
        'producteur_id',
        'type_taule_id',
        'nombre_taules',
        'matiere_creee_id',
        'recuperee',
        'recuperee_par',
        'date_recuperation',
    ];

    protected $casts = [
        'recuperee' => 'boolean',
        'date_recuperation' => 'datetime',
    ];

    public function producteur()
    {
        return $this->belongsTo(User::class, 'producteur_id');
    }

    public function recuperateur()
    {
        return $this->belongsTo(User::class, 'recuperee_par');
    }

    public function typeTaule()
    {
        return $this->belongsTo(TypeTaule::class);
    }

    public function matiereCreee()
    {
        return $this->belongsTo(Matiere::class, 'matiere_creee_id');
    }
}
