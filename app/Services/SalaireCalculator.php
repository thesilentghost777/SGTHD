<?php
namespace App\Services;

use App\Models\User;
use App\Models\Salaire;
use App\Models\AvanceSalaire;
use App\Models\Deli;
use Carbon\Carbon;

class SalaireCalculator
{
    private $deductionsCalculator;

    public function __construct(DeductionsCalculator $deductionsCalculator)
    {
        $this->deductionsCalculator = $deductionsCalculator;
    }

    private function calculerDelisMois(User $employe, Carbon $mois): array
    {
        $delisDuMois = $employe->delis()
            ->wherePivot('date_incident', '>=', $mois->startOfMonth())
            ->wherePivot('date_incident', '<=', $mois->endOfMonth())
            ->get();

        $montantTotal = 0;
        $detailsDelis = [];

        foreach ($delisDuMois as $deli) {
            $montantTotal += $deli->montant;
            $detailsDelis[] = [
                'nom' => $deli->nom,
                'date' => $deli->pivot->date_incident,
                'montant' => $deli->montant
            ];
        }

        return [
            'montant_total' => $montantTotal,
            'details' => $detailsDelis
        ];
    }

    public function calculerFichePaie(User $employe, Carbon $mois): array
    {
        $salaire = Salaire::where('id_employe', $employe->id)->first();
        $avanceSalaire = AvanceSalaire::where('id_employe', $employe->id)
            ->whereMonth('mois_as', $mois->month)
            ->whereYear('mois_as', $mois->year)
            ->first();

        $deductions = $this->deductionsCalculator->calculerDeductions($employe->id, $mois);
        $delisMois = $this->calculerDelisMois($employe, $mois);

        $salaireBase = $salaire?->somme ?? 0;
        $montantAvance = $avanceSalaire?->sommeAs ?? 0;
        $montantDelis = $delisMois['montant_total'];

        $salaireNet = $salaireBase;
        $salaireNet -= $montantAvance;
        $salaireNet -= $deductions['manquants'];
        $salaireNet -= $deductions['remboursement'];
        $salaireNet -= $deductions['caisse_sociale'];
        $salaireNet -= $montantDelis;

        return [
            'salaire_base' => $salaireBase,
            'avance_salaire' => $montantAvance,
            'manquants' => $deductions['manquants'],
            'remboursement' => $deductions['remboursement'],
            'pret' => $deductions['pret'],
            'caisse_sociale' => $deductions['caisse_sociale'],
            'delis' => [
                'montant' => $montantDelis,
                'details' => $delisMois['details']
            ],
            'salaire_net' => $salaireNet
        ];
    }
}
