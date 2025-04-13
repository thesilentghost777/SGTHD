<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeTaule extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'formule_farine',
        'formule_eau',
        'formule_huile',
        'formule_autres',
    ];

    public function tauleInutilisees()
    {
        return $this->hasMany(TauleInutilisee::class);
    }
}
