<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class EmployeeProductionController extends Controller

{

    public function index()

    {

        // Récupérer tous les employés de production (boulangers et pâtissiers)

        $employees = User::whereIn('role', ['boulanger', 'patissier'])->get();



        return view('production.employees', compact('employees'));

    }



    public function showEmployeeDetails(Request $request, $id)
    {
        // Filtres de date
        $period = $request->input('period', 'all');
        $startDate = null;
        $endDate = null;

        // Définir les dates en fonction du filtre
        if ($period !== 'all') {
            $endDate = Carbon::now();

            switch ($period) {
                case 'day':
                    $startDate = Carbon::now()->startOfDay();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    break;
                default:
                    $startDate = null;
                    $endDate = null;
            }
        }

        // Récupérer l'employé
        $employee = User::findOrFail($id);

        // Base query pour l'utilisation
        $utilisationQuery = DB::table('Utilisation')
            ->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
            ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
            ->where('Utilisation.producteur', $id);

        // Appliquer le filtre de date si nécessaire
        if ($startDate && $endDate) {
            $utilisationQuery->whereBetween('Utilisation.created_at', [$startDate, $endDate]);
        }

        // Récupérer les données d'utilisation
        $utilisations = $utilisationQuery->select(
            'Utilisation.id',
            'Utilisation.id_lot',
            'Produit_fixes.nom as nom_produit',
            'Produit_fixes.prix as prix_produit',
            'Utilisation.quantite_produit',
            'Matiere.nom as nom_matiere',
            'Matiere.prix_par_unite_minimale',
            'Utilisation.quantite_matiere',
            'Utilisation.unite_matiere',
            'Utilisation.created_at'
        )->orderBy('Utilisation.created_at', 'desc')->get();

        // Calculer les factures de matières et de produits
        $materialsInvoice = [];
        $productsInvoice = [];
        $totalMaterialsCost = 0;
        $totalProductsValue = 0;

        // Traiter les utilisations par lot de production
        $productionLots = [];

        // Organiser les données par lot
        foreach ($utilisations as $utilisation) {
            if (!isset($productionLots[$utilisation->id_lot])) {
                $productionLots[$utilisation->id_lot] = [
                    'nom_produit' => $utilisation->nom_produit,
                    'prix_produit' => $utilisation->prix_produit,
                    'quantite_produit' => $utilisation->quantite_produit,
                    'materials' => []
                ];
            }

            // Ajouter les matières utilisées pour ce lot
            $productionLots[$utilisation->id_lot]['materials'][] = [
                'nom_matiere' => $utilisation->nom_matiere,
                'prix_par_unite_minimale' => $utilisation->prix_par_unite_minimale,
                'quantite_matiere' => $utilisation->quantite_matiere,
                'unite_matiere' => $utilisation->unite_matiere
            ];
        }

        // Pour les matières premières (facture) et les produits
        foreach ($productionLots as $lotId => $lot) {
            // Ajouter à la facture des produits
            $productKey = $lot['nom_produit'];
            $productValue = $lot['quantite_produit'] * $lot['prix_produit'];

            if (!isset($productsInvoice[$productKey])) {
                $productsInvoice[$productKey] = [
                    'nom' => $lot['nom_produit'],
                    'quantite' => $lot['quantite_produit'],
                    'prix_unitaire' => $lot['prix_produit'],
                    'valeur_totale' => $productValue
                ];
            } else {
                $productsInvoice[$productKey]['quantite'] += $lot['quantite_produit'];
                $productsInvoice[$productKey]['valeur_totale'] += $productValue;
            }

            $totalProductsValue += $productValue;

            // Traiter les matières de ce lot
            foreach ($lot['materials'] as $material) {
                $materialKey = $material['nom_matiere'];
                $materialCost = $material['quantite_matiere'] * $material['prix_par_unite_minimale'];

                if (!isset($materialsInvoice[$materialKey])) {
                    $materialsInvoice[$materialKey] = [
                        'nom' => $material['nom_matiere'],
                        'quantite' => $material['quantite_matiere'],
                        'unite' => $material['unite_matiere'],
                        'cout_unitaire' => $material['prix_par_unite_minimale'],
                        'cout_total' => $materialCost
                    ];
                } else {
                    $materialsInvoice[$materialKey]['quantite'] += $material['quantite_matiere'];
                    $materialsInvoice[$materialKey]['cout_total'] += $materialCost;
                }

                $totalMaterialsCost += $materialCost;
            }
        }

        // Calculer le ratio dépenses/gains
        $ratio = $totalMaterialsCost > 0 ? ($totalProductsValue / $totalMaterialsCost) : 0;

        // Récupérer les données historiques pour le graphique
        $historicalData = $this->getHistoricalRatioData($id);

        return view('production.employee_details', compact(
            'employee',
            'materialsInvoice',
            'productsInvoice',
            'totalMaterialsCost',
            'totalProductsValue',
            'ratio',
            'period',
            'historicalData'
        ));
    }

    private function getHistoricalRatioData($employeeId)
    {
        // Récupérer les données pour les 12 derniers mois
        $monthlyData = [];

        for ($i = 0; $i < 12; $i++) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth();

            // Récupérer les utilisations pour ce mois
            $utilisations = DB::table('Utilisation')
                ->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
                ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
                ->where('Utilisation.producteur', $employeeId)
                ->whereBetween('Utilisation.created_at', [$startDate, $endDate])
                ->select(
                    'Utilisation.id_lot',
                    'Utilisation.quantite_produit',
                    'Produit_fixes.prix as prix_produit',
                    'Utilisation.quantite_matiere',
                    'Matiere.prix_par_unite_minimale'
                )
                ->get();

            // Organiser les données par lot
            $productionLots = [];
            foreach ($utilisations as $utilisation) {
                if (!isset($productionLots[$utilisation->id_lot])) {
                    $productionLots[$utilisation->id_lot] = [
                        'quantite_produit' => $utilisation->quantite_produit,
                        'prix_produit' => $utilisation->prix_produit,
                        'materials' => []
                    ];
                }

                $productionLots[$utilisation->id_lot]['materials'][] = [
                    'quantite_matiere' => $utilisation->quantite_matiere,
                    'prix_par_unite_minimale' => $utilisation->prix_par_unite_minimale
                ];
            }

            $monthMaterialCost = 0;
            $monthProductValue = 0;

            // Calculer les coûts et les gains pour chaque lot
            foreach ($productionLots as $lotId => $lot) {
                $monthProductValue += $lot['quantite_produit'] * $lot['prix_produit'];

                foreach ($lot['materials'] as $material) {
                    $monthMaterialCost += $material['quantite_matiere'] * $material['prix_par_unite_minimale'];
                }
            }

            $monthRatio = $monthMaterialCost > 0 ? ($monthProductValue / $monthMaterialCost) : 0;

            $monthlyData[] = [
                'month' => $startDate->format('M Y'),
                'ratio' => $monthRatio,
                'depense' => $monthMaterialCost,
                'gain' => $monthProductValue
            ];
        }

        // Inverser le tableau pour avoir les mois dans l'ordre chronologique
        return array_reverse($monthlyData);
    }

}
