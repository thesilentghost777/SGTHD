<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SalaireCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FichePaieController extends Controller
{
    private $salaireCalculator;

    public function __construct(SalaireCalculator $salaireCalculator)
    {
        $this->salaireCalculator = $salaireCalculator;
    }

    public function show(Request $request)
    {
        $mois = $request->get('mois')
            ? Carbon::createFromFormat('Y-m', $request->get('mois'))
            : Carbon::now()->startOfMonth();

        $employe = auth()->user();
        $fichePaie = $this->salaireCalculator->calculerFichePaie($employe, $mois);

        return view('pages.fiche-paie.show', [
            'employe' => $employe,
            'fichePaie' => $fichePaie,
            'mois' => $mois
        ]);
    }
}
