<?php
namespace App\Http\Controllers;
use App\Models\Production;
use App\Models\Daily_assignments;
use App\Models\Produit_fixes;
use App\Models\User;
use App\Models\Commande;
use App\Models\Production_suggerer_par_jour;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  // Ajout de l'import
use App\Models\Utilisation;
use App\Models\Matiere;
use App\Services\UniteConversionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUtilisationRequest;
use App\Services\AdvancedProductionStatsService;
use App\Services\ProductionService;
use App\Services\ProducteurComparisonService;
use App\Services\ProductionStatsService;
use App\Services\LotGeneratorService;
use App\Services\PerformanceService;
use Illuminate\Support\Facades\Log;
use App\Models\ReservationMp;

class ProducteurController extends Controller  // Hérite de Controller
{


    protected $statsService;
    protected $conversionService;
    protected $productionService;
    protected $uniteConversionService;
    protected $lotGeneratorService;

    public function __construct(
        AdvancedProductionStatsService $statsService,
        UniteConversionService $uniteConversionService,
        ProductionService $productionService,
        LotGeneratorService $lotGeneratorService,
        ProductionStatsService $productionStatsService
    ) {
        $this->statsService = $statsService;
        $this->uniteConversionService = $uniteConversionService;
        $this->conversionService = $uniteConversionService;
        $this->productionService = $productionService;
        $this->lotGeneratorService = $lotGeneratorService;
        $this->productionStatsService = $productionStatsService;

    }


    public function produit()
    {
        $employe = Auth::user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        // Récupérer le rôle
        $role = $employe->role === 'patissier' ? 'patisserie' :
               ($employe->role === 'boulanger' ? 'boulangerie' : '');

        // Récupérer les données via le service
        $productions = $this->productionService->getTodayProductions($employe->id);
        $productions_attendues = $this->productionService->getExpectedProductions($employe->id);
        $productions_recommandees = $this->productionService->getRecommendedProductions();

        // Données complémentaires
        $all_produits = Produit_fixes::where('categorie', $role)->get();
        $info = User::find($employe->id);

        return view('pages.producteur.producteur_produit', [
            'p' => $productions,
            'all_produits' => $all_produits,
            'heure_actuelle' => now(),
            'nom' => $info->name,
            'secteur' => $info->secteur,
            'productions_attendues' => $productions_attendues,
            'productions_recommandees' => $productions_recommandees,
            'day' => strtolower(now()->locale('fr')->dayName)
        ]);
    }

    public function produit_par_lot()
    {
        $utilisations = DB::table('Utilisation')
        ->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
        ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
        ->select(
            'Utilisation.id_lot',
            'Produit_fixes.nom as nom_produit',
            'Produit_fixes.prix as prix_produit',
            'Utilisation.quantite_produit',
            'Matiere.nom as nom_matiere',
            'Matiere.prix_par_unite_minimale',
            'Utilisation.quantite_matiere',
            'Utilisation.unite_matiere'
        )
        ->orderBy('Utilisation.id_lot')
        ->get();

    $productionsParLot = [];

    foreach ($utilisations as $utilisation) {
        $idLot = $utilisation->id_lot;
        $nomProduit = $utilisation->nom_produit;

        if (!isset($productionsParLot[$idLot])) {
            $productionsParLot[$idLot] = [
                'produit' => $nomProduit,
                'quantite_produit' => $utilisation->quantite_produit,
                'prix_unitaire' => $utilisation->prix_produit,
                'matieres' => [],
                'valeur_production' => $utilisation->quantite_produit * $utilisation->prix_produit,
                'cout_matieres' => 0
            ];
        }

        $productionsParLot[$idLot]['matieres'][] = [
            'nom' => $utilisation->nom_matiere,
            'quantite' => $utilisation->quantite_matiere,
            'unite' => $utilisation->unite_matiere,
            'cout' => $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale
        ];

        $productionsParLot[$idLot]['cout_matieres'] +=
            $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale;
    }
         $info = Auth::user();
        $nom = $info->name;
        $secteur = $info->secteur;

    return view('pages.producteur.produit_par_lot', compact('productionsParLot','nom','secteur'));
    }


private function getPeriode(): array
{
    return [
        'debut' => now()->startOfMonth(),
        'fin' => now()->endOfMonth(),
        'mois_actuel' => now()->format('F Y')
    ];
}





    public function commande() {
    // Vérification de l'authentification
    $employe = auth()->user();
    if (!$employe) {
        return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }

    // Définition du rôle avec tableau associatif (plus propre)
    $roles = [
        'patissier' => 'patisserie',
        'boulanger' => 'boulangerie'
    ];
    $role = $roles[$employe->role] ?? '';

    // Récupération des infos utilisateur
    $info = User::where('id', $employe->id)->first();
    $nom = $info->name;
    $secteur = $info->secteur;

    // Récupération des commandes
    $commandes = Commande::where('categorie', $role)->where('valider',0)->get();
    return view('pages/producteur/producteur_commande', compact('nom', 'secteur', 'commandes'));
}


    public function reserverMp() {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $info = User::where('id', $employe->id)->first();
        $nom = $info ->name;
        $secteur = $info->secteur;
        return view('pages/producteur/producteur_reserverMp',compact('nom','secteur'));

    }


    public function stat_prod()
    {
        $employe = Auth::user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        $stats = $this->statsService->getStats($employe->id);

        return view('pages.producteur.stat_prod', [
            'stats' => $stats,
            'nom' => $employe->name,
            'secteur' => $employe->secteur
        ]);
    }

    public function create()
    {
        $produits = Produit_fixes::all();
        $matieres = Matiere::all();
        $info = Auth::user();
        $nom = $info->name;
        $secteur = $info->secteur;
        return view('pages.producteur.produitmp', compact('produits', 'matieres','nom','secteur'));
    }

    public function store2(StoreUtilisationRequest $request)
    {
        try {
            DB::beginTransaction();

            // Générer un ID de lot unique pour cette production
            $lotId = $this->lotGeneratorService->generateLotId();

            foreach ($request->matieres as $matiere) {
                $matiereModel = Matiere::findOrFail($matiere['matiere_id']);

                $quantiteConvertie = $this->uniteConversionService->convertir(
                    $matiere['quantite'],
                    $matiere['unite'],
                    $matiereModel->unite_minimale
                );

                $utilisation = new Utilisation();
                $utilisation->id_lot = $lotId; // Assigner le même ID de lot
                $utilisation->produit = $request->produit;
                $utilisation->matierep = $matiere['matiere_id'];
                $utilisation->producteur = Auth::id();
                $utilisation->quantite_produit = $request->quantite_produit;
                $utilisation->quantite_matiere = $quantiteConvertie;
                $utilisation->unite_matiere = $matiereModel->unite_minimale;

                $utilisation->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Production enregistrée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage())->withInput();
        }
    }

public function comparaison(Request $request)
{
    $employe = auth()->user();
    if (!$employe) {
        return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }

    $critere = $request->input('critere', 'benefice');
    $periode = $request->input('periode', 'mois');
    $dateDebut = $request->input('date_debut');
    $dateFin = $request->input('date_fin');

    $comparisonService = app(ProducteurComparisonService::class);
    $resultats = $comparisonService->compareProducteurs($critere, $periode, $dateDebut, $dateFin);

    return view('pages.producteur.comparaison', [
        'resultats' => $resultats,
        'critere' => $critere,
        'periode' => $periode,
        'date_debut' => $dateDebut,
        'date_fin' => $dateFin
    ]);
}

public function fiche_production()
{
    $employe = auth()->user();
    if (!$employe) {
        return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }

    $userId = auth()->id();
    $stats = $this->statsService->getStats($userId);

    // Calcul des statistiques globales
    $globalStats = $this->calculateGlobalStats($stats);

    // Calcul des appréciations
    $appreciations = $this->calculateAppreciations($globalStats, $stats);
    $info = User::where('id', $employe->id)->first();
    $nom = $info ->name;
    $secteur = $info->secteur;
    $age = $info->age;
    return view('pages.producteur.producteur_fiche_production', compact('stats', 'globalStats', 'appreciations','nom', 'secteur', 'age'));
}

private function calculateGlobalStats($stats)
{
    $products = collect($stats['products']);

    return [
        'max_production' => [
            'produit' => $products->sortByDesc('produit.quantite_totale')->first(),
            'valeur' => $products->max('produit.quantite_totale')
        ],
        'max_benefice' => [
            'produit' => $products->sortByDesc('benefice')->first(),
            'valeur' => $products->max('benefice')
        ],
        'max_perte' => [
            'produit' => $products->sortBy('benefice')->first(),
            'valeur' => $products->min('benefice')
        ],
        'meilleur_jour' => [
            'date' => collect($stats['daily']['quantities'])->search(collect($stats['daily']['quantities'])->max()),
            'quantite' => collect($stats['daily']['quantities'])->max()
        ],
        'meilleur_mois' => [
            'date' => collect($stats['monthly']['quantities'])->search(collect($stats['monthly']['quantities'])->max()),
            'quantite' => collect($stats['monthly']['quantities'])->max()
        ],
        'total_benefice' => $products->sum('benefice'),
        'moyenne_marge' => $products->avg('marge')
    ];
}

private function calculateAppreciations($globalStats, $stats)
{
    $appreciations = [];

    // Appréciation de la rentabilité
    if ($globalStats['moyenne_marge'] > 30) {
        $appreciations['rentabilite'] = 'Excellente rentabilité';
    } elseif ($globalStats['moyenne_marge'] > 20) {
        $appreciations['rentabilite'] = 'Bonne rentabilité';
    } else {
        $appreciations['rentabilite'] = 'Rentabilité à améliorer';
    }

    // Tendance production
    $recentQuantities = array_slice($stats['daily']['quantities']->toArray(), 0, 3);
    if (array_sum($recentQuantities) > 0) {
        $appreciations['tendance'] = 'Production en hausse';
    } else {
        $appreciations['tendance'] = 'Production en baisse';
    }

    return $appreciations;
}
}

