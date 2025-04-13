<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldeCP extends Model
{
    use HasFactory;

    protected $table = 'solde_cp';

    protected $fillable = ['montant', 'derniere_mise_a_jour', 'description'];

    public static function getSoldeActuel()
    {
        return self::first();
    }

    public static function updateSolde($montant, $description = null)
    {
        $solde = self::first();
        $solde->montant = $montant;
        $solde->derniere_mise_a_jour = now();
        $solde->description = $description;
        $solde->save();

        return $solde;
    }
}
