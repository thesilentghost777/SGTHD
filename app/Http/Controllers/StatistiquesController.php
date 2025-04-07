<?php

namespace App\Http\Controllers;

use App\Models\Extra;
use App\Models\Horaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Matiere;
use App\Models\Production;
use App\Models\Produit_fixes;
use App\Models\Daily_assignments;
use App\Models\Production_suggerer_par_jour;
use App\Models\Utilisation;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\TransactionVente;
use App\Models\ProduitStock;
use App\Models\VersementCsg;
use App\Models\ProduitRecu;
use App\Models\BagTransaction;
use App\Models\AvanceSalaire;
use App\Models\Prime;
use App\Models\Deli;
use App\Models\Salaire;
use App\Models\MatiereRecommander;
use App\Models\ACouper;
use App\Services\RecipeCalculatorService;


class StatistiquesController extends Controller

{
    protected $recipeCalculator;

    public function __construct(RecipeCalculatorService $recipeCalculator)

    {

        $this->recipeCalculator = $recipeCalculator;

    }



    public function horaires()

    {

        // Récupérer les paramètres standards depuis Extra

        $standardHoraire = Extra::first();

        $heureArriveeStandard = $standardHoraire ? $standardHoraire->heure_arriver_adequat : '08:00:00';

        $heureDepartStandard = $standardHoraire ? $standardHoraire->heure_depart_adequat : '17:00:00';

        // Statistiques générales de ponctualité

        $tauxPonctualite = DB::table('Horaire')

            ->join('users', 'Horaire.employe', '=', 'users.id')

            ->select(

                'users.id',

                'users.name',

                'users.secteur',

                DB::raw('COUNT(*) as total_jours'),

                DB::raw("SUM(CASE WHEN TIME(arrive) <= '$heureArriveeStandard' THEN 1 ELSE 0 END) as jours_ponctuels"),

                DB::raw("ROUND((SUM(CASE WHEN TIME(arrive) <= '$heureArriveeStandard' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 2) as taux_ponctualite")

            )

            ->groupBy('users.id', 'users.name', 'users.secteur')

            ->get();

        // Statistiques d'absentéisme par secteur avec détails

        $absenteismeParSecteur = DB::table('users')

            ->leftJoin('Horaire', 'users.id', '=', 'Horaire.employe')

            ->select(

                'users.secteur',

                DB::raw('COUNT(DISTINCT users.id) as total_employes'),

                DB::raw('COUNT(DISTINCT CASE WHEN Horaire.id IS NULL THEN users.id END) as employes_absents'),

                DB::raw('ROUND((COUNT(DISTINCT CASE WHEN Horaire.id IS NULL THEN users.id END) * 100.0 / COUNT(DISTINCT users.id)), 2) as taux_absenteisme')

            )

            ->whereNotNull('users.secteur')

            ->groupBy('users.secteur')

            ->get();

        // Employé le plus absent par secteur

        $plusAbsentParSecteur = DB::table('users')

            ->leftJoin('Horaire', 'users.id', '=', 'Horaire.employe')

            ->select(

                'users.secteur',

                'users.name',

                DB::raw('COUNT(*) as nombre_absences')

            )

            ->whereNull('Horaire.id')

            ->groupBy('users.secteur', 'users.name')

            ->orderByRaw('COUNT(*) DESC')

            ->get()

            ->groupBy('secteur')

            ->map(function ($group) {

                return $group->first();

            });

        // Employé le plus ponctuel

        $plusPonctuel = DB::table('Horaire')

            ->join('users', 'Horaire.employe', '=', 'users.id')

            ->select(

                'users.name',

                DB::raw("ROUND((SUM(CASE WHEN TIME(arrive) <= '$heureArriveeStandard' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 2) as taux_ponctualite")

            )

            ->groupBy('users.name')

            ->orderBy('taux_ponctualite', 'DESC')

            ->first();

        // Statistiques d'arrivée et départ

        $statistiquesHoraires = DB::table('Horaire')
            ->join('users', 'Horaire.employe', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('MIN(TIME(arrive)) as arrive_plus_tot'),
                DB::raw('MAX(TIME(depart)) as depart_plus_tard'),
                DB::raw('AVG(HOUR(arrive)) as heure_arrivee_moyenne'),
                DB::raw('AVG(HOUR(depart)) as heure_depart_moyenne'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, arrive, depart)) as heures_travaillees'),
                DB::raw("AVG(CASE
                    WHEN TIMESTAMPDIFF(HOUR, '$heureArriveeStandard', depart) > 8
                    THEN TIMESTAMPDIFF(HOUR, '$heureArriveeStandard', depart) - 8
                    ELSE 0
                END) as heures_supplementaires")
            )
            ->groupBy('users.name')
            ->get();
        // Taux d'utilisation des congés
        $heuresSupp = DB::table('Horaire')
            ->join('users', 'Horaire.employe', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw("SUM(CASE
                    WHEN TIMESTAMPDIFF(HOUR, '$heureArriveeStandard', depart) > 8
                    THEN TIMESTAMPDIFF(HOUR, '$heureArriveeStandard', depart) - 8
                    ELSE 0
                END) as heures_supp_total")
            )
            ->groupBy('users.name')
            ->having('heures_supp_total', '>', 0)
            ->get();

        $tauxConges = DB::table('users')

            ->leftJoin('repos_conges', 'users.id', '=', 'repos_conges.employe_id')

            ->select(

                'users.secteur',

                'users.name',

                DB::raw('COUNT(repos_conges.id) as jours_conges'),

                DB::raw('(COUNT(repos_conges.id) * 100.0 / 30) as taux_utilisation_conges')

            )

            ->groupBy('users.secteur', 'users.name')

            ->get();

        // Statistiques globales par période

        $statistiquesGlobales = DB::table('Horaire')
        ->join('users', 'Horaire.employe', '=', 'users.id')
        ->select(
            'users.name',
            DB::raw('COUNT(DISTINCT DATE(arrive)) as jours_travailles'),
            DB::raw('SUM(TIMESTAMPDIFF(HOUR, arrive, depart)) as total_heures_travaillees'),
            DB::raw('AVG(TIMESTAMPDIFF(HOUR, arrive, depart)) as moyenne_heures_jour'),
            DB::raw("SUM(CASE
                WHEN TIME(arrive) > '$heureArriveeStandard'
                THEN TIMESTAMPDIFF(MINUTE, '$heureArriveeStandard', arrive)
                ELSE 0
            END) as total_minutes_retard"),
            DB::raw("ROUND((COUNT(CASE WHEN TIME(arrive) <= '$heureArriveeStandard' THEN 1 END) * 100.0 / COUNT(*)), 2) as taux_respect_horaires")
        )
        ->whereRaw('DATE(arrive) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)')
        ->groupBy('users.name')
        ->get();

    // Variation des horaires
    $variationHoraires = DB::table('Horaire')
        ->join('users', 'Horaire.employe', '=', 'users.id')
        ->select(
            'users.name',
            DB::raw('STDDEV(HOUR(arrive)) as variation_arrivee'),
            DB::raw('STDDEV(HOUR(depart)) as variation_depart')
        )
        ->groupBy('users.name')
        ->get();


        // Variation des horaires

        $variationHoraires = DB::table('Horaire')

            ->join('users', 'Horaire.employe', '=', 'users.id')

            ->select(

                'users.name',

                DB::raw('STDDEV(HOUR(arrive)) as variation_arrivee'),

                DB::raw('STDDEV(HOUR(depart)) as variation_depart')

            )

            ->groupBy('users.name')

            ->get();

                // Data for circular diagram - Monthly absence rate per employee

                $absenceRatePerEmployee = DB::table('users')

                ->leftJoin('Horaire', function($join) {

                    $join->on('users.id', '=', 'Horaire.employe')

                         ->whereMonth('Horaire.arrive', '=', now()->month);

                })

                ->select(

                    'users.name',

                    DB::raw('COUNT(Horaire.id) as days_present'),

                    DB::raw('(DAY(LAST_DAY(NOW())) - COUNT(Horaire.id)) as days_absent')

                )

                ->groupBy('users.id', 'users.name')

                ->having('days_absent', '>', 0)

                ->get();

            // Data for 3D work time diagram

            $workTimePerDay = DB::table('Horaire')

                ->select(

                    DB::raw('DATE(arrive) as work_date'),

                    DB::raw('AVG(TIMESTAMPDIFF(HOUR, arrive, depart)) as hours_worked')

                )

                ->whereMonth('arrive', '=', now()->month)

                ->groupBy('work_date')

                ->orderBy('work_date')

                ->get();

            // Monthly work evolution per employee

            $monthlyWorkEvolution = DB::table('Horaire')

                ->join('users', 'Horaire.employe', '=', 'users.id')

                ->select(

                    'users.name',

                    DB::raw('MONTH(arrive) as month'),

                    DB::raw('YEAR(arrive) as year'),

                    DB::raw('SUM(TIMESTAMPDIFF(HOUR, arrive, depart)) as total_hours')

                )

                ->whereYear('arrive', '=', now()->year)

                ->groupBy('users.name', 'month', 'year')

                ->orderBy('year')

                ->orderBy('month')

                ->get();

            // Monthly absence evolution

            $monthlyAbsenceEvolution = DB::table('users')

                ->crossJoin(DB::raw('(SELECT DISTINCT MONTH(arrive) as month FROM Horaire WHERE YEAR(arrive) = YEAR(CURRENT_DATE)) months'))

                ->leftJoin('Horaire', function($join) {

                    $join->on('users.id', '=', 'Horaire.employe')

                         ->whereRaw('MONTH(Horaire.arrive) = months.month')

                         ->whereYear('Horaire.arrive', '=', now()->year);

                })

                ->select(

                    DB::raw('months.month'),

                    DB::raw('COUNT(DISTINCT CASE WHEN Horaire.id IS NULL THEN users.id END) as absent_count')

                )

                ->groupBy('months.month')

                ->orderBy('months.month')

                ->get();

                return view('statistiques.stat_horaire', compact(
                    'tauxPonctualite',
                    'absenteismeParSecteur',
                    'plusAbsentParSecteur',
                    'plusPonctuel',
                    'statistiquesHoraires',
                    'tauxConges',
                    'statistiquesGlobales',
                    'variationHoraires',
                    'standardHoraire',
                    'absenceRatePerEmployee',
                    'workTimePerDay',
                    'monthlyWorkEvolution',
                    'monthlyAbsenceEvolution',
                    'heuresSupp',
                    'statistiquesGlobales',
                    'variationHoraires'
                ));
    }

    public function listeAbsences()
    {
        // Récupérer les jours de repos et de congés pour chaque employé
        $joursReposConges = DB::table('repos_conges')
            ->select('employe_id', 'jour', 'conges', 'debut_c', 'raison_c')
            ->get()
            ->keyBy('employe_id');

        // Calculer les jours d'absence pour chaque employé
        $absenceParEmploye = DB::table('users')
            ->leftJoin('Horaire', 'users.id', '=', 'Horaire.employe')
            ->select('users.id', 'users.name', 'users.secteur')
            ->selectRaw('DATE(arrive) as date_travail')
            ->groupBy('users.id', 'users.name', 'users.secteur', 'date_travail')
            ->havingRaw('COUNT(Horaire.id) = 0') // Pas d'entrée dans Horaire
            ->get()
            ->groupBy('id')
            ->map(function ($absences) use ($joursReposConges) {
                $employeId = $absences->first()->id;
                $reposConges = $joursReposConges->get($employeId);

                // Filtrer les absences en excluant les jours de repos et de congés
                $absencesReelles = $absences->filter(function ($absence) use ($reposConges) {
                    $jourSemaine = strtolower(Carbon::parse($absence->date_travail)->locale('fr')->dayName);

                    // Exclure les jours de repos
                    if ($reposConges && $reposConges->jour == $jourSemaine) {
                        return false;
                    }

                    // Exclure les jours de congés
                    if ($reposConges && $reposConges->conges &&
                        Carbon::parse($absence->date_travail)->between(
                            Carbon::parse($reposConges->debut_c),
                            Carbon::parse($reposConges->debut_c)->addDays($reposConges->conges)
                        )) {
                        return false;
                    }

                    return true;
                });

                return [
                    'name' => $absences->first()->name,
                    'secteur' => $absences->first()->secteur,
                    'nombre_absences' => $absencesReelles->count(),
                    'jours_absences' => $absencesReelles->pluck('date_travail')->toArray(),
                    'raison_conges' => $reposConges ? $reposConges->raison_c : null
                ];
            });

        return view('statistiques.absences', compact('absenceParEmploye'));
    }

    public function ventes()
    {
        // 1. Chiffre d'affaires par période
        $chiffreAffaires = [
            'journalier' => TransactionVente::whereDate('date_vente', Carbon::today())
                ->sum(DB::raw('prix * quantite')),
            'hebdomadaire' => TransactionVente::whereBetween('date_vente',
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum(DB::raw('prix * quantite')),
            'mensuel' => TransactionVente::whereMonth('date_vente', Carbon::now()->month)
                ->sum(DB::raw('prix * quantite'))
        ];

        $produitsPopulaires = TransactionVente::join('Produit_fixes', 'transaction_ventes.produit', '=', 'Produit_fixes.code_produit')

->select('Produit_fixes.nom as nom_produit',

    DB::raw('SUM(transaction_ventes.quantite) as total_vendu'))

->groupBy('Produit_fixes.code_produit', 'Produit_fixes.nom')

->orderByDesc('total_vendu')

->limit(5)

->get();

        // 3. État des stocks
        $stocks = ProduitStock::with('produit')
            ->select('id_produit', 'quantite_en_stock', 'quantite_invendu', 'quantite_avarie')
            ->get();


        // 5. Versements
        $versements = [
            'total' => VersementCsg::join('users', 'Versement_csg.verseur', '=', 'users.id') // Jointure avec la table des utilisateurs
                ->where('users.secteur', 'vente') // Filtre par secteur du verseur
                ->sum('Versement_csg.somme'), // Calcul du total des sommes

            'par_status' => VersementCsg::select('Versement_csg.status', DB::raw('COUNT(*) as total'))
                ->join('users', 'Versement_csg.verseur', '=', 'users.id') // Jointure avec la table des utilisateurs
                ->where('users.secteur', 'vente') // Filtre par secteur du verseur
                ->groupBy('Versement_csg.status')
                ->get()
        ];




        // 6. Performance serveurs
        $performanceServeurs = TransactionVente::select('serveur',
            DB::raw('SUM(prix * quantite) as chiffre_affaires'),
            DB::raw('COUNT(*) as nombre_ventes'))
            ->groupBy('serveur')
            ->with('user')
            ->get();

        // 7. Évolution temporelle
        $evolutionVentes = TransactionVente::select(
            DB::raw('DATE(date_vente) as date'),
            DB::raw('SUM(prix * quantite) as total')
        )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 8. Analyse paiements
        $methodePaiement = TransactionVente::select('monnaie',
            DB::raw('COUNT(*) as total'))
            ->groupBy('monnaie')
            ->get();

        // 9. Statistiques par période
        $statsParPeriode = [
            'mois' => TransactionVente::select(
                DB::raw('MONTH(date_vente) as mois'),
                DB::raw('SUM(prix * quantite) as total')
            )
                ->groupBy('mois')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
            'jours' => TransactionVente::select(
                DB::raw('DAYNAME(date_vente) as jour'),
                DB::raw('SUM(prix * quantite) as total')
            )
                ->groupBy('jour')
                ->get()
        ];

        // 10. Statistiques sacs
        $statsSacs = BagTransaction::select(
            'type',
            DB::raw('SUM(quantity) as total_quantity')
        )
            ->groupBy('type')
            ->get();

        // 1. Ruptures de stock potentielles
        $rupturesPotentielles = ProduitStock::select(
            'produit_stocks.id_produit',
         'produit_stocks.quantite_en_stock',
            DB::raw('AVG(transaction_ventes.quantite) as vente_moyenne')
        )
        ->leftJoin('transaction_ventes', 'produit_stocks.id_produit', '=', 'transaction_ventes.produit')
        ->groupBy('produit_stocks.id_produit', 'produit_stocks.quantite_en_stock') // Ajout de quantite_en_stock dans le GROUP BY
        ->havingRaw('quantite_en_stock < (vente_moyenne * 7)') // Alert si moins d'une semaine de stock
        ->get();


        // 2. Proportion produits reçus/vendus
$proportionRecuVendu = DB::table('Produit_recu')
->select(
    'Produit_recu.produit',
    'Produit_fixes.nom as nom_produit', // Utilisation du nom depuis Produit_fixes
    DB::raw('SUM(Produit_recu.quantite) as total_recu'),
    DB::raw('(SELECT SUM(quantite) FROM transaction_ventes WHERE produit = Produit_recu.produit) as total_vendu')
)
->join('Produit_fixes', 'Produit_recu.produit', '=', 'Produit_fixes.code_produit') // Jointure avec la bonne table
->groupBy('Produit_recu.produit', 'Produit_fixes.nom')
->get();


        // 3. Produits non rentables
        $produitsNonRentables = ProduitStock::select(
            'produit_stocks.id_produit',
            'produit_stocks.quantite_invendu',
            DB::raw('(produit_stocks.quantite_invendu + produit_stocks.quantite_avarie) /
                    (produit_stocks.quantite_en_stock + 0.1) * 100 as pourcentage_perte')
        )
        ->havingRaw('pourcentage_perte > 20') // Plus de 20% de pertes
        ->get();

        // 4. Coût matières premières
        $coutMatieresPremiere = ProduitRecu::select(
            DB::raw('SUM(prix * quantite) as cout_total'),
            DB::raw('DATE(date) as date')
        )
        ->groupBy('date')
        ->get();

        // 5. Évolution par année
        $evolutionAnnuelle = TransactionVente::select(
            DB::raw('YEAR(date_vente) as annee'),
            DB::raw('MONTH(date_vente) as mois'),
            DB::raw('SUM(prix * quantite) as total')
        )
        ->groupBy('annee', 'mois')
        ->orderBy('annee')
        ->orderBy('mois')
        ->get();

        // 6. Top 5 mois détaillé
        $top5Mois = TransactionVente::select(
            DB::raw('YEAR(date_vente) as annee'),
            DB::raw('MONTH(date_vente) as mois'),
            DB::raw('SUM(prix * quantite) as total'),
            DB::raw('COUNT(*) as nombre_ventes'),
            DB::raw('AVG(prix * quantite) as panier_moyen')
        )
        ->groupBy('annee', 'mois')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

        // Évolution versements
        $evolutionVersements = VersementCsg::select(
            DB::raw('DATE(date) as date'),
            DB::raw('SUM(somme) as total'),
            'status'
        )
        ->groupBy('date', 'status')
        ->orderBy('date')
        ->get();

        $topProduitsAvaries = DB::table('produit_stocks')
        ->join('Produit_fixes', 'produit_stocks.id_produit', '=', 'Produit_fixes.code_produit')
        ->select(
            'Produit_fixes.nom',
            'produit_stocks.quantite_avarie',
            'Produit_fixes.prix',
            DB::raw('(quantite_avarie * 100.0 / (quantite_en_stock + quantite_avarie)) as pourcentage_avarie'),
            DB::raw('(quantite_avarie * Produit_fixes.prix) as prix_total_avarie')
        )
        ->orderBy('prix_total_avarie', 'desc')
        ->limit(5)
        ->get();

// 2. Performance des Serveurs
$performanceServeurs = DB::table('transaction_ventes')
    ->join('users', 'transaction_ventes.serveur', '=', 'users.id')
    ->select(
        'users.name as nom_serveur',
        DB::raw('SUM(quantite) as total_ventes'),
        DB::raw('SUM(quantite * prix) as chiffre_affaires')
    )
    ->groupBy('users.id', 'users.name')
    ->orderBy('chiffre_affaires','desc')
    ->get();

// 3. Courbes de vente par mois
$ventesParMois = DB::table('transaction_ventes')
    ->join('Produit_fixes', 'transaction_ventes.produit', '=', 'Produit_fixes.code_produit')
    ->select(
        'Produit_fixes.nom as produit',
        DB::raw('MONTH(date_vente) as mois'),
        DB::raw('YEAR(date_vente) as annee'),
        DB::raw('SUM(quantite) as total_ventes')
    )
    ->whereYear('date_vente', '>=', now()->subYears(2))
    ->groupBy('Produit_fixes.code_produit', 'Produit_fixes.nom', 'mois', 'annee')
    ->orderBy('annee')
    ->orderBy('mois')
    ->get();

// 4. Statistiques Tendances
$tendances = DB::table('transaction_ventes')
    ->join('Produit_fixes', 'transaction_ventes.produit', '=', 'Produit_fixes.code_produit')
    ->select(
        'Produit_fixes.nom as produit',
        DB::raw('MONTH(date_vente) as mois'),
        DB::raw('DAYOFWEEK(date_vente) as jour_semaine'),
        DB::raw('AVG(quantite) as moyenne_ventes'),
        DB::raw('STDDEV(quantite) as ecart_type')
    )
    ->groupBy('Produit_fixes.code_produit', 'Produit_fixes.nom', 'mois', 'jour_semaine')
    ->get();

        return view('statistiques.ventes', compact(
            'chiffreAffaires',
            'produitsPopulaires',
            'stocks',
            'versements',
            'performanceServeurs',
            'evolutionVentes',
            'methodePaiement',
            'statsParPeriode',
            'statsSacs',
            'rupturesPotentielles',
            'proportionRecuVendu',
            'produitsNonRentables',
            'coutMatieresPremiere',
            'evolutionAnnuelle',
            'top5Mois',
            'evolutionVersements',
            'topProduitsAvaries',
            'performanceServeurs',
            'ventesParMois',
            'tendances'
        ));

    }


    /*finances*/
    public function finance()
    {
        // Statistiques journalières
        $statsJour = Transaction::whereDate('date', Carbon::today())
            ->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as revenus,
                SUM(CASE WHEN type = "outcome" THEN amount ELSE 0 END) as depenses,
                SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as solde
            ')
            ->first();

        // Statistiques hebdomadaires
        $statsHebdo = Transaction::whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as revenus,
                SUM(CASE WHEN type = "outcome" THEN amount ELSE 0 END) as depenses,
                SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as solde
            ')
            ->first();

        // Statistiques mensuelles
        $statsMois = Transaction::whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as revenus,
                SUM(CASE WHEN type = "outcome" THEN amount ELSE 0 END) as depenses,
                SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as solde
            ')
            ->first();

        // Statistiques annuelles
        $statsAnnee = Transaction::whereYear('date', Carbon::now()->year)
            ->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as revenus,
                SUM(CASE WHEN type = "outcome" THEN amount ELSE 0 END) as depenses,
                SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as solde
            ')
            ->first();

        // Evolution mensuelle sur l'année
        $evolutionMensuelle = Transaction::whereYear('date', Carbon::now()->year)
            ->selectRaw('
                MONTH(date) as mois,
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as revenus,
                SUM(CASE WHEN type = "outcome" THEN amount ELSE 0 END) as depenses,
                SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as solde
            ')
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // Top 5 des dépenses
        $topDepenses = Transaction::where('type', 'outcome')
            ->with('category')
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get();

        // Ratio dépenses/revenus mensuel
        $ratio = Transaction::whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->selectRaw('
                CASE
                    WHEN SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) > 0
                    THEN ROUND((SUM(CASE WHEN type = "outcome" THEN amount ELSE 0 END) /
                              SUM(CASE WHEN type = "income" THEN amount ELSE 0 END)) * 100, 2)
                    ELSE 0
                END as ratio
            ')
            ->first();

            $depensesParCategorie = Transaction::where('type', 'outcome')
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->select('categories.name', DB::raw('SUM(amount) as total'))
            ->get()
            ->pluck('total', 'name')
            ->toArray();

        // Évolution journalière sur le mois en cours
        $evolutionJournaliere = Transaction::whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->selectRaw('
                DATE(date) as jour,
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as revenus,
                SUM(CASE WHEN type = "outcome" THEN amount ELSE 0 END) as depenses
            ')
            ->groupBy('jour')
            ->orderBy('jour')
            ->get();

        return view('statistiques.finances', compact(
            'statsJour',
            'statsHebdo',
            'statsMois',
            'statsAnnee',
            'evolutionMensuelle',
            'topDepenses',
            'ratio',
             'depensesParCategorie',
            'evolutionJournaliere'
        ));
    }

    /*commande et sacs*/
    public function commande()
    {
        // Statistiques des commandes
        $orderStats = [
            'total' => DB::table('Commande')->count(),
            'validated' => DB::table('Commande')->where('valider', true)->count(),
            'pending' => DB::table('Commande')->where('valider', false)->count(),
            'monthlyOrders' => DB::table('Commande')
                ->select(DB::raw('DATE_FORMAT(date_commande, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'categoryDistribution' => DB::table('Commande')
                ->select('categorie', DB::raw('COUNT(*) as count'))
                ->groupBy('categorie')
                ->get(),
        ];

        // Statistiques des sacs
        $bagStats = [
            'totalBags' => DB::table('bags')->count(),
            'totalValue' => DB::table('bags')
                ->select(DB::raw('SUM(price * stock_quantity) as total_value'))
                ->first(),
            'lowStock' => DB::table('bags')
                ->whereRaw('stock_quantity <= alert_threshold')
                ->count(),
            'transactions' => DB::table('bag_transactions')
                ->select(
                    'transaction_date',
                    DB::raw('SUM(CASE WHEN type = "received" THEN quantity ELSE -quantity END) as net_quantity')
                )
                ->groupBy('transaction_date')
                ->orderBy('transaction_date')
                ->get(),
            'mostPopular' => DB::table('bags')
                ->join('bag_transactions', 'bags.id', '=', 'bag_transactions.bag_id')
                ->where('bag_transactions.type', 'sold')
                ->select('bags.name', DB::raw('SUM(bag_transactions.quantity) as total_sold'))
                ->groupBy('bags.id', 'bags.name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get(),
        ];

        return view('statistiques.commande', compact('orderStats', 'bagStats'));
    }
    //autre
    public function autre()
    {
        // Statistiques des dépenses
        $depenseStats = [
            'total' => DB::table('depenses')->sum('prix'),
            'count' => DB::table('depenses')->count(),
            'byType' => DB::table('depenses')
                ->select('type', DB::raw('SUM(prix) as total'))
                ->groupBy('type')
                ->get(),
            'monthly' => DB::table('depenses')
                ->select(DB::raw('DATE_FORMAT(date, "%Y-%m") as month'), DB::raw('SUM(prix) as total'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];

        // Statistiques des retenues sur salaire
        $retenueStats = [
            'totalManquants' => DB::table('Acouper')->sum('manquants'),
            'totalRemboursements' => DB::table('Acouper')->sum('remboursement'),
            'totalPrets' => DB::table('Acouper')->sum('pret'),
            'totalCaisseSociale' => DB::table('Acouper')->sum('caisse_sociale'),
            'employesManquants' => DB::table('Acouper')
                ->join('users', 'Acouper.id_employe', '=', 'users.id')
                ->select('users.name', DB::raw('SUM(manquants) as total_manquants'))
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_manquants')
                ->limit(5)
                ->get(),
        ];

        // Statistiques des primes
        $primeStats = [
            'totalPrimes' => DB::table('Prime')->sum('montant'),
            'avgPrime' => DB::table('Prime')->avg('montant'),
            'distribution' => DB::table('Prime')
                ->join('users', 'Prime.id_employe', '=', 'users.id')
                ->select('users.name', DB::raw('SUM(montant) as total_primes'))
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_primes')
                ->get(),
        ];

        // Statistiques des congés
        $congeStats = [
            'joursRepos' => DB::table('repos_conges')
                ->select('jour', DB::raw('COUNT(*) as count'))
                ->groupBy('jour')
                ->get(),
            'raisonConges' => DB::table('repos_conges')
                ->whereNotNull('raison_c')
                ->select('raison_c', DB::raw('COUNT(*) as count'))
                ->groupBy('raison_c')
                ->get(),
            'congesEnCours' => DB::table('repos_conges')
                ->join('users', 'repos_conges.employe_id', '=', 'users.id')
                ->whereNotNull('debut_c')
                ->whereRaw('DATE_ADD(debut_c, INTERVAL conges DAY) >= CURDATE()')
                ->select('users.name', 'debut_c', 'conges', 'raison_c')
                ->get(),
        ];

        // Statistiques des délits
        $deliStats = [
            'totalDelits' => DB::table('delis')->count(),
            'montantTotal' => DB::table('delis')->sum('montant'),
            'incidentsByMonth' => DB::table('deli_user')
                ->select(DB::raw('DATE_FORMAT(date_incident, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'topDelits' => DB::table('delis')
                ->select('nom', DB::raw('COUNT(deli_user.id) as count'))
                ->leftJoin('deli_user', 'delis.id', '=', 'deli_user.deli_id')
                ->groupBy('delis.id', 'delis.nom')
                ->orderByDesc('count')
                ->limit(5)
                ->get(),
        ];

        // Statistiques des salaires
        $salaireStats = [
            'totalMensuel' => DB::table('salaires')->sum('somme'),
            'moyenneSalaire' => DB::table('salaires')->avg('somme'),
            'distribution' => DB::table('salaires')
                ->join('users', 'salaires.id_employe', '=', 'users.id')
                ->select(
                    'users.name',
                    'salaires.somme',
                )
                ->orderByDesc('salaires.somme')
                ->get(),
        ];

        return view('statistiques.autres', compact(
            'depenseStats',
            'retenueStats',
            'primeStats',
            'congeStats',
            'deliStats',
            'salaireStats'
        ));
    }
    /*stagiere*/
    public function stagiere()

    {

        // Statistiques générales

        $generalStats = [

            'total' => DB::table('stagiaires')->count(),

            'actifs' => DB::table('stagiaires')

                ->whereDate('date_fin', '>=', Carbon::now())

                ->count(),

            'totalRemuneration' => DB::table('stagiaires')->sum('remuneration'),

            'moyenneRemuneration' => DB::table('stagiaires')->avg('remuneration'),

        ];

        // Répartition par type de stage

        $typeStats = DB::table('stagiaires')

            ->select('type_stage', DB::raw('COUNT(*) as total'))

            ->groupBy('type_stage')

            ->get();

        // Évolution mensuelle des arrivées

        $evolutionMensuelle = DB::table('stagiaires')

            ->select(DB::raw('DATE_FORMAT(date_debut, "%Y-%m") as mois'), DB::raw('COUNT(*) as total'))

            ->groupBy('mois')

            ->orderBy('mois')

            ->get();

        // Durée moyenne des stages par type

        $dureeMoyenne = DB::table('stagiaires')

            ->select('type_stage',

                DB::raw('AVG(DATEDIFF(date_fin, date_debut)) as duree_moyenne'))

            ->groupBy('type_stage')

            ->get();

        // Top 5 des appréciations

        $topAppreciations = DB::table('stagiaires')

            ->select('appreciation', DB::raw('COUNT(*) as total'))

            ->whereNotNull('appreciation')

            ->groupBy('appreciation')

            ->orderByDesc('total')

            ->limit(5)

            ->get();

        // Répartition par département

        $departementStats = DB::table('stagiaires')

            ->select('departement', DB::raw('COUNT(*) as total'))

            ->groupBy('departement')

            ->get();

        return view('statistiques.stagiaire', compact(

            'generalStats',

            'typeStats',

            'evolutionMensuelle',

            'dureeMoyenne',

            'topAppreciations',

            'departementStats'

        ));

    }

    /*salaire et autres relatifs a l'argent*/
    public function salaire_argent()

    {

        // Statistiques des salaires

        $statsSalaires = [

            'total_salaires' => Salaire::where('flag', true)->sum('somme'),

            'salaires_en_attente' => Salaire::where('retrait_demande', true)

                                          ->where('retrait_valide', false)

                                          ->count(),

            'moyenne_salaires' => Salaire::where('flag', true)->avg('somme'),

        ];

        // Statistiques des avances

        $statsAvances = [

            'total_avances' => AvanceSalaire::where('flag', true)->sum('sommeAs'),

            'avances_en_cours' => AvanceSalaire::where('flag', true)

                                             ->where('retrait_valide', false)

                                             ->count(),

            'pourcentage_employes_avec_avance' => (AvanceSalaire::where('flag', true)->count() / User::count()) * 100,

        ];

        // Statistiques des primes

        $statsPrimes = [

            'total_primes' => Prime::sum('montant'),

            'moyenne_prime_par_employe' => Prime::avg('montant'),

            'primes_par_type' => Prime::select('libelle', DB::raw('count(*) as total'))

                                    ->groupBy('libelle')

                                    ->get(),

        ];

        // Statistiques des délis

        $statsDelis = [

            'total_delis' => Deli::count(),

            'montant_total_delis' => Deli::sum('montant'),

            'delis_par_mois' => DB::table('deli_user')

                                ->select(DB::raw('MONTH(date_incident) as mois'), DB::raw('count(*) as total'))

                                ->whereYear('date_incident', date('Y'))

                                ->groupBy('mois')

                                ->get(),

        ];

        // Statistiques des déductions

        $statsDeductions = [

            'total_manquants' => ACouper::sum('manquants'),

            'total_remboursements' => ACouper::sum('remboursement'),

            'total_prets' => ACouper::sum('pret'),

            'total_caisse_sociale' => ACouper::sum('caisse_sociale'),

        ];

        // Tendances mensuelles

        $tendances = [

            'salaires' => $this->getTendancesMensuelles('salaires', 'somme'),

            'avances' => $this->getTendancesMensuelles('avance_salaires', 'sommeAs'),

            'delis' => $this->getTendancesMensuelles('delis', 'montant'),

        ];

        return view('statistiques.argent', compact(

            'statsSalaires',

            'statsAvances',

            'statsPrimes',

            'statsDelis',

            'statsDeductions',

            'tendances'

        ));

    }

    private function getTendancesMensuelles($table, $colonne)

    {

        return DB::table($table)

            ->select(DB::raw('MONTH(created_at) as mois'), DB::raw("SUM($colonne) as total"))

            ->whereYear('created_at', date('Y'))

            ->groupBy('mois')

            ->orderBy('mois')

            ->get();

    }

    public function production()

    {

        return view('statistiques.production', [

            'productionEvolution' => $this->getProductionEvolution(),

            'topProfitableProducts' => $this->getTopProfitableProducts(),

            'leastProfitableProducts' => $this->getLeastProfitableProducts(),

            'wasteEvolution' => $this->getWasteEvolution(),

            'monthlyProfits' => $this->getMonthlyProfits(),

            'materialUsageFrequency' => $this->getMaterialUsageFrequency(),

            'staffStats' => $this->getStaffStats() ,

            'objectiveRealization' => $this->getObjectiveRealizationRate(),

            'employeeProductivity' => $this->getEmployeeProductivity(),

            'materialUsageRatio' => $this->getMaterialUsageRatio(),

            'topQuantityProducts' => $this->getTopProductsByQuantity(),

            'bottomQuantityProducts' => $this->getBottomProductsByQuantity(),

            'topPriceProducts' => $this->getTopProductsByPrice(),

            'bottomPriceProducts' => $this->getBottomProductsByPrice(),

            'assignmentCompletionRate' => $this->getAssignmentCompletionRate(),

            'productionByPeriod' => $this->getProductionByPeriod(),

            'materialYieldByProduct' => $this->getMaterialYieldByProduct(),

            'productionGapRate' => $this->getProductionGapRate()

        ]);

    }

    private function getProductionEvolution()
    {
        // Récupérer les données de production par date
        $utilisations = DB::table('Utilisation')
            ->select(
                DB::raw('DATE(created_at) as date'),
                'id_lot',
                'quantite_produit'
            )
            ->get()
            ->groupBy('date');

        $resultats = [];

        foreach ($utilisations as $date => $productions) {
            $lotsTraites = [];
            $totalProduction = 0;

            foreach ($productions as $production) {
                $idLot = $production->id_lot;

                // Si ce lot n'a pas encore été compté pour cette date
                if (!isset($lotsTraites[$idLot])) {
                    $lotsTraites[$idLot] = true;
                    $totalProduction += $production->quantite_produit;
                }
            }

            $resultats[] = [
                'date' => $date,
                'total_production' => $totalProduction
            ];
        }

        // Trier par date
        usort($resultats, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return collect($resultats);
    }


    private function getTopProfitableProducts($limit = 5)
    {
        $utilisations = DB::table('Utilisation as u')
            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')
            ->join('Matiere as m', 'u.matierep', '=', 'm.id')
            ->select(
                'u.id_lot',
                'p.code_produit',
                'p.nom',
                'p.prix',
                'u.quantite_produit',
                'm.prix_par_unite_minimale',
                'u.quantite_matiere'
            )
            ->get();

        $produitsStats = [];

        foreach ($utilisations as $utilisation) {
            $idProduit = $utilisation->code_produit;
            $idLot = $utilisation->id_lot;

            // Initialiser les statistiques pour ce produit s'il n'existe pas encore
            if (!isset($produitsStats[$idProduit])) {
                $produitsStats[$idProduit] = (object)[
                    'code_produit' => $utilisation->code_produit,
                    'nom' => $utilisation->nom,
                    'prix' => $utilisation->prix,
                    'quantite_totale' => 0,
                    'revenu_total' => 0,
                    'cout_matieres' => 0,
                    'benefice_brut' => 0,
                    'lots_traites' => []
                ];
            }

            // Si ce lot n'a pas encore été compté pour ce produit
            if (!isset($produitsStats[$idProduit]->lots_traites[$idLot])) {
                $produitsStats[$idProduit]->lots_traites[$idLot] = true;
                $produitsStats[$idProduit]->quantite_totale += $utilisation->quantite_produit;
                $produitsStats[$idProduit]->revenu_total += $utilisation->quantite_produit * $utilisation->prix;
            }

            // Ajouter le coût des matières (même si le lot a déjà été compté pour la quantité)
            $produitsStats[$idProduit]->cout_matieres +=
                $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale;
        }

        // Calculer le bénéfice brut pour chaque produit
        foreach ($produitsStats as &$produit) {
            $produit->benefice_brut = $produit->revenu_total - $produit->cout_matieres;
            unset($produit->lots_traites); // Supprimer le tableau de lots traités
        }

        // Trier par bénéfice brut et prendre les N plus profitables
        uasort($produitsStats, function($a, $b) {
            return $b->benefice_brut <=> $a->benefice_brut;
        });

        return collect(array_slice($produitsStats, 0, $limit, true))->values();
    }

    private function getLeastProfitableProducts($limit = 5)
    {
        $utilisations = DB::table('Utilisation as u')
            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')
            ->join('Matiere as m', 'u.matierep', '=', 'm.id')
            ->select(
                'u.id_lot',
                'p.code_produit',
                'p.nom',
                'p.prix',
                'u.quantite_produit',
                'm.prix_par_unite_minimale',
                'u.quantite_matiere'
            )
            ->get();

        $produitsStats = [];

        foreach ($utilisations as $utilisation) {
            $idProduit = $utilisation->code_produit;
            $idLot = $utilisation->id_lot;

            // Initialiser les statistiques pour ce produit s'il n'existe pas encore
            if (!isset($produitsStats[$idProduit])) {
                $produitsStats[$idProduit] = (object)[
                    'code_produit' => $utilisation->code_produit,
                    'nom' => $utilisation->nom,
                    'prix' => $utilisation->prix,
                    'quantite_totale' => 0,
                    'revenu_total' => 0,
                    'cout_matieres' => 0,
                    'benefice_brut' => 0,
                    'lots_traites' => []
                ];
            }

            // Si ce lot n'a pas encore été compté pour ce produit
            if (!isset($produitsStats[$idProduit]->lots_traites[$idLot])) {
                $produitsStats[$idProduit]->lots_traites[$idLot] = true;
                $produitsStats[$idProduit]->quantite_totale += $utilisation->quantite_produit;
                $produitsStats[$idProduit]->revenu_total += $utilisation->quantite_produit * $utilisation->prix;
            }

            // Ajouter le coût des matières (même si le lot a déjà été compté pour la quantité)
            $produitsStats[$idProduit]->cout_matieres +=
                $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale;
        }

        // Calculer le bénéfice brut pour chaque produit
        foreach ($produitsStats as &$produit) {
            $produit->benefice_brut = $produit->revenu_total - $produit->cout_matieres;
            unset($produit->lots_traites); // Supprimer le tableau de lots traités
        }

        // Trier par bénéfice brut (croissant) et prendre les N moins profitables
        uasort($produitsStats, function($a, $b) {
            return $a->benefice_brut <=> $b->benefice_brut;
        });

        return collect(array_slice($produitsStats, 0, $limit, true))->values();
    }

    private function getWasteEvolution()
    {
        $utilisations = Utilisation::with(['matierePremiere', 'produitFixe'])

            ->get()

            ->groupBy(function($item) {

                return $item->created_at->format('Y-m-d');

            });

        $wasteData = [];

        foreach ($utilisations as $date => $dayUtilisations) {

            $totalWaste = 0;

            foreach ($dayUtilisations as $utilisation) {

                $recommended = Matiererecommander::where('produit', $utilisation->produit)

                    ->where('matierep', $utilisation->matierep)

                    ->first();

                if ($recommended) {

                    $recommendedQuantity = $this->recipeCalculator->calculateIngredientsForQuantity(

                        1,

                        $utilisation->quantite_produit,

                        $recommended->quantite

                    );

                    $waste = $utilisation->quantite_matiere - $recommendedQuantity;

                    $totalWaste += $waste;

                }

            }

            $wasteData[] = [

                'date' => $date,

                'waste' => $totalWaste

            ];

        }

        return $wasteData;

    }

    private function getMonthlyProfits()
    {
        // Récupérer les données d'utilisation avec les informations de produit et matière
        $utilisations = DB::table('Utilisation as u')
            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')
            ->join('Matiere as m', 'u.matierep', '=', 'm.id')
            ->select(
                DB::raw('YEAR(u.created_at) as year'),
                DB::raw('MONTH(u.created_at) as month'),
                'u.id_lot',
                'p.nom as nom_produit',
                'p.prix as prix_produit',
                'u.quantite_produit',
                'm.nom as nom_matiere',
                'm.prix_par_unite_minimale',
                'u.quantite_matiere',
                'u.unite_matiere'
            )
            ->orderBy('u.id_lot')
            ->get();

        // Regrouper d'abord par mois et par année
        $profitsParMois = [];

        foreach ($utilisations as $utilisation) {
            $annee = $utilisation->year;
            $mois = $utilisation->month;
            $idLot = $utilisation->id_lot;
            $cleMois = "$annee-$mois";

            // Initialiser le mois s'il n'existe pas encore
            if (!isset($profitsParMois[$cleMois])) {
                $profitsParMois[$cleMois] = [
                    'year' => $annee,
                    'month' => $mois,
                    'lots' => [],
                    'total_revenus' => 0,
                    'total_couts' => 0,
                    'profit' => 0
                ];
            }

            // Initialiser le lot pour ce mois s'il n'existe pas encore
            if (!isset($profitsParMois[$cleMois]['lots'][$idLot])) {
                $profitsParMois[$cleMois]['lots'][$idLot] = [
                    'quantite_produit' => $utilisation->quantite_produit,
                    'prix_unitaire' => $utilisation->prix_produit,
                    'valeur_production' => $utilisation->quantite_produit * $utilisation->prix_produit,
                    'cout_matieres' => 0
                ];

                // Ajouter la valeur de production au total des revenus du mois
                $profitsParMois[$cleMois]['total_revenus'] +=
                    $utilisation->quantite_produit * $utilisation->prix_produit;
            }

            // Ajouter le coût de la matière à ce lot et au total des coûts du mois
            $coutMatiere = $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale;
            $profitsParMois[$cleMois]['lots'][$idLot]['cout_matieres'] += $coutMatiere;
            $profitsParMois[$cleMois]['total_couts'] += $coutMatiere;
        }

        // Calculer le profit net pour chaque mois
        foreach ($profitsParMois as $cleMois => &$mois) {
            $mois['profit'] = $mois['total_revenus'] - $mois['total_couts'];
            unset($mois['lots']);
            unset($mois['total_revenus']);
            unset($mois['total_couts']);
        }

        // Trier par année et mois
        uasort($profitsParMois, function($a, $b) {
            if ($a['year'] != $b['year']) {
                return $a['year'] <=> $b['year'];
            }
            return $a['month'] <=> $b['month'];
        });

        // Calculer le pourcentage de changement
        $result = [];
        $previousProfit = null;

        foreach ($profitsParMois as $mois) {
            $percentageChange = null;
            if ($previousProfit !== null && $previousProfit != 0) {
                $percentageChange = (($mois['profit'] - $previousProfit) / abs($previousProfit)) * 100;
            }

            $result[] = (object)[
                'year' => $mois['year'],
                'month' => $mois['month'],
                'profit' => $mois['profit'],
                'percentage_change' => $percentageChange
            ];

            $previousProfit = $mois['profit'];
        }

        return $result;
    }


    private function getMaterialUsageFrequency()

    {

        return DB::table('Utilisation')

            ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')

            ->select('Matiere.nom', DB::raw('COUNT(*) as frequency'))

            ->groupBy('Matiere.id', 'Matiere.nom')

            ->orderBy('frequency', 'desc')

            ->get();

    }

    private function getStaffStats()

    {

        return [

'total_producteurs' => User::where('role', 'boulanger')
                          ->orWhere('role', 'patissier')
                          ->count(),
            'patissiers' => User::where('role', 'patissier')->count(),

            'boulangers' => User::where('role', 'boulanger')->count()

        ];

    }
    private function getObjectiveRealizationRate()

    {

        $data = DB::table('Daily_assignments as da')

            ->join('Utilisation as u', function($join) {

                $join->on('u.produit', '=', 'da.produit')

                    ->whereRaw('DATE(u.created_at) = da.assignment_date');

            })

            ->select(

                'da.assignment_date',

                DB::raw('SUM(da.expected_quantity) as expected'),

                DB::raw('SUM(u.quantite_produit) as realized'),

                DB::raw('(SUM(u.quantite_produit) / SUM(da.expected_quantity)) * 100 as realization_rate')

            )

            ->groupBy('da.assignment_date')

            ->orderBy('da.assignment_date')

            ->get();

        return $data;

    }

    private function getEmployeeProductivity()

    {

        return DB::table('Utilisation')

            ->join('users', 'Utilisation.producteur', '=', 'users.id')

            ->select(

                'users.name',

                DB::raw('DATE(Utilisation.created_at) as date'),

                DB::raw('SUM(quantite_produit) as total_production')

            )

            ->groupBy('users.id', 'users.name', 'date')

            ->orderBy('date')

            ->get();

    }

    private function getMaterialUsageRatio()

    {

        return DB::table('Utilisation as u')

            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')

            ->select(

                'p.nom',

                DB::raw('SUM(u.quantite_produit) as total_production'),

                DB::raw('SUM(u.quantite_matiere) as total_material_used'),

                DB::raw('SUM(u.quantite_produit) / SUM(u.quantite_matiere) as usage_ratio')

            )

            ->groupBy('p.code_produit', 'p.nom')

            ->get();

    }

    private function getTopProductsByQuantity($limit = 5)

    {

        return DB::table('Utilisation as u')

            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')

            ->select('p.nom', DB::raw('SUM(u.quantite_produit) as total_quantity'))

            ->groupBy('p.code_produit', 'p.nom')

            ->orderBy('total_quantity', 'desc')

            ->limit($limit)

            ->get();

    }

    private function getBottomProductsByQuantity($limit = 5)

    {

        return DB::table('Utilisation as u')

            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')

            ->select('p.nom', DB::raw('SUM(u.quantite_produit) as total_quantity'))

            ->groupBy('p.code_produit', 'p.nom')

            ->orderBy('total_quantity', 'asc')

            ->limit($limit)

            ->get();

    }

    private function getTopProductsByPrice($limit = 5)

    {

        return DB::table('Produit_fixes')

            ->select('nom', 'prix')

            ->orderBy('prix', 'desc')

            ->limit($limit)

            ->get();

    }

    private function getBottomProductsByPrice($limit = 5)

    {

        return DB::table('Produit_fixes')

            ->select('nom', 'prix')

            ->orderBy('prix', 'asc')

            ->limit($limit)

            ->get();

    }

    private function getAssignmentCompletionRate()

    {

        return DB::table('Daily_assignments')

            ->select(

                'assignment_date',

                DB::raw('COUNT(*) as total_assignments'),

                DB::raw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as completed_assignments'),

                DB::raw('(SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as completion_rate')

            )

            ->groupBy('assignment_date')

            ->orderBy('assignment_date')

            ->get();

    }

    private function getProductionByPeriod()

    {

        return DB::table('Utilisation as u')

            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')

            ->select(

                'p.nom',

                DB::raw('DATE(u.created_at) as date'),

                DB::raw('SUM(u.quantite_produit) as quantity')

            )

            ->groupBy('p.code_produit', 'p.nom', 'date')

            ->orderBy('date')

            ->get();

    }

    private function getMaterialYieldByProduct()

    {

        return DB::table('Utilisation as u')

            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')

            ->join('Matiere_recommander as mr', function($join) {

                $join->on('u.produit', '=', 'mr.produit')

                    ->on('u.matierep', '=', 'mr.matierep');

            })

            ->select(

                'p.nom',

                DB::raw('SUM(u.quantite_produit) as total_production'),

                DB::raw('SUM(u.quantite_matiere) as total_material_used'),

                DB::raw('(SUM(u.quantite_produit) / SUM(u.quantite_matiere)) * 100 as yield_rate')

            )

            ->groupBy('p.code_produit', 'p.nom')

            ->get();

    }

    private function getProductionGapRate()

    {

        return DB::table('Daily_assignments as da')

            ->leftJoin('Utilisation as u', function($join) {

                $join->on('u.produit', '=', 'da.produit')

                    ->whereRaw('DATE(u.created_at) = da.assignment_date');

            })

            ->select(

                'da.assignment_date',

                DB::raw('SUM(da.expected_quantity) as expected'),

                DB::raw('COALESCE(SUM(u.quantite_produit), 0) as actual'),

                DB::raw('((COALESCE(SUM(u.quantite_produit), 0) - SUM(da.expected_quantity)) / SUM(da.expected_quantity)) * 100 as gap_rate')

            )

            ->groupBy('da.assignment_date')

            ->orderBy('da.assignment_date')

            ->get();

    }

}
