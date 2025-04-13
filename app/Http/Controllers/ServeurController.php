<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProduitRecu;
use App\Models\Produit_fixes;
use App\Models\Production;
use App\Models\User;
use App\Models\VersementCsg;
use App\Models\ProduitStock;
use App\Models\VersementChef;
use App\Models\TransactionVente;
use App\Models\Production_suggerer_par_jour;
use Carbon\Carbon;
use App\Http\Controllers\NotificationController;
use App\Traits\HistorisableActions;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServeurController extends Controller
{
    use HistorisableActions;
    public function __construct(NotificationController $notificationController)
	{
    		$this->notificationController = $notificationController;
	}
    public function dashboard() {
        $employe = auth()->user();

         if (!$employe) {
           return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $nom = $employe->name;
        $produits = ProduitRecu::latest()->get();
        $proV=TransactionVente::latest()->get();
        $produitInv=TransactionVente::latest()->get();
        $Versement=VersementCsg::latest()->get();
        $user = User::where('id', $employe->id)->first();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');

        return view('pages/serveur/serveur_dashboard',['produits' => $produits,'proV'=>$proV,'produitInv'=>$produitInv,'Versement'=>$Versement,'user'=>$user, 'nom'=>$user->name,'heure_actuelle' => $heure_actuelle]);
    }

    public function store(Request $request) {
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

            // Réponse JSON en cas de succès
            return response()->json([
                'success' => true,
                'message' => 'Produit ajouté avec succès',
                'data' => $produit
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Réponse JSON en cas d'erreur de validation
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Réponse JSON en cas d'erreur générale
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'ajout du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function ajouterProduit_recu(){
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
       }
       $nom = $employe->name;
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $Employe=User::where('role','pointeur')->get();
        $produitR=Produit_fixes::all();
        $produits = \DB::table('Produit_recu')
        ->join('Produit_fixes', 'Produit_recu.produit', '=', 'Produit_fixes.code_produit')
        ->join('users', 'Produit_recu.pointeur', '=', 'users.id')
        ->select(
            'Produit_recu.*',
            'Produit_fixes.nom as nom',
            'Produit_fixes.prix as prix',
            'users.name as pointeur'
        )
        ->orderBy('Produit_recu.code_produit', 'desc') // Assurez-vous que la colonne date existe dans la table 'Produit_recu'
        ->get();



        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages/serveur/serveur-ajouterProduit_recu',compact('Employe','produitR','produits','heure_actuelle','nom'));

    }

    public function store_vendu(Request $request) {

    }

    // Méthode pour gérer les avaries (à ajouter au controller)
    public function declare_avarie(Request $request) {
        $employe = auth()->user();

        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        try {
            $validated = $request->validate([
                'produit' => 'required|exists:Produit_fixes,code_produit',
                'quantite' => 'required|numeric|min:1',
                'raison' => 'required|string',
            ]);

            $produitR = Produit_fixes::where('code_produit', $request->produit)->first();
            $stockProduit = ProduitStock::where('id_produit', $request->produit)->first();

            if (!$stockProduit) {
                return back()->with('error', 'Aucun stock disponible pour ce produit');
            }

            if ($stockProduit->quantite_en_stock < $request->quantite) {
                return back()->with('error', 'Quantité d\'avarie supérieure au stock disponible');
            }

            // Mettre à jour le stock pour ajouter les avaries
            $stockProduit->quantite_en_stock -= $request->quantite;
            $stockProduit->quantite_avarie += $request->quantite;
            $stockProduit->save();

            // Historiser l'action
            $this->historiser("Déclaration de {$request->quantite} unité(s) avariées du produit {$produitR->nom} par {$employe->name}. Raison: {$request->raison}", 'avarie');

            // Envoyer une notification aux responsables
            $request->merge([
                'recipient_id' => 1, // Administrateur ou responsable (à ajuster)
                'subject' => 'Déclaration d\'avarie',
                'message' => "L'employé {$employe->name} a déclaré {$request->quantite} unité(s) avariées du produit {$produitR->nom}. Raison: {$request->raison}"
            ]);

            $this->notificationController->send($request);

            return redirect()->route('serveur-dashboard')->with('success', 'Avarie déclarée avec succès');
        } catch(\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de la déclaration d\'avarie: ' . $e->getMessage());
        }
    }

    public function enrProduit_vendu(){
        $employe = auth()->user();
        $nom = $employe->name;
        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $produitR=Produit_fixes::all();
        $proV=\DB::table('transaction_ventes')
        ->join('Produit_fixes','transaction_ventes.produit','=','Produit_fixes.code_produit')
        ->join('users', 'transaction_ventes.serveur', '=', 'users.id')
        ->select('transaction_ventes.*',
                'Produit_fixes.nom as produit',
                'Produit_fixes.prix as prix'
        )
        ->where('transaction_ventes.type','=','Vente')
        ->where('transaction_ventes.serveur','=',$employe->id)
        ->get();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages/serveur/serveur-enrProduit_vendu',compact('produitR','proV','heure_actuelle','nom'));
    }
    public function store_invendu(Request $request){
        $employe = auth()->user();

        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        try {
            $validated = $request->validate([
                'produit' => 'required|exists:Produit_fixes,code_produit',
                'quantite' => 'required|numeric|min:1',
                'prix' => 'required|numeric|min:0',
                'type' => 'required',
            ]);

            // Vérifier que quantité et prix sont positifs
            if ($request->quantite <= 0 || $request->prix <= 0) {
                return back()->with('error', 'La quantité et le prix doivent être supérieurs à zéro');
            }

            $produitR = Produit_fixes::where('code_produit', $request->produit)->first();

            // Vérifier que le prix correspond au prix du produit
            if ($produitR->prix != $request->prix) {
                session()->flash('error', "Le prix saisi ne correspond pas au prix réel du produit sélectionné. Le prix doit être de {$produitR->prix}.");
                return back();
            }

            // Récupérer le stock du produit
            $stockProduit = ProduitStock::where('id_produit', $request->produit)->first();
            if (!$stockProduit) {
                return back()->with('error', 'Aucun stock disponible pour ce produit');
            }
            // Vérifier si la quantité vendue est supérieure à la quantité en stock
            if ($stockProduit->quantite_en_stock < $request->quantite) {
                $cp = User::where('role', 'chef_production')->get();
                foreach ($cp as $chef) {
                    $user = User::where('id', $chef->id)->first();
                    //notification
                    $request->merge([
                    'recipient_id' => $user->id,
                    'subject' => 'Alerte de stock',
                    'message' => "Le produit {$produitR->nom} est en rupture de stock et le(la) vendeur(se) {$user->name} souhaite enregistrer une vente en utilisant une quantité supérieure à celle en stock, ce qui indique soit une erreur, soit une absence d'enregistrement préalable.Ceci pourrait creer un stock negatif et un probleme dans la gestion des stocks. Veuillez vérifier et corriger la situation.",
                ]);
                $this->notificationController->send($request);
                }
                return back()->with('error', 'Quantité vendue supérieure au stock disponible Rapprocher vous du chef de production ou du producteur pour qu\'il enregistre la production');
            }

            // Récupérer la quantité invendue de la journée précédente
            $hier = Carbon::yesterday()->format('Y-m-d');
            $qteInvenduePrecedente = $stockProduit->quantite_invendu;


            // Créer la transaction de vente
            $proV = TransactionVente::create([
                'produit' => $request->produit,
                'serveur' => $employe->id,
                'quantite' => $request->quantite,
                'prix' => $request->prix,
                'date_vente' => Carbon::now(),
                'type' => $request->type,
                'monnaie' => 'FCFA',
            ]);

            // Mettre à jour le stock après la vente
            //si le type c'est invendu,ne pas diminuer le stock
            //pour chaque type (vente,avarie,invendu) on doit faire une mise à jour du stock
            if ($request->type == 'Produit invendu') {
                $stockProduit->quantite_invendu += $request->quantite;
            } elseif ($request->type == 'Produit Avarie') {
                $stockProduit->quantite_avarie += $request->quantite;
            }
            if ($request->type != 'Produit invendu') {
                $stockProduit->quantite_en_stock -= $request->quantite;
            }
            $stockProduit->save();

            // Historiser l'action
            $this->historiser("Vente de {$request->quantite} unité(s) du produit {$produitR->nom} par {$employe->name}", 'vente');

            return redirect()->back()->with('success', 'Vente enregistrée avec succès');
        } catch(\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage());
        }
    }



        public function produit_invendu(){
            $employe = auth()->user();
            $nom = $employe->name;
            $produitR=Produit_fixes::all();
            $heure_actuelle = now();
            $heure_actuelle->setTimezone('UTC');
            $proV=\DB::table('transaction_ventes')
        ->join('Produit_fixes','transaction_ventes.produit','=','Produit_fixes.code_produit')
        ->join('users', 'transaction_ventes.serveur', '=', 'users.id')
        ->select('transaction_ventes.*',
                'Produit_fixes.nom as produit',
                'Produit_fixes.prix as prix'
        )
        ->where('transaction_ventes.type','=','Vente')
        ->where('transaction_ventes.serveur','=',$employe->id)
        ->get();
            return view('pages/serveur/serveur-produit_invendu',compact('proV','produitR','heure_actuelle','nom'));
        }
        public function store_versement(Request $request){
            $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
            $validated= $request->validate([
                'libelle'=>'required',
                'date'=>'required',
               'somme'=>'required',
               'encaisseur'=>'required|exists:users,id',
             ]);
              $Versement=VersementCsg::create([
                'libelle'=>$request->libelle,
                'date'=>$request->date,
                'somme'=>$request->somme,
                'verseur'=>$employe->id,
                'encaisseur'=>$request->encaisseur,

              ]);
              return redirect()->route('serveur-dashboard')->with('success', 'Versement effectues avec succès');
        }
        public function versement(){
            $versement=User::where('role','chef_production')->get();
            $heure_actuelle = now();
            $heure_actuelle->setTimezone('UTC');
            return view('pages/serveur/serveur-versement_cp',compact('versement','heure_actuelle'));
        }
        public function fiche_versement(){
         $employe = auth()->user();

        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        // Obtenir le premier et dernier jour du mois courant
        $debut_mois = now()->startOfMonth();
        $fin_mois = now()->endOfMonth();

        // Récupérer  les versements du mois pour tous les produits

        $versements_mois = VersementCsg::where('verseur', $employe->id)
            ->whereBetween('created_at', [$debut_mois, $fin_mois])
            ->get()
            ->groupBy('produit');

        // Collection pour stocker les statistiques des versements
        $statistiques_versements = collect();

        foreach ($versements_mois as $code_vcsg => $versement_csgs) {
            // Obtenir les informations du versement
            $verseM = VersementCsg::where('code_vcsg', $code_vcsg)->first();

            if ($verseM) {
                // Calculer les statistiques pour ce versement
                $montant_totale = $versement_csgs->sum('somme');
                // Grouper par jour pour voir l'évolution
                $versements_par_jour = $versement_csgs
                    ->groupBy(function($versement){
                        return $versement->created_at->format('Y-m-d');
                    })
                    ->map(function($groupe) {
                        return $groupe->sum('somme');
                    });

                // Calculer la moyenne journalière
                $moyenne_journaliere = $montant_totale / max($versements_par_jour->count(),1);

                // Trouver le jour avec le versement maximale
                $jour_max_versement = $versements_par_jour->max();
                $date_max_versement = $versements_par_jour
                    ->filter(function($somme) use ($jour_max_versement) {
                        return $somme == $jour_max_versement;
                    })
                    ->keys()
                    ->first();

                $statistiques_versements->push([
                    'libelle' => $verseM->libelle,
                    'code_vcsg' => $code_vcsg,
                    'montant_totale' => $montant_totale,
                    'moyenne_journaliere' => round($moyenne_journaliere, 2),
                    'versement_max' => [
                        'somme' => $jour_max_versement,
                        'date' => $date_max_versement
                    ],
                    'versements_journalieres' => $versements_par_jour->toArray(),
                ]);
            }
        }

        // Récupérer les informations de l'employé
        $info = User::where('id', $employe->id)->first();

        // Retour correct pour la vue avec un tableau de données
        return view('pages\serveur\ serveur-fiche_versement', [
            'statistiques' => $statistiques_versements->toArray(),
            'mois_actuel' => now()->format('F Y'),
            'debut_mois' => $debut_mois->format('Y-m-d'),
            'fin_mois' => $fin_mois->format('Y-m-d'),
            'nom' => $info->name,
            'secteur' => $info->secteur,
            'num_tel' => $info->num_tel,
        ]);
        }


            public function stats($period = 'current')
            {
                $nom = auth()->user()->name;
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

                // Récupérer les produits avec leurs données de réception et de vente dans la période donnée
                $produits = Produit_fixes::with(['receptions' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }, 'ventes' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }])->get();

                // Calcul des statistiques pour chaque produit
                $stats = $produits->map(function ($produit) {
                    $receptionsVentes=$produit->receptions->filter(function($reception){
                         return $reception->type=='Vente';
                    });
                    $receptionsRecu=$produit->receptions(function($reception){
                        return $reception;
                   });

                    $receptionsInVendu=$produit->receptions->filter(function($reception){
                        return $reception->type=='Produit invendu';
                   });
                   $InVendu=$produit->ventes->filter(function($vente){
                    return $vente->type=='Produit invendu';
               });
               $Vendu=$produit->ventes->filter(function($vente){
                return $vente->type=='Vente';
                 });
                $Avarie=$produit->ventes->filter(function($avarie){
                    return $avarie->type=='Produit Avarie';
                });
                    $totalRecu = $produit->receptions->sum(function ($reception) {
                        return $reception->prix * $reception->quantite;
                    });

                    $totalVendu = $Vendu->sum(function ($vente) {
                        return $vente->prix * $vente->quantite;
                    });

                    $quantiteRecu = $produit->receptions->sum('quantite');
                    $quantiteVendue = $Vendu->sum('quantite');
                    $quantiteInVendue = $InVendu->sum('quantite');
                    $quantiteAvarie=$Avarie->sum('quantite');
                    $qte=$quantiteVendue+$quantiteInVendue+$quantiteAvarie-$quantiteRecu;
                    $totalManquant=($qte * $Avarie->sum('prix'));

                    $perte = $Avarie->sum(function($avarie){
                        return ($avarie->prix*$avarie->quantite);
                       });

                    return [
                        'nom' => $produit->nom,
                        'quantite_recue' => $quantiteRecu,
                        'quantite_vendue' => $quantiteVendue,
                        'quantite_invendu'=>$quantiteInVendue,
                        'total_recu' => $totalRecu,
                        'total_vendu' => $totalVendu,
                        'ttavarie'=>$totalManquant,
                        'perte' => $perte,
                    ];
                });
                $dailyStats = [];
                foreach ($produits as $produit) {
                    foreach ($produit->ventes as $vente) {
                        $day = $vente->created_at->format('Y-m-d');
                        $type = $vente->type;

                        if (!isset($dailyStats[$day])) {
                            $dailyStats[$day] = [
                                'recus' => [],
                                'vendus' => [],
                                'avarie' => [],
                                'manquants' => 0,
                            ];
                        }

                        if ($type === 'Vente') {
                            $dailyStats[$day]['vendus'][] = $produit->nom;
                        } elseif ($type === 'Produit Avarie') {
                            $dailyStats[$day]['avarie'][] = $produit->nom;
                            $dailyStats[$day]['manquants'] = -1*($vente->prix * $vente->quantite);
                        } else {
                            $dailyStats[$day]['recus'][] = $produit->nom;
                        }
                    }
                }

                // Calcul des totaux globaux
                $totalProducts = $stats->sum('quantite_recue');
                $totalSold = $stats->sum('quantite_vendue');
                $totalNoSold=$stats->sum('quantite_invendu');
                $totalCost = $stats->sum('total_recu');
                $totalRevenue = $stats->sum('total_vendu');
                $ttavarie=$stats->sum('ttavarie');
                $totalLosses = $stats->sum('perte');

                return view('pages/serveur/serveur-stats', compact(
                    'stats',
                    'totalProducts',
                    'totalSold',
                    'totalNoSold',
                    'totalCost',
                    'totalRevenue',
                    'ttavarie',
                    'totalLosses',
                    'period',
                    'dailyStats',
                    'nom'
                ));
            }

        public function statistique(){
            $produits = \DB::table('transaction_ventes')
            ->join('Produit_fixes', 'transaction_ventes.produit', '=', 'Produit_fixes.code_produit')
            ->select(
                'Produit_fixes.nom as produit_nom',
                \DB::raw('SUM(CASE WHEN transaction_ventes.type = "Vente" THEN transaction_ventes.quantite ELSE 0 END) as total_quantite_vendu'),
                \DB::raw('SUM(CASE WHEN transaction_ventes.type = "Produit invendu" THEN transaction_ventes.quantite ELSE 0 END) as total_quantite_invendu')
            )
            ->groupBy('Produit_fixes.nom')
            ->get();
            $ventesParJour = \DB::table('transaction_ventes')
            ->select(
                \DB::raw('DATE(created_at) as date'),
                \DB::raw('SUM(CASE WHEN type = "Vente" THEN quantite ELSE 0 END) as ventes'),
                \DB::raw('SUM(CASE WHEN type = "Produit invendu" THEN quantite ELSE 0 END) as invendus')
            )
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();
            $employe = auth()->user();
            $nom = $employe->nom;




    // Récupérer les ventes du mois
    $currentMonth = now()->month;
    $salesData = \DB::table('transaction_ventes')
        ->join('Produit_fixes', 'transaction_ventes.produit', '=', 'Produit_fixes.code_produit')
        ->whereMonth('transaction_ventes.date_vente', $currentMonth)
        ->select('Produit_fixes.nom', \DB::raw('SUM(transaction_ventes.quantite) as total_vendu'))
        ->groupBy('Produit_fixes.nom')
        ->get();

    // Préparer les données pour le graphique
    $productNames = $salesData->pluck('nom')->toArray();
    $productSales = $salesData->pluck('total_vendu')->toArray();

        return view('pages/serveur/serveur-stats-produit', compact('produits','ventesParJour','productNames','productSales','nom'));
        }
        public function classement()
    {
        $classements = \DB::table('transaction_ventes')
        ->join('users', 'transaction_ventes.serveur', '=', 'users.id')
        ->select('users.name', \DB::raw('SUM(transaction_ventes.prix * transaction_ventes.quantite) as total_ventes'))
        ->where('transaction_ventes.type', 'vente')
        ->whereMonth('transaction_ventes.created_at', now()->month)
        ->whereYear('transaction_ventes.created_at', now()->year)
        ->groupBy('users.id', 'users.name')
        ->orderBy('total_ventes', 'desc')
        ->get();

        return view('pages/serveur/serveur-classement', ['classements' => $classements]);
    }


    public function rapportVente()
    {
        $employe = auth()->user();

        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        // Filtrer les ventes par mois et serveur connecté
        $currentMonth = now()->format('m');
        $ventes = \DB::table('transaction_ventes')
            ->join('Produit_fixes', 'transaction_ventes.produit', '=', 'Produit_fixes.code_produit')
            ->select('Produit_fixes.nom as produit', 'transaction_ventes.quantite', 'transaction_ventes.prix', 'transaction_ventes.date_vente')
            ->whereMonth('transaction_ventes.date_vente', $currentMonth)
            ->where('transaction_ventes.serveur', $employe->id)
            ->where('transaction_ventes.type', 'Vente')
            ->get();

        // Calculer la recette totale
        $recetteTotale = $ventes->sum(function ($vente) {
            return $vente->quantite * $vente->prix;
        });

        return view('pages/serveur/serveur-rapport', compact('employe', 'ventes', 'recetteTotale'));
    }

    public function recupererInvendus(Request $request)
{
    // Récupérer la date d'hier
    $hier = now()->subDay()->format('Y-m-d');

    // Récupérer les produits invendus d'hier
    $invendus = TransactionVente::where('type', 'Produit invendu')
        ->whereDate('created_at', $hier)
        ->get();

    // Vérifier s'il y a des invendus
    if ($invendus->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Aucun produit invendu hier trouvé.'
        ]);
    }

    DB::beginTransaction();
    try {
        foreach ($invendus as $produit) {
            // Ajouter le produit à la table produit_recu
            ProduitRecu::create([
                'pointeur' => $produit->serveur, // On stocke le serveur qui a pointé les invendus
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

            $user = auth()->user();
            // Enregistrer dans l'historique
            $this->historiser("{$user->name} a recuperer les invendus de la journer precedente",'recuperation_invendu');
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Les produits invendus ont été récupérés avec succès.']);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur lors de la récupération des invendus: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Erreur lors de la récupération des invendus']);
    }
}
    /**************************************** */
    public function store_versement_cp(Request $request){
          return redirect()->route('serveur-dashboard')->with('success', 'Versement effectues avec succès');
    }
    /**************************************** */

    public function versement_cp(){
        $versement=User::where('role','chef_production')->get();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages.serveur.serveur-versement_cp',compact('versement','heure_actuelle'));
    }
    public function aide(){
        return view('pages/serveur/serveur-aide');
    }
 public function nbre_sacs(Request $request){
    $request->validate=([
   'quantite'=>'required',
   'sac'=>'required'
    ]);
    TransactionVente::create([
    'quantite'=>$request->quantite,
    'type'=>$request->sac
    ]);
 return redirect()->route('serveur-dashboard');
 }
   public function nbre_sacs_vente(){
    $nom=auth()->user()->name;
    return view('pages/serveur/serveur-sac',compact('nom'));
    }

}
