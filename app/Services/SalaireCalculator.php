<?php

namespace App\Services;

use App\Models\User;
use App\Models\Salaire;
use App\Models\AvanceSalaire;
use Carbon\Carbon;

class SalaireCalculator
{
    private $deductionsCalculator;

    public function __construct(DeductionsCalculator $deductionsCalculator)
    {
        $this->deductionsCalculator = $deductionsCalculator;
    }

    public function calculerFichePaie(User $employe, Carbon $mois): array
    {
        $salaire = Salaire::where('id_employe', $employe->id)->first();
        $avanceSalaire = AvanceSalaire::where('id_employe', $employe->id)
            ->whereMonth('mois_as', $mois->month)
            ->whereYear('mois_as', $mois->year)
            ->first();

        $deductions = $this->deductionsCalculator->calculerDeductions($employe->id, $mois);

        $salaireBase = $salaire?->somme ?? 0;
        $montantAvance = $avanceSalaire?->sommeAs ?? 0;

        $salaireNet = $salaireBase;
        $salaireNet -= $montantAvance;
        $salaireNet -= $deductions['manquants'];
        $salaireNet -= $deductions['remboursement'];
        $salaireNet -= $deductions['caisse_sociale'];

        return [
            'salaire_base' => $salaireBase,
            'avance_salaire' => $montantAvance,
            'manquants' => $deductions['manquants'],
            'remboursement' => $deductions['remboursement'],
            'pret' => $deductions['pret'], // Gardé pour affichage informatif uniquement
            'caisse_sociale' => $deductions['caisse_sociale'],
            'salaire_net' => $salaireNet
        ];
    }
}
