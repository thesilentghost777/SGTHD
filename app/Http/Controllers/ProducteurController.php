<?php
namespace App\Http\Controllers;
use App\Models\Production;
use App\Models\Daily_assignments;
use App\Models\Produit_fixes;
use App\Models\User;
use App\Models\AssignationMatiere;
use App\Models\Commande;
use App\Models\Production_suggerer_par_jour;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;  // Ajout de l'import
use App\Models\Utilisation;
use App\Models\Matiere;
use App\Models\ProduitStock;
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
use App\Traits\HistorisableActions;
use App\Http\Controllers\NotificationController;
class ProducteurController extends Controller  // Hérite de Controller
{

    use HistorisableActions;
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
        ProductionStatsService $productionStatsService,
        NotificationController $notificationController
    ) {
        $this->statsService = $statsService;
        $this->uniteConversionService = $uniteConversionService;
        $this->conversionService = $uniteConversionService;
        $this->productionService = $productionService;
        $this->lotGeneratorService = $lotGeneratorService;
        $this->productionStatsService = $productionStatsService;
        $this->notificationController = $notificationController;
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

    // Dates de référence
    $today = now()->startOfDay();
    $yesterday = now()->subDay()->startOfDay();

    // Récupérer toutes les matières sauf celles dont le nom commence par 'Taule'
    // et qui ont été créées avant hier
    $matieres = Matiere::where(function($query) use ($today, $yesterday) {
        $query->where('nom', 'not like', 'Taule%')  // Les matières qui ne commencent pas par 'Taule'
              ->orWhere(function($q) use ($today, $yesterday) {
                  $q->where('nom', 'like', 'Taule%')  // Les matières qui commencent par 'Taule'
                    ->where(function($subq) use ($today, $yesterday) {
                        $subq->where('created_at', '>=', $yesterday)  // Et créées aujourd'hui ou hier
                             ->where('created_at', '<', $today->copy()->addDay());
                    });
              });
    })->get();

    $info = Auth::user();
    $nom = $info->name;
    $secteur = $info->secteur;

    return view('pages.producteur.produitmp', compact('produits', 'matieres', 'nom', 'secteur'));
}

public function store2(StoreUtilisationRequest $request)
{
    try {
        DB::beginTransaction();

        // Générer un ID de lot unique pour cette production
        $lotId = $this->lotGeneratorService->generateLotId();
        $producteurId = Auth::id();
        $errors = [];
        $conversionService = $this->uniteConversionService;

        // Vérifier que la quantité produite est positive
        if ($request->quantite_produit <= 0) {
            DB::rollBack();
            return redirect()->back()->with('error', 'La quantité produite doit être positive')->withInput();
        }

        foreach ($request->matieres as $index => $matiere) {
            $matiereModel = Matiere::findOrFail($matiere['matiere_id']);

            // Conversion de la quantité demandée vers l'unité minimale si pas déjà en unité minimale
            if ($matiere['unite'] !== $matiereModel->unite_minimale) {
                $quantiteConvertie = $conversionService->convertir(
                    $matiere['quantite'],
                    $matiere['unite'],
                    $matiereModel->unite_minimale
                );
            } else {
                $quantiteConvertie = $matiere['quantite'];
            }

            // Vérifier si la quantité de matière est positive
            if ($quantiteConvertie <= 0) {
                $errors[] = "La quantité de '{$matiereModel->nom}' doit être positive.";
                continue;
            }

            // Vérifier si le producteur a cette matière assignée avec une quantité suffisante
            $assignation = AssignationMatiere::where('producteur_id', $producteurId)
                            ->where('matiere_id', $matiere['matiere_id'])
                            ->where('quantite_restante', '>', 0)
                            ->latest()
                            ->first();

            if (!$assignation) {
                $errors[] = "La matière '{$matiereModel->nom}' n'est pas assignée à votre compte ou n'est plus disponible.";
                continue;
            }
            //convertir la qte restante dans l'unité minimale
            if ($assignation->unite_minimale !== $matiereModel->unite_minimale) {
                $quantiteRestanteConvertie = $conversionService->convertir(
                    $assignation->quantite_restante,
                    $assignation->unite_assignee,
                    $matiereModel->unite_minimale
                );
            } else {
                $quantiteRestanteConvertie = $assignation->quantite_restante;
            }

            // Vérifier si la quantité est suffisante (déjà en unité minimale)
            if ($quantiteRestanteConvertie < $quantiteConvertie) {
                $uniteMinimaleString = is_object($matiereModel->unite_minimale) ? $matiereModel->unite_minimale->value : $matiereModel->unite_minimale;
                $errors[] = "Quantité insuffisante pour '{$matiereModel->nom}'. " .
                            "Disponible: {$assignation->quantite_restante} {$uniteMinimaleString}, " .
                            "Demandé: {$quantiteConvertie} {$uniteMinimaleString}";
                continue;
            }
            //retrancher la quantité de matière utiliser pour la production toujours convertie dans l'unité minimale a la quantité assigner restante en uniter minimale et enfin reconvertir la quantité restante dans l'unité assignée et savegarder
            $quantiteRestanteConvertie -= $quantiteConvertie;
            $matiere_cible = Matiere::findOrFail($matiere['matiere_id']);
            $assignation->quantite_restante = $conversionService->convertir(
                $quantiteRestanteConvertie,
                $matiere_cible->unite_minimale,
                $assignation->unite_assignee
            );
            Log::info('Conversion des unités pour assignation', [
                'quantiteRestanteConvertie' => $quantiteRestanteConvertie,
                'unite_minimale' => $matiereModel->unite_minimale,
                'unite_assignee' => $assignation->unite_assignee,
            ]);
            $assignation->save();
        }

        // S'il y a des erreurs, annuler la transaction
        if (!empty($errors)) {
            DB::rollBack();
            return redirect()->back()->with('error', implode('<br>', $errors))->withInput();
        }

        // Récupérer le produit pour calculer la valeur de la production
        $produit = DB::table('Produit_fixes')->where('code_produit', $request->produit)->first();
        if (!$produit) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Produit non trouvé')->withInput();
        }

        // Calculer la valeur totale de la production
        $valeurTotale = $request->quantite_produit * $produit->prix;
        $coutTotal = 0;

        // Procéder à l'enregistrement et mise à jour des quantités
        foreach ($request->matieres as $matiere) {
            $matiereModel = Matiere::findOrFail($matiere['matiere_id']);

            // Conversion de la quantité en unité minimale
            if ($matiere['unite'] !== $matiereModel->unite_minimale) {
                $quantiteConvertie = $conversionService->convertir(
                    $matiere['quantite'],
                    $matiere['unite'],
                    $matiereModel->unite_minimale
                );
            } else {
                $quantiteConvertie = $matiere['quantite'];
            }

            // Récupérer à nouveau l'assignation pour mise à jour
            $assignation = AssignationMatiere::where('producteur_id', $producteurId)
                            ->where('matiere_id', $matiere['matiere_id'])
                            ->where('quantite_restante', '>', 0)
                            ->latest()
                            ->first();

            // Déduire la quantité utilisée (en unité minimale)
            $assignation->quantite_restante -= $quantiteConvertie;
            $assignation->save();

            // Créer l'enregistrement d'utilisation
            $utilisation = new Utilisation();
            $utilisation->id_lot = $lotId;
            $utilisation->produit = $request->produit;
            $utilisation->matierep = $matiere['matiere_id'];
            $utilisation->producteur = $producteurId;
            $utilisation->quantite_produit = $request->quantite_produit;
            $utilisation->quantite_matiere = $quantiteConvertie;
            $utilisation->unite_matiere = is_object($matiereModel->unite_minimale) ? $matiereModel->unite_minimale->value : $matiereModel->unite_minimale;
            $utilisation->save();

            // Ajouter au coût total des matières
            $coutMatiere = $quantiteConvertie * $matiereModel->prix_par_unite_minimale;
            $coutTotal += $coutMatiere;
        }

        // Calculer le bénéfice
        $benefice = $valeurTotale - $coutTotal;

        // Mettre à jour le stock de produits
        $produitStock = ProduitStock::firstOrNew(['id_produit' => $request->produit]);
        $produitStock->quantite_en_stock += $request->quantite_produit;
        $produitStock->save();

        // Historiser l'opération
        $infoProducteur = User::findOrFail($producteurId);
        $this->historiser("Production du lot {$lotId} par {$infoProducteur->name}: {$request->quantite_produit} unités de {$produit->nom}. Bénéfice: {$benefice} FCFA", 'create_production');

        // Envoyer des notifications en fonction du bénéfice
        $producteur = User::findOrFail($producteurId);
        $chefProduction = User::where('role', 'chef_production')->first();

        // Notification pour bénéfice négatif ou inférieur à 5000
        if ($benefice < 5000) {
            // Notification au producteur
            $request->merge([
                'recipient_id' => $producteurId,
                'subject' => 'Alerte - Bénéfice faible sur production',
                'message' => "Votre production du lot {$lotId} a généré un bénéfice de {$benefice} FCFA, ce qui est inférieur au seuil de rentabilité recommandé de 5000 FCFA. Nous vous invitons à revoir votre processus de production pour optimiser les coûts."
            ]);
            $this->notificationController->send($request);

            // Notification au chef de production si disponible
            if ($chefProduction) {
                $request->merge([
                    'recipient_id' => $chefProduction->id,
                    'subject' => 'Alerte - Production à faible rentabilité',
                    'message' => "La production du lot {$lotId} par {$producteur->name} a généré un bénéfice de seulement {$benefice} FCFA, ce qui est inférieur au seuil de rentabilité recommandé de 5000 FCFA."
                ]);
                $this->notificationController->send($request);
            }
        }
        // Notification pour bénéfice supérieur à 25000
        elseif ($benefice > 25000) {
            // Notification au producteur
            $request->merge([
                'recipient_id' => $producteurId,
                'subject' => 'Félicitations - Production très rentable',
                'message' => "Votre production du lot {$lotId} a généré un excellent bénéfice de {$benefice} FCFA, dépassant le seuil de haute rentabilité de 25000 FCFA. Félicitations pour cette performance remarquable!"
            ]);
            $this->notificationController->send($request);

            // Notification au chef de production si disponible
            if ($chefProduction) {
                $request->merge([
                    'recipient_id' => $chefProduction->id,
                    'subject' => 'Performance exceptionnelle - Production très rentable',
                    'message' => "La production du lot {$lotId} par {$producteur->name} a généré un excellent bénéfice de {$benefice} FCFA, dépassant le seuil de haute rentabilité de 25000 FCFA."
                ]);
                $this->notificationController->send($request);
            }
        }

        DB::commit();
        return redirect()->back()->with('success', 'Production enregistrée avec succès. Bénéfice réalisé: ' . $benefice . ' FCFA');
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

