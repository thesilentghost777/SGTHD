<?php

namespace App\Services;

use App\Models\Utilisation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LotSecurityService
{
    /**
     * Vérifie si un lot est valide et appartient à l'utilisateur
     */
    public function verifyLotAccess(string $lotId, int $userId): bool
    {
        return Utilisation::where('id_lot', $lotId)
            ->where('producteur', $userId)
            ->exists();
    }

    /**
     * Vérifie si un lot existe déjà
     */
    public function lotExists(string $lotId): bool
    {
        return Utilisation::where('id_lot', $lotId)->exists();
    }

    /**
     * Génère un ID de lot unique
     */
    public function generateUniqueLotId(int $userId): string
    {
        $prefix = Carbon::now()->format('Ymd');
        $unique = false;
        $lotId = '';

        while (!$unique) {
            $lotId = $prefix . '-' . $userId . '-' . Str::random(4);
            if (!$this->lotExists($lotId)) {
                $unique = true;
            }
        }

        return $lotId;
    }

    /**
     * Valide le format de l'ID du lot
     */
    public function validateLotIdFormat(string $lotId): bool
    {
        return (bool) preg_match('/^\d{8}-\d+-[A-Za-z0-9]{4}$/', $lotId);
    }

    /**
     * Vérifie si le lot est du même jour
     */
    public function isLotFromToday(string $lotId): bool
    {
        $lotDate = substr($lotId, 0, 8);
        return $lotDate === Carbon::now()->format('Ymd');
    }

    /**
     * Vérifie si le lot appartient à l'utilisateur et est de la bonne date
     */
    public function validateFullLotAccess(string $lotId, int $userId): bool
    {
        return $this->validateLotIdFormat($lotId) &&
               $this->verifyLotAccess($lotId, $userId) &&
               $this->isLotFromToday($lotId);
    }
}
