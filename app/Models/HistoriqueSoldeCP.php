<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueSoldeCP extends Model
{
    use HasFactory;

    protected $table = 'historique_solde_cp';

    protected $fillable = [
        'montant',
        'type_operation',
        'operation_id',
        'solde_avant',
        'solde_apres',
        'user_id',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function logTransaction($montant, $type, $operation_id, $description = null)
    {
        $solde = SoldeCP::getSoldeActuel();
        $soldeAvant = $solde->montant;

        // Mise à jour du solde
        if ($type == 'versement') {
            $soldeApres = $soldeAvant + $montant;
        } else {
            $soldeApres = $soldeAvant - $montant;
        }

        SoldeCP::updateSolde($soldeApres, $description);

        // Enregistrement de l'historique
        return self::create([
            'montant' => $montant,
            'type_operation' => $type,
            'operation_id' => $operation_id,
            'solde_avant' => $soldeAvant,
            'solde_apres' => $soldeApres,
            'user_id' => auth()->id(),
            'description' => $description
        ]);
    }
}
