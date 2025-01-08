<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProduitRecu;
use App\Models\Produit_fixes;
use App\Models\Production;
use App\Models\User;
use App\Models\VersementCsg;
use App\Models\VersementChef;
use App\Models\Transaction_vente;
use App\Models\Production_suggerer_par_jour;
use Carbon\Carbon;
class ServeurController extends Controller
{
    public function dashboard() {
          $employe = auth()->user();

         if (!$employe) {
           return redirect()->route('login')->with('error', 'Veuillez vous connecter');
         }
        $produits = ProduitRecu::latest()->get();
        $proV=Transaction_vente::latest()->get();
        $produitInv=Transaction_vente::latest()->get();
        $Versement=VersementCsg::latest()->get();
        $user = User::where('id', $employe->id)->first();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        
        return view('pages/serveur/serveur_dashboard',['produits' => $produits,'proV'=>$proV,'produitInv'=>$produitInv,'Versement'=>$Versement,'user'=>$user, 'nom'=>$user->name,'heure_actuelle' => $heure_actuelle]);
    }
   public function store(Request $request){
    $validate=  $request->validate([
        'pointeur'=>'required',
        'produit'=>'required',
        'prix'=>'required',
        'quantite'=>'required',
        
        
        ]);
        $produits=ProduitRecu::create($validate);
        $produitR = Produit_fixes::where('code_produit',$request->produit)->first();
        foreach($produitR->productions as $production) { 
            $production->quantite += $request->quantite;
            $production->save(); 
        }
        return redirect()->route('serveur-dashboard')->with('Produit ajoute avec succes');

   }
    public function ajouterProduit_recu(){
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $Employe=User::where('role','pointeur')->get();
        $produitR=Produit_fixes::all();
        $produits = \DB::table('produit_recus')
        ->join('produit_fixes', 'produit_recus.produit', '=', 'produit_fixes.code_produit')
        ->join('users', 'produit_recus.pointeur', '=', 'users.id')
        ->select('produit_recus.*',
               'produit_fixes.nom as nom',      
                'produit_fixes.prix as prix',
                'users.name as pointeur'
        )
        ->get();
        
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages\serveur\ serveur-ajouterProduit_recu',compact('Employe','produitR','produits','heure_actuelle'));
      
    }
    public function store_vendu(Request $request) {
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
         try { 
            $validated = $request->validate([ 
                'produit' => 'required|exists:produit_fixes,code_produit',
                 'quantite' => 'required|numeric|min:1', 
                 'prix' => 'required|numeric|min:0', 
                 'type'=>'required',
                 'monnaie'=>'required',
                ]); 
            $produitR = Produit_fixes::where('code_produit',$request->produit)->first(); 
            if ($produitR->prix != $request->prix) {
                session()->flash('error', "Le prix saisi ne correspond pas au prix réel du produit sélectionné. Le prix doit être de {$produitR->prix_unitaire}.");
                return back();
            }
            $quantiteDisponible = $produitR->productions->sum('quantite');
             if($quantiteDisponible <= $request->quantite) {
                 return back()->with('error', 'Stock insuffisant pour la vente'); }
           
                    $quantiteRestante = $request->quantite;
                     foreach($produitR->productions as $production) { 
                        if($quantiteRestante <= 0) break;
                         $reduction = min($production->quantite, $quantiteRestante); 
                         $production->quantite -= $reduction;
                          $production->save(); 
                          $quantiteRestante -= $reduction; }
                          $proV= Transaction_vente::create([
                           'produit'=>$request->produit,
                            'serveur'=>$employe->id,
                            'quantite'=>$request->quantite,
                            'prix'=>$request->prix,
                           
                            'date_vente'=>Carbon::now(),
                            'type'=>$request->type,
                            'monnaie'=>$request->monnaie
                            
                        ]);
                                return redirect()->route('serveur-dashboard')->with('success', 'Vente enregistrée avec succès');
                             }
                                 catch(\Exception $e) { 
                                    return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage());
                                 }
                                 } 
    public function enrProduit_vendu(){
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $produitR=Produit_fixes::all();
        $proV=\DB::table('transaction_ventes')
        ->join('produit_fixes','transaction_ventes.produit','=','produit_fixes.code_produit')
        ->join('users', 'transaction_ventes.serveur', '=', 'users.id')
        ->select('transaction_ventes.*',
                'produit_fixes.nom as produit',
                'produit_fixes.prix as prix'
        )
        ->where('transaction_ventes.type','=','Vente')
        ->where('transaction_ventes.serveur','=',$employe->id)
        ->get();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages\serveur\ serveur-enrProduit_vendu',compact('produitR','proV','heure_actuelle'));
    }
    public function store_invendu(Request $request){
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        try { 
        $validated = $request->validate([ 
            'produit' => 'required|exists:produit_fixes,code_produit',
             'quantite' => 'required|numeric|min:1', 
             'prix' => 'required|numeric|min:0', 
             'type'=>'required|string',
            
            ]); 
           // $produitR = Produit_fixes::where('code_produit',$request->produit)->first();
            $produitInv=Transaction_vente::create([
                'produit'=>$request->produit,
                'serveur'=>$employe->id,
                'quantite'=>$request->quantite,
                'prix'=>$request->prix,
                'type'=>$request->type,
                
            ]);
            return redirect()->route('serveur-dashboard')->with('success', 'Produits Invendus enregistrée avec succès'); 
        }
        catch(\Exception $e) { 
           return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement: ' . $e->getMessage());
        }
        }

        public function produit_invendu(){
            $produitR=Produit_fixes::all();
            $heure_actuelle = now();
            $heure_actuelle->setTimezone('UTC');
            return view('pages\serveur\ serveur-produit_invendu',compact('produitR','heure_actuelle'));
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
            return view('pages\serveur\ serveur-versement',compact('versement','heure_actuelle'));
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
                    'dailyStats'
                ));
            }   
            
        public function statistique(){
            $produits = \DB::table('transaction_ventes')
            ->join('produit_fixes', 'transaction_ventes.produit', '=', 'produit_fixes.code_produit')
            ->select(
                'produit_fixes.nom as produit_nom',
                \DB::raw('SUM(CASE WHEN transaction_ventes.type = "Vente" THEN transaction_ventes.quantite ELSE 0 END) as total_quantite_vendu'),
                \DB::raw('SUM(CASE WHEN transaction_ventes.type = "Produit invendu" THEN transaction_ventes.quantite ELSE 0 END) as total_quantite_invendu')
            )
            ->groupBy('produit_fixes.nom')
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
    
   

    // Récupérer les ventes du mois
    $currentMonth = now()->month;
    $salesData = \DB::table('transaction_ventes')
        ->join('produit_fixes', 'transaction_ventes.produit', '=', 'produit_fixes.code_produit')
        ->whereMonth('transaction_ventes.date_vente', $currentMonth)
        ->select('produit_fixes.nom', \DB::raw('SUM(transaction_ventes.quantite) as total_vendu'))
        ->groupBy('produit_fixes.nom')
        ->get();

    // Préparer les données pour le graphique
    $productNames = $salesData->pluck('nom')->toArray();
    $productSales = $salesData->pluck('total_vendu')->toArray();
    
        return view('pages/serveur/serveur-stats-produit', compact('produits','ventesParJour','productNames','productSales'));
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
            ->join('produit_fixes', 'transaction_ventes.produit', '=', 'produit_fixes.code_produit')
            ->select('produit_fixes.nom as produit', 'transaction_ventes.quantite', 'transaction_ventes.prix', 'transaction_ventes.date_vente')
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
        $invendus = Transaction_vente::where('type', 'Produit invendu')
        ->whereDate('created_at', $hier)
        ->get();

    // Vérifier s'il y a des invendus
    if ($invendus->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Aucun produit invendu  hier trouvé.'
        ]);
    }
        foreach ($invendus as $produit) {
            // Ajouter le produit à la table produit_recu
            ProduitRecu::create([
                'pointeur' => $produit->serveur, // On stocke le serveur qui a pointé les invendus
                'produit' => $produit->produit,
                'quantite' => $produit->quantite,
                'prix' => $produit->prix,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Les produits invendus ont été récupérés avec succès.']);
    }
    public function store_versement_cp(Request $request){
      
         $request->validate([
            'cp'=>'required|exists:users,id',
            'libelle'=>'required',
           'montant'=>'required',
         ]);
         $Versement=VersementChef::create([
            'chef_production'=>$request->cp,
            'libelle'=>$request->libelle,
            'montant'=>$request->montant,
            
          ]);
          return redirect()->route('serveur-dashboard')->with('success', 'Versement effectues avec succès'); 
    }
    public function versement_cp(){
        $versement=User::where('role','chef_production')->get();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages\serveur\serveur-versement_cp',compact('versement','heure_actuelle'));
    }
    public function aide(){
        return view('pages/serveur/serveur-aide');
    }
 public function nbre_sacs(Request $request){
    $request->validate=([
   'quantite'=>'required',
   'sac'=>'required'
    ]);
    Transaction_vente::create([
    'quantite'=>$request->quantite,
    'type'=>$request->sac
    ]);
 return redirect()->route('serveur-dashboard');
 }
   public function nbre_sacs_vente(){
    return view('pages/serveur/serveur-sac');
    } 

}