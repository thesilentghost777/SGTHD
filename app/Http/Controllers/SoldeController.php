<?php

namespace App\Http\Controllers;

use App\Models\Complexe;

use App\Models\Salaire;

use App\Models\AvanceSalaire;

use App\Models\Deli;

use App\Models\ACouper;

use App\Models\Prime;

use App\Models\Transaction;

use App\Models\BagTransaction;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class SoldeController extends Controller

{

    public function index()

    {

        $currentMonth = Carbon::now()->month;

        $currentYear = Carbon::now()->year;

        // Données du complexe

        $complexe = Complexe::first();

        $soldeTotalEntreprise = $complexe ? $complexe->solde : 0;

        $caisseSociale = $complexe ? $complexe->caisse_sociale : 0;

        // Calcul des dépenses totales (transactions de type 'outcome')

        $depensesTotales = Transaction::where('type', 'outcome')

            ->sum('amount');

        // Salaires du mois courant

        $salairesTotauxMois = Salaire::whereMonth('mois_salaire', $currentMonth)

            ->whereYear('mois_salaire', $currentYear)

            ->sum('somme');

        // Avances salaires du mois courant

        $avancesTotalesMois = AvanceSalaire::whereMonth('mois_as', $currentMonth)

            ->whereYear('mois_as', $currentYear)

            ->sum('sommeAs');

        // Manquants et délis du mois courant

        $Delis = DB::table('deli_user')

            ->join('delis', 'deli_user.deli_id', '=', 'delis.id')

            ->whereMonth('date_incident', $currentMonth)

            ->whereYear('date_incident', $currentYear)

            ->sum('delis.montant');

        // Primes du mois courant

        $primesTotalesMois = Prime::whereMonth('created_at', $currentMonth)

            ->whereYear('created_at', $currentYear)

            ->sum('montant');

        // Ventes de sacs

        $ventesSacsMois = BagTransaction::where('type', 'sold')

            ->whereMonth('transaction_date', $currentMonth)

            ->whereYear('transaction_date', $currentYear)

            ->sum('quantity');

        // Statistiques mensuelles sur l'année en cours

        $statsParMois = collect(range(1, 12))->map(function($mois) use ($currentYear) {

            return [

                'mois' => Carbon::create()->month($mois)->format('F'),

                'salaires' => Salaire::whereMonth('mois_salaire', $mois)

                    ->whereYear('mois_salaire', $currentYear)

                    ->sum('somme'),

                'avances' => AvanceSalaire::whereMonth('mois_as', $mois)

                    ->whereYear('mois_as', $currentYear)

                    ->sum('sommeAs'),

                'delis' => DB::table('deli_user')

                    ->join('delis', 'deli_user.deli_id', '=', 'delis.id')

                    ->whereMonth('date_incident', $mois)

                    ->whereYear('date_incident', $currentYear)

                    ->sum('delis.montant'),

                'primes' => Prime::whereMonth('created_at', $mois)

                    ->whereYear('created_at', $currentYear)

                    ->sum('montant'),

                'ventes_sacs' => BagTransaction::where('type', 'sold')

                    ->whereMonth('transaction_date', $mois)

                    ->whereYear('transaction_date', $currentYear)

                    ->sum('quantity')

            ];

        });

        // Statistiques annuelles sur les 5 dernières années

        $statsParAnnee = collect(range(0, 4))->map(function($yearsAgo) {

            $year = Carbon::now()->subYears($yearsAgo)->year;

            return [

                'annee' => $year,

                'salaires' => Salaire::whereYear('mois_salaire', $year)->sum('somme'),

                'avances' => AvanceSalaire::whereYear('mois_as', $year)->sum('sommeAs'),

                'delis' => DB::table('deli_user')

                    ->join('delis', 'deli_user.deli_id', '=', 'delis.id')

                    ->whereYear('date_incident', $year)

                    ->sum('delis.montant'),

                'primes' => Prime::whereYear('created_at', $year)->sum('montant'),

                'ventes_sacs' => BagTransaction::where('type', 'sold')

                    ->whereYear('transaction_date', $year)

                    ->sum('quantity')

            ];

        });

        $currentMonth = now()->format('Y-m');



        // Calcul du montant total des salaires prévisionnels

        $totalSalaires = Salaire::sum('somme');

        $totalPrimes = Prime::whereMonth('created_at', now()->month)->sum('montant');

        $totalManquants = Acouper::whereMonth('date', now()->month)->sum('manquants');

        $totalDelis = DB::table('deli_user')

            ->join('delis', 'deli_user.deli_id', '=', 'delis.id')

            ->whereMonth('date_incident', now()->month)

            ->sum('delis.montant');

        $totalAvances = AvanceSalaire::where('flag', true)

            ->whereMonth('mois_as', now()->month)

            ->sum('sommeAs');

        // Montant total à prévoir pour les salaires

        $montantPrevisionnel = $totalSalaires + $totalPrimes - $totalManquants - $totalDelis - $totalAvances;
        Log::info('montantPrevisionnel => ' . $montantPrevisionnel . ' TotalSalaires => ' . $totalSalaires . ' TotalPrimes => ' . $totalPrimes . ' Total AS => ' . $totalAvances . 'total manquants' . $totalManquants .'totalAvances' . $totalAvances);

        // Montant pour la caisse sociale

        $montantCaisseSociale = Acouper::whereMonth('date', now()->month)->sum('caisse_sociale');

        // Montant pour les enveloppes (montant prévisionnel - caisse sociale)

        $montantEnveloppes = $montantPrevisionnel - $montantCaisseSociale;

        // Statistiques des sacs

        $statsSacs = [
            'total_vendus' => BagTransaction::where('bag_transactions.type', 'sold')
                ->whereMonth('transaction_date', now()->month)
                ->sum('quantity'),
            'revenu_ventes' => BagTransaction::where('bag_transactions.type', 'sold')
                ->whereMonth('transaction_date', now()->month)
                ->join('transactions', 'bag_transactions.id', '=', 'transactions.id')
                ->sum('transactions.amount'),
            'evolution_mensuelle' => BagTransaction::where('bag_transactions.type', 'sold')
                ->select(DB::raw('MONTH(transaction_date) as mois'), DB::raw('SUM(quantity) as total'))
                ->whereYear('transaction_date', now()->year)
                ->groupBy('mois')
                ->get()
        ];

        // Statistiques générales

        $statsGenerales = [

            'solde_total' => Complexe::first()->solde ?? 0,

            'depenses_totales' => Transaction::where('type', 'outcome')

                ->whereMonth('date', now()->month)

                ->sum('amount'),

            'revenus_totaux' => Transaction::where('type', 'income')

                ->whereMonth('date', now()->month)

                ->sum('amount')

        ];

        $manquantsEtDelis =$totalManquants + $Delis;


        return view('solde.index', compact(
            'montantPrevisionnel',

            'montantCaisseSociale',

            'montantEnveloppes',

            'statsSacs',

            'statsGenerales',

            'soldeTotalEntreprise',

            'depensesTotales',

            'salairesTotauxMois',

            'avancesTotalesMois',

            'manquantsEtDelis',

            'primesTotalesMois',

            'caisseSociale',

            'ventesSacsMois',

            'statsParMois',

            'statsParAnnee'

        ));

    }

}
