<?php

namespace App\Services;

use App\Models\Utilisation;
use Carbon\Carbon;

class LotGeneratorService
{
    /**
     * Génère un ID de lot unique
     * Format: AAAAMMJJ-XXX où XXX est un numéro séquentiel
     */
    public function generateLotId(): string
    {
        $date = Carbon::now()->format('Ymd');

        // Récupérer le dernier lot de la journée
        $lastLot = Utilisation::where('id_lot', 'like', $date . '-%')
            ->orderBy('id_lot', 'desc')
            ->first();

        if (!$lastLot) {
            $sequence = '001';
        } else {
            // Extraire et incrémenter le numéro de séquence
            $lastSequence = (int)substr($lastLot->id_lot, -3);
            $sequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        }

        return $date . '-' . $sequence;
    }
}
