<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Prime;
use App\Models\Commande;
use App\Models\ACouper;
use App\Models\Salaire;
use App\Models\AvanceSalaire;
use App\Models\Evaluation;
use App\Models\ReposConge;
use App\Models\DeliUser;
use App\Models\Depense;
use App\Models\Deli;
use App\Models\Horaire;
use App\Models\Produit_fixes;
use App\Models\TransactionVente;
use App\Models\Transaction;
use App\Models\VersementChef;
use App\Models\VersementCsg;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class RapportsController extends Controller
{
    public function index()
    {
        // Récupérer tous les employés
        $employees = User::whereNotNull('role')->orderBy('name')->get();

        return view('rapports.index', compact('employees'));
    }

    public function select()
    {
        $employes = User::all();
        return view('rapports.select', compact('employes'));
    }

    public function genererRapport(Request $request, $id)
    {
        // Récupérer l'employé
        $employee = User::findOrFail($id);

        // Date de début et de fin du mois courant
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Données communes pour tous les employés
        $data = [
            'employee' => $employee,
            'month' => Carbon::now()->format('F Y'),
            'anneeService' => $employee->annee_debut_service
                ? Carbon::now()->year - $employee->annee_debut_service
                : 'Non spécifié',
            'dateNaissance' => $employee->date_naissance
                ? Carbon::parse($employee->date_naissance)->format('d/m/Y')
                : 'Non spécifiée',
            'age' => $employee->date_naissance
                ? Carbon::parse($employee->date_naissance)->age
                : 'Non spécifié',
        ];

        // Récupérer l'avance sur salaire pour le mois courant
        $avanceSalaire = AvanceSalaire::where('id_employe', $id)
            ->whereYear('mois_as', $startOfMonth->year)
            ->whereMonth('mois_as', $startOfMonth->month)
            ->first();

        $data['avanceSalaire'] = $avanceSalaire ? $avanceSalaire->sommeAs : 0;

        // Récupérer le salaire pour le mois courant
        $salaire = Salaire::where('id_employe', $id)
            ->whereYear('mois_salaire', $startOfMonth->year)
            ->whereMonth('mois_salaire', $startOfMonth->month)
            ->first();

        $data['salaire'] = $salaire ? $salaire->somme : 0;

        // Récupérer les évaluations
        $evaluation = Evaluation::where('user_id', $id)
            ->latest()
            ->first();

        $data['evaluation'] = $evaluation ?? null;

        // Récupérer les primes
        $primes = Prime::where('id_employe', $id)
            ->whereDate('created_at', '>=', $startOfMonth)
            ->whereDate('created_at', '<=', $endOfMonth)
            ->get();

        $data['primes'] = $primes;
        $data['totalPrimes'] = $primes->sum('montant');

        // Récupérer les éléments à couper du salaire
        $acouper = ACouper::where('id_employe', $id)
            ->whereYear('date', $startOfMonth->year)
            ->whereMonth('date', $startOfMonth->month)
            ->first();

        $data['acouper'] = $aCouper ?? null;

        // Récupérer les jours de repos et congés
        $reposConge = ReposConge::where('employe_id', $id)->first();
        $data['reposConge'] = $reposConge ?? null;

        // Récupérer les délits
        $delits = DeliUser::where('user_id', $id)
            ->whereYear('date_incident', $startOfMonth->year)
            ->whereMonth('date_incident', $startOfMonth->month)
            ->with('deli')
            ->get();

        $data['delits'] = $delits;
        $data['totalDelits'] = $delits->sum(function($item) {
            return $item->deli->montant;
        });

        // Récupérer les horaires du mois
        $horaires = Horaire::where('employe', $id)
            ->whereDate('arrive', '>=', $startOfMonth)
            ->whereDate('arrive', '<=', $endOfMonth)
            ->get();

        $data['horaires'] = $horaires;
        $data['joursPresence'] = $horaires->count();

        // Calcul du temps de travail total en heures
        $totalHeures = 0;
        foreach($horaires as $horaire) {
            if ($horaire->arrive && $horaire->depart) {
                $arrive = Carbon::parse($horaire->arrive);
                $depart = Carbon::parse($horaire->depart);
                $totalHeures += $arrive->diffInHours($depart);
            }
        }
        $data['totalHeuresTravail'] = $totalHeures;

        // Données spécifiques en fonction du rôle
        switch($employee->role) {
            case 'vendeur_boulangerie':
            case 'vendeur_patisserie':
                $this->prepareVendeurData($data, $id, $startOfMonth, $endOfMonth);
                break;

            case 'boulanger':
            case 'patissier':
                $this->prepareProducteurData($data, $id, $startOfMonth, $endOfMonth);
                break;

            // Autres rôles peuvent être ajoutés ici
        }

        // Génération du rapport
        if($request->has('format') && $request->format == 'pdf') {
            $pdf = app('dompdf.wrapper')->loadView('rapports.pdf', $data);
            return $pdf->download('rapport_'.$employee->name.'_'.Carbon::now()->format('Y_m').'.pdf');
        }

        return view('rapports.show', $data);
    }

    private function prepareVendeurData(&$data, $employeeId, $startOfMonth, $endOfMonth)
    {
        // Récupérer le chiffre d'affaires du vendeur pour le mois courant
        $transactions = TransactionVente::where('serveur', $employeeId)
            ->whereDate('date_vente', '>=', $startOfMonth)
            ->whereDate('date_vente', '<=', $endOfMonth)
            ->where('type', 'vente')
            ->get();

        $data['transactions'] = $transactions;
        $data['chiffreAffaires'] = $transactions->sum(function($transaction) {
            return $transaction->quantite * $transaction->prix;
        });

        $data['nbTransactions'] = $transactions->count();

        // Calculer la moyenne des ventes par jour
        $data['moyenneVentesParJour'] = $data['joursPresence'] > 0
            ? $data['chiffreAffaires'] / $data['joursPresence']
            : 0;
    }

    private function prepareProducteurData(&$data, $employeeId, $startOfMonth, $endOfMonth)
    {
        // Récupérer les productions du mois
        // Récupérer les productions du mois
$utilisations = DB::table('Utilisation')
->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
->where('Utilisation.producteur', $employeeId)
->whereDate('Utilisation.created_at', '>=', $startOfMonth)
->whereDate('Utilisation.created_at', '<=', $endOfMonth)
->select(
    'Utilisation.id_lot',
    'Produit_fixes.nom as nom_produit',
    'Produit_fixes.prix as prix_produit',
    'Utilisation.quantite_produit'
)
->groupBy('Utilisation.id_lot', 'Produit_fixes.nom', 'Produit_fixes.prix', 'Utilisation.quantite_produit')
->get();

// Traiter les données pour éviter de compter plusieurs fois la même production
$productionsParLot = [];
foreach ($utilisations as $utilisation) {
$idLot = $utilisation->id_lot;
if (!isset($productionsParLot[$idLot])) {
    $productionsParLot[$idLot] = [
        'id_lot' => $idLot,
        'nom_produit' => $utilisation->nom_produit,
        'prix_produit' => $utilisation->prix_produit,
        'quantite_produit' => $utilisation->quantite_produit,
        'valeur_production' => $utilisation->quantite_produit * $utilisation->prix_produit
    ];
}
}

// Convertir en collection pour utiliser les méthodes de collection Laravel
$productions = collect(array_values($productionsParLot));
$data['productions'] = $productions;
$data['valeurTotaleProduction'] = $productions->sum('valeur_production');

        // Récupérer le coût des matières premières
        $coutMatieres = DB::table('Utilisation')
            ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
            ->where('Utilisation.producteur', $employeeId)
            ->whereDate('Utilisation.created_at', '>=', $startOfMonth)
            ->whereDate('Utilisation.created_at', '<=', $endOfMonth)
            ->select(DB::raw('SUM(Utilisation.quantite_matiere * Matiere.prix_par_unite_minimale) as cout_total'))
            ->first();

        $data['coutMatieresPremieres'] = $coutMatieres ? $coutMatieres->cout_total : 0;

        // Calculer le ratio dépense/gain
        $data['ratioDepenseGain'] = $data['coutMatieresPremieres'] > 0
            ? $data['valeurTotaleProduction'] / $data['coutMatieresPremieres']
            : 0;
    }
    public function generatePdf($id)
    {
        $employe = User::findOrFail($id);

        // Récupérer les données financières
        $salaire = Salaire::where('id_employe', $id)->value('somme') ?? 0;
        $avanceSalaire = AvanceSalaire::where('id_employe', $id)
                            ->where('retrait_valide', true)
                            ->sum('sommeAs');

        // Récupérer les primes
        $primes = Prime::where('id_employe', $id)->get();
        $totalPrimes = $primes->sum('montant');

        // Récupérer les évaluations
        $evaluations = Evaluation::where('user_id', $id)->get();
        $noteAverage = $evaluations->avg('note') ?? 0;

        // Récupérer les congés
        $reposConge = ReposConge::where('employe_id', $id)->first();

        // Récupérer les présences
        $moisCourant = Carbon::now()->month;
        $presences = Horaire::where('employe', $id)
                     ->whereMonth('arrive', $moisCourant)
                     ->count();

        // Récupérer les incidents
        $incidents = DeliUser::where('user_id', $id)->with('deli')->get();

        // Récupérer les données à couper (maintenant défini même si vide)
        $acouper = Acouper::where('id_employe', $id)
                    ->orderBy('date', 'desc')
                    ->first();

        // Données spécifiques au rôle
        $donneesSecteur = [];

        // Si c'est un vendeur, récupérer les ventes
        if ($employe->role == 'vendeur' || $employe->secteur == 'vente') {
            // ... keep existing code (sales data collection)
        }

        // Si c'est un producteur (boulanger/pâtissier)
        if (in_array($employe->role, ['boulanger', 'pâtissier']) || $employe->secteur == 'production') {
            // ... keep existing code (production data collection)
        }

        $pdf = PDF::loadView('rapports.pdf', compact(
            'employe',
            'salaire',
            'avanceSalaire',
            'primes',
            'totalPrimes',
            'evaluations',
            'noteAverage',
            'reposConge',
            'presences',
            'incidents',
            'acouper',
            'donneesSecteur'
        ));

        // Changed from download() to stream() with headers to force download
        return $pdf->download('rapport_employe_' . $employe->id . '.pdf');

}

/**
     * Affiche le rapport global de production pour la direction.
     */
    public function productionGlobal()
    {
        // Définir le mois courant et le mois précédent
        $moisCourant = Carbon::now();
        $moisPrecedent = Carbon::now()->subMonth();

        // Noms des mois pour l'affichage
        $moisCourantNom = $this->getNomMoisFrancais($moisCourant->month);
        $moisPrecedentNom = $this->getNomMoisFrancais($moisPrecedent->month);

        // Récupérer toutes les utilisations du mois courant
        $utilisationsMoisCourant = DB::table('Utilisation as u')
            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')
            ->join('Matiere as m', 'u.matierep', '=', 'm.id')
            ->select(
                'u.id_lot',
                'p.code_produit',
                'p.nom as nom_produit',
                'p.prix',
                'u.quantite_produit',
                'm.id as id_matiere',
                'm.nom as nom_matiere',
                'm.prix_par_unite_minimale', // Changed from prix_unitaire to prix_par_unite_minimale
                'u.quantite_matiere',
                'u.unite_matiere',
                'u.producteur'
            )
            ->whereMonth('u.created_at', $moisCourant->month)
            ->whereYear('u.created_at', $moisCourant->year)
            ->get();

        // Traiter les données par lot pour éviter de compter plusieurs fois la même production
        $productionsParLotCourant = [];
        foreach ($utilisationsMoisCourant as $utilisation) {
            $idLot = $utilisation->id_lot;

            // Initialiser la production pour ce lot s'il n'existe pas encore
            if (!isset($productionsParLotCourant[$idLot])) {
                $productionsParLotCourant[$idLot] = [
                    'produit' => $utilisation->nom_produit,
                    'quantite_produit' => $utilisation->quantite_produit,
                    'prix_unitaire' => $utilisation->prix,
                    'valeur_production' => $utilisation->quantite_produit * $utilisation->prix,
                    'matieres' => [],
                    'cout_matieres' => 0,
                    'producteur' => $utilisation->producteur ?? null
                ];
            }

            // Ajouter les infos de matière première pour ce lot
            $coutMatiere = $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale;
            $productionsParLotCourant[$idLot]['matieres'][] = [
                'id' => $utilisation->id_matiere,
                'nom' => $utilisation->nom_matiere,
                'quantite' => $utilisation->quantite_matiere,
                'unite' => $utilisation->unite_matiere,
                'cout' => $coutMatiere
            ];

            // Ajouter le coût de cette matière
            $productionsParLotCourant[$idLot]['cout_matieres'] += $coutMatiere;
        }

        // Même chose pour le mois précédent
        $utilisationsMoisPrecedent = DB::table('Utilisation as u')
            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')
            ->join('Matiere as m', 'u.matierep', '=', 'm.id')
            ->select(
                'u.id_lot',
                'p.code_produit',
                'p.nom as nom_produit',
                'p.prix',
                'u.quantite_produit',
                'm.id as id_matiere',
                'm.nom as nom_matiere',
                'm.prix_par_unite_minimale', // Changed from prix_unitaire to prix_par_unite_minimale
                'u.quantite_matiere',
                'u.unite_matiere',
                'u.producteur'
            )
            ->whereMonth('u.created_at', $moisPrecedent->month)
            ->whereYear('u.created_at', $moisPrecedent->year)
            ->get();

        $productionsParLotPrecedent = [];
        foreach ($utilisationsMoisPrecedent as $utilisation) {
            $idLot = $utilisation->id_lot;

            if (!isset($productionsParLotPrecedent[$idLot])) {
                $productionsParLotPrecedent[$idLot] = [
                    'produit' => $utilisation->nom_produit,
                    'quantite_produit' => $utilisation->quantite_produit,
                    'prix_unitaire' => $utilisation->prix,
                    'valeur_production' => $utilisation->quantite_produit * $utilisation->prix,
                    'matieres' => [],
                    'cout_matieres' => 0,
                    'producteur' => $utilisation->producteur ?? null
                ];
            }

            // Calcul correct du coût matière
            $coutMatiere = $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale;
            $productionsParLotPrecedent[$idLot]['matieres'][] = [
                'id' => $utilisation->id_matiere,
                'nom' => $utilisation->nom_matiere,
                'quantite' => $utilisation->quantite_matiere,
                'unite' => $utilisation->unite_matiere,
                'cout' => $coutMatiere
            ];

            // Ajouter le coût de cette matière
            $productionsParLotPrecedent[$idLot]['cout_matieres'] += $coutMatiere;
        }

        // Calculer les sommes pour le mois courant
        $valeurTotaleProduction = collect($productionsParLotCourant)->sum('valeur_production');
        $coutMatierePremiere = collect($productionsParLotCourant)->sum('cout_matieres');
        $beneficeBrut = $valeurTotaleProduction - $coutMatierePremiere;

        // Calculer le bénéfice du mois précédent
        $valeurProductionPrecedent = collect($productionsParLotPrecedent)->sum('valeur_production');
        $coutMatierePrecedent = collect($productionsParLotPrecedent)->sum('cout_matieres');
        $beneficeMoisPrecedent = $valeurProductionPrecedent - $coutMatierePrecedent;

        // Calculer le pourcentage d'évolution
        $pourcentageEvolution = $beneficeMoisPrecedent > 0
            ? (($beneficeBrut - $beneficeMoisPrecedent) / $beneficeMoisPrecedent) * 100
            : 100; // Si le mois précédent n'avait pas de profit

        // Obtenir les données pour le diagramme circulaire (produits et leurs bénéfices)
        $produitsData = [];
        foreach ($productionsParLotCourant as $production) {
            $nomProduit = $production['produit'];
            $benefice = $production['valeur_production'] - $production['cout_matieres'];
            $revenu = $production['valeur_production'];
            $cout = $production['cout_matieres'];

            if (!isset($produitsData[$nomProduit])) {
                $produitsData[$nomProduit] = [
                    'nom' => $nomProduit,
                    'revenu' => 0,
                    'cout' => 0,
                    'benefice' => 0
                ];
            }

            $produitsData[$nomProduit]['revenu'] += $revenu;
            $produitsData[$nomProduit]['cout'] += $cout;
            $produitsData[$nomProduit]['benefice'] += $benefice;
        }

        // Trier et limiter aux 8 produits les plus rentables
        $produitsData = collect($produitsData)->sortByDesc('benefice')->take(8)->values();
        $produitsLabels = $produitsData->pluck('nom')->toArray();
        $produitsBenefices = $produitsData->pluck('benefice')->toArray();

        // Obtenir les 3 meilleurs producteurs du mois (en utilisant les IDs de producteurs des lots)
        $producteursData = [];
        foreach ($productionsParLotCourant as $production) {
            $producteurId = $production['producteur'];
            if (!$producteurId) continue;

            $benefice = $production['valeur_production'] - $production['cout_matieres'];

            if (!isset($producteursData[$producteurId])) {
                $producteursData[$producteurId] = [
                    'id' => $producteurId,
                    'benefice' => 0,
                    'produits' => []
                ];
            }

            $producteursData[$producteurId]['benefice'] += $benefice;

            // Ajouter les informations sur les produits pour ce producteur
            if (!isset($producteursData[$producteurId]['produits'][$production['produit']])) {
                $producteursData[$producteurId]['produits'][$production['produit']] = 0;
            }
            $producteursData[$producteurId]['produits'][$production['produit']] += $production['quantite_produit'];
        }

        // Récupérer les noms des producteurs depuis la base de données
        $producteurIds = array_keys($producteursData);
        $producteurInfos = DB::table('users')
            ->whereIn('id', $producteurIds)
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        // Compléter les informations des producteurs
        foreach ($producteursData as $id => &$data) {
            if (isset($producteurInfos[$id])) {
                $data['name'] = $producteurInfos[$id]->name;
            } else {
                $data['name'] = 'Inconnu';
            }

            // Trouver le produit phare (le plus produit en quantité)
            $produitPhare = '';
            $maxQuantite = 0;
            foreach ($data['produits'] as $produit => $quantite) {
                if ($quantite > $maxQuantite) {
                    $maxQuantite = $quantite;
                    $produitPhare = $produit;
                }
            }
            $data['produit_phare'] = $produitPhare ?: 'N/A';
        }

        // Trier et limiter aux 3 meilleurs producteurs
        $topProducteurs = collect($producteursData)
            ->sortByDesc('benefice')
            ->take(3)
            ->values();

        return view('rapports.production-global', compact(
            'valeurTotaleProduction',
            'coutMatierePremiere',
            'beneficeBrut',
            'moisCourantNom',
            'moisPrecedentNom',
            'pourcentageEvolution',
            'beneficeMoisPrecedent',
            'produitsLabels',
            'produitsBenefices',
            'topProducteurs'
        ));
    }

    /**
     * Renvoie le nom du mois en français
     */
    private function getNomMoisFrancais($numeroMois)
    {
        $mois = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        ];

        return $mois[$numeroMois] ?? '';
    }

    public function venteGlobal()
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Définir la période du mois précédent
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $previousMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Récupérer le chiffre d'affaires du mois courant (uniquement les ventes)
        $currentMonthSales = TransactionVente::whereBetween('date_vente', [$currentMonthStart, $currentMonthEnd])
            ->where('type', 'vente')
            ->sum(DB::raw('quantite * prix'));

        // Récupérer le chiffre d'affaires du mois précédent (uniquement les ventes)
        $previousMonthSales = TransactionVente::whereBetween('date_vente', [$previousMonthStart, $previousMonthEnd])
            ->where('type', 'vente')
            ->sum(DB::raw('quantite * prix'));

        // Calculer l'évolution en pourcentage
        $evolution = $previousMonthSales > 0
            ? round((($currentMonthSales - $previousMonthSales) / $previousMonthSales) * 100, 2)
            : 100;

        // Récupérer les pertes (produits avariés)
        $losses = TransactionVente::whereBetween('date_vente', [$currentMonthStart, $currentMonthEnd])
            ->where('type', 'avarie')
            ->sum(DB::raw('quantite * prix'));

        // Récupérer les produits par gains rapportés
        $productsByRevenue = TransactionVente::select(
                'produit',
                DB::raw('SUM(quantite * prix) as revenue')
            )
            ->whereBetween('date_vente', [$currentMonthStart, $currentMonthEnd])
            ->where('type', 'vente')
            ->groupBy('produit')
            ->orderBy('revenue', 'desc')
            ->get();

        // Récupérer les informations des produits
        $produitIds = $productsByRevenue->pluck('produit')->toArray();
        $produits = Produit_fixes::whereIn('code_produit', $produitIds)->get()->keyBy('code_produit');

        // Préparer les données pour le graphique en camembert
        $productRevenueData = [];
        foreach ($productsByRevenue as $product) {
            $produitId = $product->produit;
            if (isset($produits[$produitId])) {
                $productRevenueData[$produits[$produitId]->nom] = $product->revenue;
            } else {
                $productRevenueData["Produit #".$produitId] = $product->revenue;
            }
        }

        // Récupérer le top 3 des vendeurs
        $topSellers = TransactionVente::select(
                'serveur',
                DB::raw('SUM(quantite * prix) as revenue'),
                DB::raw('COUNT(*) as total_sales')
            )
            ->whereBetween('date_vente', [$currentMonthStart, $currentMonthEnd])
            ->where('type', 'vente')
            ->groupBy('serveur')
            ->orderBy('revenue', 'desc')
            ->take(3)
            ->get();

        // Récupérer les informations des vendeurs
        $vendeurIds = $topSellers->pluck('serveur')->toArray();
        $vendeurs = User::whereIn('id', $vendeurIds)->get()->keyBy('id');

        // Compléter les données des vendeurs
        foreach ($topSellers as $seller) {
            $seller->vendeur = isset($vendeurs[$seller->serveur]) ? $vendeurs[$seller->serveur] : null;
        }

        // Récupérer l'évolution journalière des ventes du mois courant
        $dailySales = TransactionVente::select(
                DB::raw('DATE(date_vente) as date'),
                DB::raw('SUM(quantite * prix) as revenue')
            )
            ->whereBetween('date_vente', [$currentMonthStart, $currentMonthEnd])
            ->where('type', 'vente')
            ->groupBy(DB::raw('DATE(date_vente)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d/m'),
                    'value' => $item->revenue
                ];
            });

        // Calculer les statistiques générales
        $totalTransactions = TransactionVente::whereBetween('date_vente', [$currentMonthStart, $currentMonthEnd])
            ->where('type', 'vente')
            ->count();

        $averageTransactionValue = $totalTransactions > 0
            ? $currentMonthSales / $totalTransactions
            : 0;

        // Récupérer les 5 dernières transactions
        $recentTransactions = TransactionVente::whereBetween('date_vente', [$currentMonthStart, $currentMonthEnd])
            ->where('type', 'vente')
            ->latest('date_vente')
            ->take(5)
            ->get();

        // Récupérer les produits pour les transactions récentes
        $recentProduitIds = $recentTransactions->pluck('produit')->toArray();
        $recentProduits = Produit_fixes::whereIn('code_produit', $recentProduitIds)->get()->keyBy('code_produit');

        // Récupérer les vendeurs pour les transactions récentes
        $recentVendeurIds = $recentTransactions->pluck('serveur')->toArray();
        $recentVendeurs = User::whereIn('id', $recentVendeurIds)->get()->keyBy('id');

        // Associer les produits et vendeurs aux transactions
        foreach ($recentTransactions as $transaction) {
            $transaction->produit_info = isset($recentProduits[$transaction->produit]) ? $recentProduits[$transaction->produit] : null;
            $transaction->vendeur_info = isset($recentVendeurs[$transaction->serveur]) ? $recentVendeurs[$transaction->serveur] : null;
        }

        return view('rapports.vente_global', compact(
            'currentMonthSales',
            'previousMonthSales',
            'evolution',
            'losses',
            'productRevenueData',
            'topSellers',
            'dailySales',
            'totalTransactions',
            'averageTransactionValue',
            'recentTransactions',
            'currentMonthStart'
        ));
    }

    public function avancesSalaire()
    {
        $currentDate = Carbon::now();
        $currentMonthName = $currentDate->locale('fr')->format('F Y');

        // Get avances for the current month
        $avances = AvanceSalaire::with('employe')
            ->whereMonth('mois_as', $currentDate->month)
            ->whereYear('mois_as', $currentDate->year)
            ->get();

        // Calculate various statistics
        $totalAvances = $avances->sum('sommeAs');
        $nombreAvances = $avances->count();
        $avancesValidees = $avances->where('retrait_valide', true)->count();
        $avancesEnAttente = $avances->where('retrait_demande', true)
                                   ->where('retrait_valide', false)
                                   ->count();
        $montantMoyen = $nombreAvances > 0 ? $totalAvances / $nombreAvances : 0;

        // Get the total from last month for comparison
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthTotal = AvanceSalaire::whereMonth('mois_as', $lastMonth->month)
            ->whereYear('mois_as', $lastMonth->year)
            ->sum('sommeAs');

        // Calculate evolution percentage
        $evolution = $lastMonthTotal > 0
            ? round((($totalAvances - $lastMonthTotal) / $lastMonthTotal) * 100, 1)
            : 0;

        return view('rapports.avances_salaire', compact(
            'avances',
            'totalAvances',
            'nombreAvances',
            'avancesValidees',
            'avancesEnAttente',
            'montantMoyen',
            'evolution',
            'currentMonthName'
        ));
    }

    public function salaires()
    {
        $currentDate = Carbon::now();
        $currentMonthName = $currentDate->locale('fr')->format('F Y');

        // Get salaires for the current month
        $salaires = Salaire::with('employe')
            ->whereMonth('mois_salaire', $currentDate->month)
            ->whereYear('mois_salaire', $currentDate->year)
            ->get();

        // Calculate various statistics
        $totalSalaires = $salaires->sum('somme');
        $nombreEmployes = $salaires->count();
        $salaireMoyen = $nombreEmployes > 0 ? $totalSalaires / $nombreEmployes : 0;

        // Get validation stats
        $salaireValides = $salaires->where('retrait_valide', true)->count();
        $salaireEnAttente = $salaires->where('retrait_demande', true)
                                    ->where('retrait_valide', false)
                                    ->count();
        $salaireNonTraites = $salaires->where('retrait_demande', false)
                                     ->where('retrait_valide', false)
                                     ->count();

        // Calculate percentages
        $pourcentageValides = $nombreEmployes > 0 ? round(($salaireValides / $nombreEmployes) * 100, 1) : 0;
        $pourcentageEnAttente = $nombreEmployes > 0 ? round(($salaireEnAttente / $nombreEmployes) * 100, 1) : 0;
        $pourcentageNonTraites = $nombreEmployes > 0 ? round(($salaireNonTraites / $nombreEmployes) * 100, 1) : 0;

        // Calculate validated amount
        $montantValide = $salaires->where('retrait_valide', true)->sum('somme');

        return view('rapports.salaires', compact(
            'salaires',
            'totalSalaires',
            'nombreEmployes',
            'salaireMoyen',
            'pourcentageValides',
            'pourcentageEnAttente',
            'pourcentageNonTraites',
            'montantValide',
            'currentMonthName'
        ));
    }

    public function acouper()
    {
        $currentDate = Carbon::now();
        $currentMonthName = $currentDate->locale('fr')->format('F Y');

        // Get Acouper entries for the current month
        $acoupers = Acouper::with('employe')
            ->whereMonth('date', $currentDate->month)
            ->whereYear('date', $currentDate->year)
            ->get();

        // Calculate totals
        $totalManquants = $acoupers->sum('manquants');
        $totalRemboursements = $acoupers->sum('remboursement');
        $totalPrets = $acoupers->sum('pret');
        $totalCaisseSociale = $acoupers->sum('caisse_sociale');
        $totalGeneral = $totalManquants + $totalRemboursements + $totalPrets + $totalCaisseSociale;

        // Group by employee for analysis
        $acoupersParEmploye = [];
        foreach ($acoupers as $acouper) {
            $employeName = optional($acouper->employe)->name ?? 'Employé inconnu';
            $total = $acouper->manquants + $acouper->remboursement + $acouper->pret + $acouper->caisse_sociale;

            if (!isset($acoupersParEmploye[$employeName])) {
                $acoupersParEmploye[$employeName] = 0;
            }

            $acoupersParEmploye[$employeName] += $total;
        }

        return view('rapports.acouper', compact(
            'acoupers',
            'totalManquants',
            'totalRemboursements',
            'totalPrets',
            'totalCaisseSociale',
            'totalGeneral',
            'acoupersParEmploye',
            'currentMonthName'
        ));
    }


    /**
     * Rapport des dépenses
     */
    public function depenses()
    {
        $currentMonth = now()->format('Y-m');
        $currentMonthName = now()->locale('fr')->format('F Y');

        // Statistiques générales
        $totalDepenses = Depense::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('prix');

        $nombreDepenses = Depense::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        // Répartition par type
        $depensesParType = Depense::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->select('type', DB::raw('SUM(prix) as total'))
            ->groupBy('type')
            ->get();

        // Evolution par rapport au mois dernier
        $moisPrecedent = now()->subMonth();
        $totalDepensesMoisPrecedent = Depense::whereYear('date', $moisPrecedent->year)
            ->whereMonth('date', $moisPrecedent->month)
            ->sum('prix');

        $evolution = $totalDepensesMoisPrecedent > 0
            ? round((($totalDepenses - $totalDepensesMoisPrecedent) / $totalDepensesMoisPrecedent) * 100, 2)
            : 100;

        // Liste des dépenses
        $depenses = Depense::with('auteurRelation')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->orderBy('date', 'desc')
            ->get();

        return view('rapports.depenses', compact(
            'currentMonthName',
            'totalDepenses',
            'nombreDepenses',
            'depensesParType',
            'evolution',
            'depenses'
        ));
    }

    /**
     * Rapport des versements chef
     */
    public function versementsChef()
    {
        $currentMonth = now()->format('Y-m');
        $currentMonthName = now()->locale('fr')->format('F Y');

        // Statistiques générales
        $totalVersements = VersementChef::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('montant');

        $nombreVersements = VersementChef::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        $versementsEnAttente = VersementChef::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->where('status', 0)
            ->count();

        $versementsValides = VersementChef::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->where('status', 1)
            ->count();

        // Evolution par rapport au mois dernier
        $moisPrecedent = now()->subMonth();
        $totalVersementsMoisPrecedent = VersementChef::whereYear('date', $moisPrecedent->year)
            ->whereMonth('date', $moisPrecedent->month)
            ->sum('montant');

        $evolution = $totalVersementsMoisPrecedent > 0
            ? round((($totalVersements - $totalVersementsMoisPrecedent) / $totalVersementsMoisPrecedent) * 100, 2)
            : 100;

        // Liste des versements
        $versements = VersementChef::with('chefProduction')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->orderBy('date', 'desc')
            ->get();

        return view('rapports.versements_chef', compact(
            'currentMonthName',
            'totalVersements',
            'nombreVersements',
            'versementsEnAttente',
            'versementsValides',
            'evolution',
            'versements'
        ));
    }

    /**
     * Rapport des commandes
     */
    public function commandes()
    {
        $currentMonth = now()->format('Y-m');
        $currentMonthName = now()->locale('fr')->format('F Y');

        // Statistiques générales
        $totalCommandes = Commande::whereYear('date_commande', now()->year)
            ->whereMonth('date_commande', now()->month)
            ->count();

        $commandesValidees = Commande::whereYear('date_commande', now()->year)
            ->whereMonth('date_commande', now()->month)
            ->where('valider', true)
            ->count();

        $commandesEnAttente = Commande::whereYear('date_commande', now()->year)
            ->whereMonth('date_commande', now()->month)
            ->where('valider', false)
            ->count();

        // Répartition par catégorie
        $commandesParCategorie = Commande::whereYear('date_commande', now()->year)
            ->whereMonth('date_commande', now()->month)
            ->select('categorie', DB::raw('COUNT(*) as nombre'))
            ->groupBy('categorie')
            ->get();

        // Liste des commandes
        $commandes = Commande::with('produitRelation')
            ->whereYear('date_commande', now()->year)
            ->whereMonth('date_commande', now()->month)
            ->orderBy('date_commande', 'desc')
            ->get();

        return view('rapports.commandes', compact(
            'currentMonthName',
            'totalCommandes',
            'commandesValidees',
            'commandesEnAttente',
            'commandesParCategorie',
            'commandes'
        ));
    }

    /**
     * Rapport des déductions (A couper)
     */
    public function deductions()
    {
        $currentMonth = now()->format('Y-m');
        $currentMonthName = now()->locale('fr')->format('F Y');

        // Statistiques générales
        $totalManquants = Acouper::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('manquants');

        $totalRemboursements = Acouper::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('remboursement');

        $totalPrets = Acouper::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('pret');

        $totalCaisseSociale = Acouper::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('caisse_sociale');

        $totalDeductions = $totalManquants + $totalRemboursements + $totalPrets + $totalCaisseSociale;

        // Evolution par rapport au mois dernier
        $moisPrecedent = now()->subMonth();
        $totalDeductionsMoisPrecedent = Acouper::whereYear('date', $moisPrecedent->year)
            ->whereMonth('date', $moisPrecedent->month)
            ->sum(DB::raw('manquants + remboursement + pret + caisse_sociale'));

        $evolution = $totalDeductionsMoisPrecedent > 0
            ? round((($totalDeductions - $totalDeductionsMoisPrecedent) / $totalDeductionsMoisPrecedent) * 100, 2)
            : 100;

        // Liste des déductions
        $deductions = Acouper::with('employe')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->orderBy('date', 'desc')
            ->get();

        return view('rapports.deductions', compact(
            'currentMonthName',
            'totalManquants',
            'totalRemboursements',
            'totalPrets',
            'totalCaisseSociale',
            'totalDeductions',
            'evolution',
            'deductions'
        ));
    }

    public function transactions()
    {
        $currentMonth = now()->format('Y-m');
        $currentMonthName = now()->locale('fr')->format('F Y');

        // Statistiques générales
        $totalRevenus = Transaction::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->where('type', 'income')
            ->sum('amount');

        $totalDepenses = Transaction::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->where('type', 'outcome')
            ->sum('amount');

        $balance = $totalRevenus - $totalDepenses;

        // Répartition par catégorie
        $transactionsParCategorie = Transaction::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name', 'transactions.type', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name', 'transactions.type')
            ->get();

        // Evolution par rapport au mois dernier
        $moisPrecedent = now()->subMonth();
        $balanceMoisPrecedent = Transaction::whereYear('date', $moisPrecedent->year)
            ->whereMonth('date', $moisPrecedent->month)
            ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) - SUM(CASE WHEN type = "outcome" THEN amount ELSE 0 END) as balance')
            ->value('balance');

            $evolution = 0;

        // Liste des transactions
        $transactions = Transaction::with('category')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->orderBy('date', 'desc')
            ->get();

        return view('rapports.transactions', compact(
            'currentMonthName',
            'totalRevenus',
            'totalDepenses',
            'balance',
            'transactionsParCategorie',
            'evolution',
            'transactions'
        ));
    }
}
