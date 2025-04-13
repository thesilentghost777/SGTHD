<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Models\Daily_assignments;

use App\Models\Utilisation;

use App\Models\MatiereRecommander;

use App\Models\Produit_fixes;
use App\Models\Salaire;
use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class EmployeePerformanceController extends Controller {

    public function index() {

        $employees = User::where('secteur', 'production')->get();

        return view('employee.performance.index', compact('employees'));

    }

    // Dans le contrôleur EmployeePerformanceController
public function filter(Request $request) {
    $period = $request->period;
    $startDate = $request->start_date ?? now()->startOfDay();
    $endDate = $request->end_date ?? now()->endOfDay();

    switch($period) {
        case 'day':
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $message = "Affichage des données pour aujourd'hui";
            break;
        case 'week':
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
            $message = "Affichage des données de la semaine";
            break;
        case 'month':
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $message = "Affichage des données du mois";
            break;
        case 'custom':
            $message = "Affichage des données du " . \Carbon\Carbon::parse($startDate)->format('d/m/Y') . " au " . \Carbon\Carbon::parse($endDate)->format('d/m/Y');
            break;
    }

    $employees = User::where('secteur', 'production')
        ->with(['utilisations' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->get();

    return response()->json([
        'employees' => $employees,
        'message' => $message
    ]);
}
public function code_list() {
    return view('pages.code_list');
}

    public function show(Request $request, $id) {
            $period = $request->period ?? 'month';

            // Définir les dates en fonction de la période
            switch($period) {
                case 'day':
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'custom':
                    $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
                    $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();
                    break;
                default:
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
            }

        $employee = User::findOrFail($id);

        // Get employee's age
        $age = Carbon::parse($employee->date_naissance)->age;

        // Get production data
        $productions = Utilisation::where('producteur', $id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Calculate total production and revenue
        $productionStats = $this->calculateProductionStats($productions);

        // Get daily assignments completion rate
        $assignmentRate = $this->calculateAssignmentCompletionRate($id, $startDate, $endDate);

        // Calculate waste percentage per product
        $wasteStats = $this->calculateWasteStats($id, $startDate, $endDate);

        // Get evolution data for chart
        $evolutionData = $this->getProductionEvolution($id, $startDate, $endDate);

        // Get period display text
        $periodDisplay = $this->getPeriodDisplayText($period, $startDate, $endDate);

        // Get most produced product
        $mostProducedProduct = $this->getMostProducedProduct($id, $startDate, $endDate);

         // Get salary information
         $salary = Salaire::where('id_employe', $id)->first();


        return view('employee.performance.show', compact(
            'employee',
            'age',
            'productionStats',
            'assignmentRate',
            'wasteStats',
            'salary',
            'evolutionData',
            'period',
            'periodDisplay',
            'mostProducedProduct'
        ));
    }

    private function getPeriodDisplayText($period, $startDate, $endDate) {
        switch($period) {
            case 'day':
                return "Aujourd'hui (" . Carbon::parse($startDate)->format('d/m/Y') . ")";
            case 'week':
                return "Semaine du " . Carbon::parse($startDate)->format('d/m/Y') . " au " . Carbon::parse($endDate)->format('d/m/Y');
            case 'month':
                return "Mois de " . Carbon::parse($startDate)->format('F Y');
            default:
                return "Période du " . Carbon::parse($startDate)->format('d/m/Y') . " au " . Carbon::parse($endDate)->format('d/m/Y');
        }
    }
    private function getMostProducedProduct($employeeId, $startDate, $endDate) {
        // Première étape : obtenir une valeur unique de quantité produite par lot
        $lotsUniques = DB::table('Utilisation')
            ->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
            ->select('Utilisation.id_lot', 'Produit_fixes.nom as nom_produit', DB::raw('SUM(DISTINCT Utilisation.quantite_produit) as total_quantity'))
            ->where('Utilisation.producteur', $employeeId)
            ->whereBetween('Utilisation.created_at', [$startDate, $endDate])
            ->groupBy('Utilisation.id_lot', 'Produit_fixes.nom')
            ->get();

        // Deuxième étape : regrouper par produit et sommer les quantités
        $produitLePlusProduit = collect($lotsUniques)
            ->groupBy('nom_produit')
            ->map(function ($group) {
                return [
                    'nom_produit' => $group[0]->nom_produit,
                    'total_quantity' => $group->sum('total_quantity')
                ];
            })
            ->sortByDesc('total_quantity')
            ->first();

        return $produitLePlusProduit;
    }




    private function calculateProductionStats($productions) {
        $stats = [];
        $totalRevenue = 0;
        $totalCost = 0;

        // Utiliser un tableau pour suivre les lots déjà traités
        $processedLots = [];

        foreach($productions as $prod) {
            // Vérifier si ce lot a déjà été traité
            if (in_array($prod->id_lot, $processedLots)) {
                continue; // Passer au suivant pour éviter les doublons
            }

            $processedLots[] = $prod->id_lot; // Marquer ce lot comme traité

            $product = Produit_fixes::find($prod->produit);

            if(!isset($stats[$product->id])) {
                $stats[$product->id] = [
                    'name' => $product->nom,
                    'quantity' => 0,
                    'revenue' => 0
                ];
            }

            // Compter la quantité produite une seule fois par lot
            $stats[$product->id]['quantity'] += $prod->quantite_produit;
            $stats[$product->id]['revenue'] += $prod->quantite_produit * $product->prix;

            // Calculer le coût des matières premières
            $materialsUsed = Utilisation::where('id_lot', $prod->id_lot)
                ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
                ->select('Matiere.prix_par_unite_minimale', 'Utilisation.quantite_matiere')
                ->get();

            $lotCost = 0;
            foreach($materialsUsed as $material) {
                $lotCost += $material->quantite_matiere * $material->prix_par_unite_minimale;
            }

            $totalCost += $lotCost;
            $totalRevenue += $prod->quantite_produit * $product->prix;
        }

        return [
            'products' => $stats,
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'profit' => $totalRevenue - $totalCost
        ];
    }

    private function calculateMaterialCost($production) {
        // Récupérer toutes les matières premières utilisées pour ce lot
        $materialsUsed = Utilisation::where('id_lot', $production->id_lot)
            ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
            ->select(
                'Matiere.nom as nom_matiere',
                'Matiere.prix_par_unite_minimale',
                'Utilisation.quantite_matiere',
                'Utilisation.unite_matiere'
            )
            ->get();

        $totalCost = 0;
        foreach($materialsUsed as $material) {
            $totalCost += $material->quantite_matiere * $material->prix_par_unite_minimale;
        }

        return $totalCost;
    }

    public function productionDetails(Request $request) {
        // Modifier la requête pour éviter les doublons par lot de production
        // en sélectionnant un seul enregistrement par lot
        $query = Utilisation::select(
                'Utilisation.id_lot',
                'Utilisation.producteur',
                'Utilisation.produit',
                'Produit_fixes.prix',
                'Produit_fixes.nom as nom_produit',
                'users.name as nom_producteur',
                'Utilisation.quantite_produit',
                'Utilisation.created_at as date_production'
            )
            ->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
            ->join('users', 'Utilisation.producteur', '=', 'users.id')
            // Utiliser DISTINCT ON avec id_lot pour PostgreSQL
            // Pour MySQL, on peut utiliser GROUP BY
            ->groupBy(
                'Utilisation.id_lot',
                'Utilisation.producteur',
                'Utilisation.produit',
                'Produit_fixes.prix',
                'Produit_fixes.nom',
                'users.name',
                'Utilisation.quantite_produit',
                'Utilisation.created_at'
            );

        // Filtres
        if ($request->has('date_debut') && $request->date_debut) {
            $query->whereDate('Utilisation.created_at', '>=', $request->date_debut);
        }
        if ($request->has('date_fin') && $request->date_fin) {
            $query->whereDate('Utilisation.created_at', '<=', $request->date_fin);
        }
        if ($request->has('producteur') && $request->producteur) {
            $query->where('Utilisation.producteur', $request->producteur);
        }
        if ($request->has('produit') && $request->produit) {
            $query->where('Utilisation.produit', $request->produit);
        }

        // Traiter les résultats pour calculer les statistiques par lot
        $uniqueLots = [];
        $tempProductions = $query->get();

        foreach ($tempProductions as $production) {
            // Vérifier si ce lot a déjà été traité
            if (isset($uniqueLots[$production->id_lot])) {
                continue;
            }

            $uniqueLots[$production->id_lot] = $production;
        }

        $productions = collect(array_values($uniqueLots))->map(function ($production) {
            // Calculer le coût selon la même logique que produit_par_lot
            $materialsUsed = Utilisation::where('id_lot', $production->id_lot)
                ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
                ->select(
                    'Matiere.nom as nom_matiere',
                    'Matiere.prix_par_unite_minimale',
                    'Utilisation.quantite_matiere',
                    'Utilisation.unite_matiere'
                )
                ->get();

            $coutProduction = 0;
            foreach($materialsUsed as $material) {
                $coutProduction += $material->quantite_matiere * $material->prix_par_unite_minimale;
            }

            // Calculer le chiffre d'affaires en utilisant le prix du produit
            $chiffreAffaires = $production->quantite_produit * $production->prix;
            $beneficeBrut = $chiffreAffaires - $coutProduction;

            // Récupérer l'assignation correspondante
            $assignation = Daily_assignments::where('producteur', $production->producteur)
                ->whereDate('assignment_date', Carbon::parse($production->date_production)->toDateString())
                ->first();

            // Calculer le taux de respect de l'assignation
            $tauxRespect = $assignation
                ? ($production->quantite_produit / $assignation->expected_quantity) * 100
                : null;

            // Calculer le taux de gaspillage
            $tauxGaspillage = $this->calculateWastageRate($production->id_lot);

            return [
                'id_lot' => $production->id_lot,
                'date_production' => Carbon::parse($production->date_production)->format('d/m/Y'),
                'producteur' => $production->nom_producteur,
                'produit' => $production->nom_produit,
                'quantite' => $production->quantite_produit,
                'chiffre_affaires' => $chiffreAffaires,
                'cout_production' => $coutProduction,
                'benefice_brut' => $beneficeBrut,
                'taux_respect' => $tauxRespect,
                'taux_gaspillage' => $tauxGaspillage
            ];
        });

        $producteurs = User::whereNotNull('role')->get();
        $produits = Produit_fixes::all();

        return view('statistiques.details', compact('productions', 'producteurs', 'produits'));
    }



    private function calculateAssignmentCompletionRate($employeeId, $startDate, $endDate) {

        $totalAssignments = Daily_assignments::where('producteur', $employeeId)

            ->whereBetween('assignment_date', [$startDate, $endDate])

            ->count();



        $completedAssignments = Daily_assignments::where('producteur', $employeeId)

            ->whereBetween('assignment_date', [$startDate, $endDate])

            ->where('status', 1)

            ->count();

        return $totalAssignments > 0 ? ($completedAssignments / $totalAssignments) * 100 : 0;

    }

    private function calculateWasteStats($employeeId, $startDate, $endDate) {

        $wasteStats = [];



        $productions = Utilisation::where('producteur', $employeeId)

            ->whereBetween('created_at', [$startDate, $endDate])

            ->get();

        foreach($productions as $prod) {

            $recommended = MatiereRecommander::where('produit', $prod->produit)

                ->where('matierep', $prod->matierep)

                ->first();

            if($recommended) {

                $expectedUsage = ($prod->quantite_produit / $recommended->quantitep) * $recommended->quantite;

                $actualUsage = $prod->quantite_matiere;

                $waste = max(0, $actualUsage - $expectedUsage);



                if(!isset($wasteStats[$prod->produit])) {

                    $product = Produit_fixes::find($prod->produit);

                    $wasteStats[$prod->produit] = [

                        'name' => $product->nom,

                        'waste_percentage' => 0,

                        'total_waste' => 0

                    ];

                }



                $wasteStats[$prod->produit]['total_waste'] += $waste;

                $wasteStats[$prod->produit]['waste_percentage'] =

                    ($waste / $actualUsage) * 100;

            }

        }

        return $wasteStats;

    }

    private function getProductionEvolution($employeeId, $startDate, $endDate) {
        // Première étape : obtenir une valeur unique de quantité produite par lot
        $lotsUniques = DB::table('Utilisation')
            ->select('id_lot', DB::raw('DATE(created_at) as date'), 'quantite_produit')
            ->where('producteur', $employeeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('id_lot', DB::raw('DATE(created_at)'), 'quantite_produit')
            ->get();

        // Deuxième étape : regrouper par date et sommer les quantités
        $productionParJour = collect($lotsUniques)
            ->groupBy('date')
            ->map(function ($group) {
                return [
                    'date' => $group[0]->date,
                    'total' => $group->sum('quantite_produit')
                ];
            })
            ->values();

        return $productionParJour;
    }

    private function calculateWastageRate($idLot)
{
    // Récupérer toutes les utilisations pour ce lot
    $utilisations = Utilisation::where('id_lot', $idLot)->get();

    $totalGaspillageQuantite = 0;    // Gaspillage total en quantité
    $totalQuantiteRecommandee = 0;   // Total des quantités recommandées

    foreach ($utilisations as $utilisation) {
        // Récupérer la quantité recommandée pour ce produit et cette matière
        $recommandation = MatiereRecommander::where('produit', $utilisation->produit)
            ->where('matierep', $utilisation->matierep)
            ->first();

        if ($recommandation) {
            // Calculer la quantité recommandée proportionnellement
            $qteRecommandee = ($recommandation->quantite * $utilisation->quantite_produit) / $recommandation->quantitep;

            // Calculer le gaspillage en quantité réelle
            $gaspillageQuantite = max(0, $utilisation->quantite_matiere - $qteRecommandee);

            // Ajouter au total
            $totalGaspillageQuantite += $gaspillageQuantite;
            $totalQuantiteRecommandee += $qteRecommandee;
        }
    }

    // Calculer le taux de gaspillage global
    // (Total gaspillage / Total recommandé) * 100
    if ($totalQuantiteRecommandee > 0) {
        $tauxGaspillageGlobal = ($totalGaspillageQuantite / $totalQuantiteRecommandee) * 100;
        return round($tauxGaspillageGlobal, 2);
    }

    return 0;
}


}
