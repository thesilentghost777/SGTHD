<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProduitRecu;
use App\Models\Produit_fixes;
use App\Models\ProduitStock;
use App\Models\TransactionVente;
use App\Models\VersementCsg;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\HistorisableActions;

class ServeurApiController extends Controller
{
    use HistorisableActions;
    
    public function dashboard()
    {
        try {
            $user = auth()->user();
            
            // Récupérer les statistiques des produits
            $produits = [
                'en_stock' => ProduitStock::sum('quantite_en_stock'),
                'ventes_jour' => TransactionVente::where('type', 'Vente')
                    ->where('created_at', '>=', now()->startOfDay())
                    ->count(),
                'invendus' => TransactionVente::where('type', 'Produit invendu')->count()
            ];
            
            $data = [
                'nom' => $user->name,
                'heure_actuelle' => Carbon::now(),
                'produits' => $produits,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function stats(Request $request)
    {
        try {
            $period = $request->get('period', 'current');
            
            // Calcul des plages de dates en fonction de la période
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();

            if ($period === 'last') {
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
            } elseif ($period === '3months') {
                $startDate = now()->subMonths(3)->startOfMonth();
                $endDate = now()->endOfMonth();
            }

            // Récupérer les produits avec leurs données de réception et de vente
            $produits = Produit_fixes::all();
            
            $stats = [];
            foreach ($produits as $produit) {
                $ventes = TransactionVente::where('produit', $produit->code_produit)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();
                
                $ventesProduit = $ventes->where('type', 'Vente');
                $invendusProduit = $ventes->where('type', 'Produit invendu');
                $avariesProduit = $ventes->where('type', 'Produit Avarie');
                
                $quantiteVendue = $ventesProduit->sum('quantite');
                $quantiteInvendue = $invendusProduit->sum('quantite');
                $quantiteAvarie = $avariesProduit->sum('quantite');
                
                $totalVendu = $ventesProduit->sum(function ($vente) {
                    return $vente->prix * $vente->quantite;
                });
                
                $perte = $avariesProduit->sum(function($avarie) {
                    return $avarie->prix * $avarie->quantite;
                });
                
                $stats[] = [
                    'nom' => $produit->nom,
                    'quantite_vendue' => $quantiteVendue,
                    'quantite_invendu' => $quantiteInvendue,
                    'total_vendu' => $totalVendu,
                    'perte' => $perte,
                ];
            }
            
            // Calcul des totaux globaux
            $totalSold = array_sum(array_column($stats, 'quantite_vendue'));
            $totalNoSold = array_sum(array_column($stats, 'quantite_invendu'));
            $totalRevenue = array_sum(array_column($stats, 'total_vendu'));
            $totalLosses = array_sum(array_column($stats, 'perte'));
            
            $summary = [
                'total_vendu' => $totalSold,
                'total_invendu' => $totalNoSold,
                'total_revenu' => $totalRevenue,
                'total_perte' => $totalLosses,
            ];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'stats' => $stats,
                    'summary' => $summary,
                    'period' => $period
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function versements()
    {
        try {
            $user = auth()->user();
            
            // Obtenir le premier et dernier jour du mois courant
            $debut_mois = now()->startOfMonth();
            $fin_mois = now()->endOfMonth();
            
            // Récupérer les versements du mois pour l'utilisateur connecté
            $versements = VersementCsg::where('verseur', $user->id)
                ->whereBetween('created_at', [$debut_mois, $fin_mois])
                ->with('encaisseurUser:id,name')
                ->get();
                
            $montantTotal = $versements->sum('somme');
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'versements' => $versements,
                    'montant_total' => $montantTotal,
                    'debut_mois' => $debut_mois->format('Y-m-d'),
                    'fin_mois' => $fin_mois->format('Y-m-d'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function storeVendu(Request $request)
    {
        try {
            $user = auth()->user();
            
            $validated = $request->validate([
                'produit' => 'required|exists:Produit_fixes,code_produit',
                'quantite' => 'required|numeric|min:1',
                'prix' => 'required|numeric|min:0',
            ]);
            
            // Vérifier que le prix et la quantité sont positifs
            if ($request->quantite <= 0 || $request->prix <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'La quantité et le prix doivent être supérieurs à zéro'
                ], 422);
            }
            
            $produit = Produit_fixes::where('code_produit', $request->produit)->first();
            
            // Vérifier que le prix correspond au prix du produit
            if ($produit->prix != $request->prix) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Le prix saisi ne correspond pas au prix réel du produit sélectionné. Le prix doit être de {$produit->prix}."
                ], 422);
            }
            
            // Récupérer le stock du produit
            $stockProduit = ProduitStock::where('id_produit', $request->produit)->first();
            if (!$stockProduit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucun stock disponible pour ce produit'
                ], 422);
            }
            
            // Vérifier si la quantité vendue est supérieure à la quantité en stock
            if ($stockProduit->quantite_en_stock < $request->quantite) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Quantité vendue supérieure au stock disponible'
                ], 422);
            }
            
            // Créer la transaction de vente
            $vente = TransactionVente::create([
                'produit' => $request->produit,
                'serveur' => $user->id,
                'quantite' => $request->quantite,
                'prix' => $request->prix,
                'date_vente' => Carbon::now(),
                'type' => 'Vente',
                'monnaie' => 'FCFA',
            ]);
            
            // Mettre à jour le stock après la vente
            $stockProduit->quantite_en_stock -= $request->quantite;
            $stockProduit->save();
            
            // Historiser l'action
            $this->historiser("Vente de {$request->quantite} unité(s) du produit {$produit->nom} par {$user->name}", 'vente');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Vente enregistrée avec succès',
                'data' => $vente
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function storeInvendu(Request $request)
    {
        try {
            $user = auth()->user();
            
            $validated = $request->validate([
                'produit' => 'required|exists:Produit_fixes,code_produit',
                'quantite' => 'required|numeric|min:1',
                'prix' => 'required|numeric|min:0',
            ]);
            
            $produit = Produit_fixes::where('code_produit', $request->produit)->first();
            
            // Vérifier que le prix correspond au prix du produit
            if ($produit->prix != $request->prix) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Le prix saisi ne correspond pas au prix réel du produit sélectionné. Le prix doit être de {$produit->prix}."
                ], 422);
            }
            
            // Récupérer le stock du produit
            $stockProduit = ProduitStock::where('id_produit', $request->produit)->first();
            if (!$stockProduit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucun stock disponible pour ce produit'
                ], 422);
            }
            
            // Créer la transaction d'invendu
            $invendu = TransactionVente::create([
                'produit' => $request->produit,
                'serveur' => $user->id,
                'quantite' => $request->quantite,
                'prix' => $request->prix,
                'date_vente' => Carbon::now(),
                'type' => 'Produit invendu',
                'monnaie' => 'FCFA',
            ]);
            
            // Mettre à jour le stock pour enregistrer l'invendu
            $stockProduit->quantite_invendu += $request->quantite;
            $stockProduit->save();
            
            // Historiser l'action
            $this->historiser("Enregistrement de {$request->quantite} unité(s) invendues du produit {$produit->nom} par {$user->name}", 'invendu');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Produit invendu enregistré avec succès',
                'data' => $invendu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function declareAvarie(Request $request)
    {
        try {
            $user = auth()->user();
            
            $validated = $request->validate([
                'produit' => 'required|exists:Produit_fixes,code_produit',
                'quantite' => 'required|numeric|min:1',
                'raison' => 'required|string',
            ]);
            
            $produit = Produit_fixes::where('code_produit', $request->produit)->first();
            $stockProduit = ProduitStock::where('id_produit', $request->produit)->first();
            
            if (!$stockProduit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucun stock disponible pour ce produit'
                ], 422);
            }
            
            if ($stockProduit->quantite_en_stock < $request->quantite) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Quantité d\'avarie supérieure au stock disponible'
                ], 422);
            }
            
            // Créer la transaction d'avarie
            $avarie = TransactionVente::create([
                'produit' => $request->produit,
                'serveur' => $user->id,
                'quantite' => $request->quantite,
                'prix' => $produit->prix, // Utiliser le prix du produit
                'date_vente' => Carbon::now(),
                'type' => 'Produit Avarie',
                'monnaie' => 'FCFA',
                'commentaire' => $request->raison
            ]);
            
            // Mettre à jour le stock après l'avarie
            $stockProduit->quantite_en_stock -= $request->quantite;
            $stockProduit->quantite_avarie += $request->quantite;
            $stockProduit->save();
            
            // Historiser l'action
            $this->historiser("Déclaration de {$request->quantite} unité(s) avariées du produit {$produit->nom} par {$user->name}. Raison: {$request->raison}", 'avarie');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Avarie déclarée avec succès',
                'data' => $avarie
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la déclaration d\'avarie: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function recupererInvendus(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Récupérer la date d'hier
            $hier = now()->subDay()->format('Y-m-d');
            
            // Récupérer les produits invendus d'hier
            $invendus = TransactionVente::where('type', 'Produit invendu')
                ->whereDate('created_at', $hier)
                ->get();
                
            // Vérifier s'il y a des invendus
            if ($invendus->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Aucun produit invendu hier trouvé.'
                ], 404);
            }
            
            DB::beginTransaction();
            try {
                foreach ($invendus as $produit) {
                    // Ajouter le produit à la table produit_recu
                    ProduitRecu::create([
                        'pointeur' => $produit->serveur,
                        'produit' => $produit->produit,
                        'quantite' => $produit->quantite,
                        'prix' => $produit->prix,
                    ]);
                    
                    // Réinitialiser la quantité invendue dans produit_stocks
                    DB::table('produit_stocks')
                        ->where('id_produit', $produit->produit)
                        ->update([
                            'quantite_invendu' => 0,
                            'updated_at' => now()
                        ]);
                }
                
                // Enregistrer dans l'historique
                $this->historiser("{$user->name} a récupéré les invendus de la journée précédente", 'recuperation_invendu');
                
                DB::commit();
                return response()->json([
                    'status' => 'success', 
                    'message' => 'Les produits invendus ont été récupérés avec succès.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erreur lors de la récupération des invendus: ' . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la récupération des invendus: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'pointeur' => 'required',
                'produit' => 'required',
                'prix' => 'required|numeric|min:0',
                'quantite' => 'required|numeric|min:1',
                'date' => 'required|date',
            ]);
            
            // Création du produit
            $produit = ProduitRecu::create($validated);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Produit ajouté avec succès',
                'data' => $produit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de l\'ajout du produit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProduitsRecus()
    {
        try {
            $produitsRecus = \DB::table('Produit_recu')
                ->join('Produit_fixes', 'Produit_recu.produit', '=', 'Produit_fixes.code_produit')
                ->join('users', 'Produit_recu.pointeur', '=', 'users.id')
                ->select(
                    'Produit_recu.*',
                    'Produit_fixes.nom as nom_produit',
                    'Produit_fixes.prix as prix_fixe',
                    'users.name as nom_pointeur'
                )
                ->orderBy('Produit_recu.created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $produitsRecus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeProduitRecu(Request $request)
    {
        try {
            // Validation
            $validate = $request->validate([
                'pointeur' => 'required',
                'produit' => 'required',
                'prix' => 'required|numeric|min:0',
                'quantite' => 'required|numeric|min:1',
                'date' => 'required|date',
            ]);

            // Création du produit
            $produit = ProduitRecu::create($validate);

            return response()->json([
                'status' => 'success',
                'message' => 'Produit ajouté avec succès',
                'data' => $produit
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de l\'ajout du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
}



