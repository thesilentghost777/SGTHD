<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncoherenceController extends Controller
{
    public function index()
    {
        // Récupérer les données d'utilisation (production)
        $utilisations = DB::table('Utilisation')
            ->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
            ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
            ->select(
                'Utilisation.id_lot',
                'Produit_fixes.code_produit',
                'Produit_fixes.nom as nom_produit',
                'Produit_fixes.prix as prix_produit',
                'Utilisation.quantite_produit',
                'Utilisation.created_at as date_production',
                'Matiere.nom as nom_matiere',
                'Matiere.prix_par_unite_minimale',
                'Utilisation.quantite_matiere',
                'Utilisation.unite_matiere'
            )
            ->orderBy('Utilisation.created_at')
            ->get();

        // Récupérer les données de vente
        $ventes = DB::table('transaction_ventes')
            ->join('Produit_fixes', 'transaction_ventes.produit', '=', 'Produit_fixes.code_produit')
            ->select(
                'Produit_fixes.code_produit',
                'Produit_fixes.nom as nom_produit',
                'transaction_ventes.quantite',
                'transaction_ventes.prix',
                'transaction_ventes.date_vente'
            )
            ->orderBy('transaction_ventes.date_vente')
            ->get();

        // Organiser les productions par produit et par date
        $productionsParProduit = [];
        $productionsParDate = [];
        $coutFabricationParProduit = [];

        foreach ($utilisations as $utilisation) {
            $codeProduit = $utilisation->code_produit;
            $date = Carbon::parse($utilisation->date_production)->format('Y-m-d');

            // Productions par produit
            if (!isset($productionsParProduit[$codeProduit])) {
                $productionsParProduit[$codeProduit] = [
                    'nom' => $utilisation->nom_produit,
                    'quantite_totale' => 0,
                    'cout_total' => 0
                ];
            }

            $productionsParProduit[$codeProduit]['quantite_totale'] += $utilisation->quantite_produit;

            // Coût de fabrication par produit
            if (!isset($coutFabricationParProduit[$codeProduit])) {
                $coutFabricationParProduit[$codeProduit] = 0;
            }

            $coutMatiere = $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale;
            $coutFabricationParProduit[$codeProduit] += $coutMatiere;
            $productionsParProduit[$codeProduit]['cout_total'] += $coutMatiere;

            // Productions par date et par produit
            if (!isset($productionsParDate[$date])) {
                $productionsParDate[$date] = [];
            }

            if (!isset($productionsParDate[$date][$codeProduit])) {
                $productionsParDate[$date][$codeProduit] = [
                    'nom' => $utilisation->nom_produit,
                    'quantite' => 0,
                    'prix_unitaire' => $utilisation->prix_produit
                ];
            }

            $productionsParDate[$date][$codeProduit]['quantite'] += $utilisation->quantite_produit;
        }

        // Organiser les ventes par produit et par date
        $ventesParProduit = [];
        $ventesParDate = [];

        foreach ($ventes as $vente) {
            $codeProduit = $vente->code_produit;
            $date = Carbon::parse($vente->date_vente)->format('Y-m-d');

            // Ventes par produit
            if (!isset($ventesParProduit[$codeProduit])) {
                $ventesParProduit[$codeProduit] = [
                    'nom' => $vente->nom_produit,
                    'quantite_totale' => 0,
                    'valeur_totale' => 0
                ];
            }

            $ventesParProduit[$codeProduit]['quantite_totale'] += $vente->quantite;
            $ventesParProduit[$codeProduit]['valeur_totale'] += $vente->quantite * $vente->prix;

            // Ventes par date et par produit
            if (!isset($ventesParDate[$date])) {
                $ventesParDate[$date] = [];
            }

            if (!isset($ventesParDate[$date][$codeProduit])) {
                $ventesParDate[$date][$codeProduit] = [
                    'nom' => $vente->nom_produit,
                    'quantite' => 0,
                    'valeur' => 0
                ];
            }

            $ventesParDate[$date][$codeProduit]['quantite'] += $vente->quantite;
            $ventesParDate[$date][$codeProduit]['valeur'] += $vente->quantite * $vente->prix;
        }

        // Calculer le ratio produit/vendu pour chaque produit
        $ratioProduitsVendus = [];
        $alertesProduits = [];
        $recommandationsProduits = [];

        foreach ($productionsParProduit as $codeProduit => $production) {
            $quantiteProduite = $production['quantite_totale'];
            $quantiteVendue = isset($ventesParProduit[$codeProduit]) ? $ventesParProduit[$codeProduit]['quantite_totale'] : 0;
            $valeurVentes = isset($ventesParProduit[$codeProduit]) ? $ventesParProduit[$codeProduit]['valeur_totale'] : 0;
            $coutProduction = $production['cout_total'];

            // Calculer le ratio
            $ratio = $quantiteProduite > 0 ? ($quantiteVendue / $quantiteProduite) * 100 : 0;

            $ratioProduitsVendus[$codeProduit] = [
                'nom' => $production['nom'],
                'quantite_produite' => $quantiteProduite,
                'quantite_vendue' => $quantiteVendue,
                'ratio' => $ratio,
                'cout_production' => $coutProduction,
                'valeur_ventes' => $valeurVentes,
                'profit' => $valeurVentes - $coutProduction
            ];

            // Déterminer la recommandation
            if ($valeurVentes - $coutProduction > 0) {
                if ($ratio > 95) {
                    $recommandationsProduits[$codeProduit] = [
                        'nom' => $production['nom'],
                        'statut' => 'augmenter',
                        'message' => 'Augmenter la production (forte demande, bon profit)'
                    ];
                } else if ($ratio > 75) {
                    $recommandationsProduits[$codeProduit] = [
                        'nom' => $production['nom'],
                        'statut' => 'maintenir',
                        'message' => 'Maintenir la production (bon équilibre)'
                    ];
                } else {
                    $recommandationsProduits[$codeProduit] = [
                        'nom' => $production['nom'],
                        'statut' => 'reduire',
                        'message' => 'Réduire légèrement la production (surplus modéré)'
                    ];
                }
            } else {
                if ($ratio < 50) {
                    $recommandationsProduits[$codeProduit] = [
                        'nom' => $production['nom'],
                        'statut' => 'annuler',
                        'message' => 'Envisager d\'arrêter la production (faible demande, non rentable)'
                    ];
                } else {
                    $recommandationsProduits[$codeProduit] = [
                        'nom' => $production['nom'],
                        'statut' => 'optimiser',
                        'message' => 'Optimiser les coûts ou augmenter les prix (ventes correctes mais non rentables)'
                    ];
                }
            }
        }

        // Calculer les données pour le graphique d'évolution du ratio
        $evolutionRatio = [];
        $dates = array_unique(array_merge(array_keys($productionsParDate), array_keys($ventesParDate)));
        sort($dates);

        foreach ($dates as $date) {
            $evolutionRatio[$date] = [];

            foreach ($productionsParProduit as $codeProduit => $production) {
                $quantiteProduite = isset($productionsParDate[$date][$codeProduit]) ? $productionsParDate[$date][$codeProduit]['quantite'] : 0;
                $quantiteVendue = isset($ventesParDate[$date][$codeProduit]) ? $ventesParDate[$date][$codeProduit]['quantite'] : 0;
                $ratio = $quantiteProduite > 0 ? ($quantiteVendue / $quantiteProduite) * 100 : 0;

                $evolutionRatio[$date][$codeProduit] = [
                    'nom' => $production['nom'],
                    'ratio' => $ratio,
                    'quantite_produite' => $quantiteProduite,
                    'quantite_vendue' => $quantiteVendue
                ];

                // Vérifier s'il y a un écart important (perte > 5000 XAF)
                if ($quantiteProduite > $quantiteVendue) {
                    $invendus = $quantiteProduite - $quantiteVendue;
                    $prixUnitaire = isset($productionsParDate[$date][$codeProduit]) ? $productionsParDate[$date][$codeProduit]['prix_unitaire'] : 0;
                    $perteEstimee = $invendus * $prixUnitaire;

                    if ($perteEstimee > 5000) {
                        $alertesProduits[] = [
                            'date' => $date,
                            'produit' => $production['nom'],
                            'invendus' => $invendus,
                            'perte' => $perteEstimee
                        ];
                    }
                }
            }
        }

        // Trier les ratios pour trouver les top 5 meilleurs et pires
        uasort($ratioProduitsVendus, function ($a, $b) {
            return $b['ratio'] <=> $a['ratio'];
        });

        $topMeilleursRatios = array_slice($ratioProduitsVendus, 0, 5);
        $topPiresRatios = array_slice(array_reverse($ratioProduitsVendus, true), 0, 5);

        // Transformer les données pour les graphiques
        $dataEvolutionRatio = [];
        foreach ($evolutionRatio as $date => $produitsRatio) {
            foreach ($produitsRatio as $codeProduit => $data) {
                $dataEvolutionRatio[] = [
                    'date' => $date,
                    'produit' => $data['nom'],
                    'ratio' => round($data['ratio'], 2)
                ];
            }
        }

        return view('incoherence.index', [
            'ratioProduitsVendus' => $ratioProduitsVendus,
            'topMeilleursRatios' => $topMeilleursRatios,
            'topPiresRatios' => $topPiresRatios,
            'alertesProduits' => $alertesProduits,
            'recommandationsProduits' => $recommandationsProduits,
            'dataEvolutionRatio' => json_encode($dataEvolutionRatio)
        ]);
    }
}
