<?php

namespace App\Services;

use App\Models\ACouper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeductionsCalculator
{
    public function calculerDeductions(int $employeId, Carbon $mois): array
    {
        $deductions = DB::table('Acouper')
            ->where('id_employe', $employeId)
            ->whereYear('date', $mois->year)
            ->whereMonth('date', $mois->month)
            ->selectRaw('
                COALESCE(SUM(manquants), 0) as total_manquants,
                COALESCE(SUM(remboursement), 0) as total_remboursement,
                COALESCE(SUM(pret), 0) as total_pret,
                COALESCE(SUM(caisse_sociale), 0) as total_caisse_sociale
            ')
            ->first();

        if (!$deductions) {
            return [
                'manquants' => 0,
                'remboursement' => 0,
                'pret' => 0,
                'caisse_sociale' => 0
            ];
        }

        return [
            'manquants' => (int) $deductions->total_manquants,
            'remboursement' => (int) $deductions->total_remboursement,
            'pret' => (int) $deductions->total_pret,
            'caisse_sociale' => (int) $deductions->total_caisse_sociale
        ];
    }
}
