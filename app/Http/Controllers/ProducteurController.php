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

class ProducteurController extends Controller  // Hérite de Controller
{
    /*au debut de chaque fonction pensez a ajouter cet expression :
    $employe = auth()->user();

        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
    */

   
    
public function produit() {
    $employe = auth()->user();
    if (!$employe) {
        return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }
    
    // Récupérer les IDs des produits pour aujourd'hui
    $productIds = Production::where('producteur', $employe->id)
        ->whereDate('created_at', now()->toDateString())
        ->pluck('produit')
        ->toArray();

    // Récupérer les produits et leurs quantités
    $produits = Produit_fixes::whereIn('code_produit', $productIds)->get();
    $productions = Production::whereIn('produit', $productIds)
        ->where('producteur', $employe->id)
        ->whereDate('created_at', now()->toDateString())
        ->get();

    // Créer la collection combinée
    $p = collect();
    foreach ($produits as $produit) {
        $production = $productions->where('produit', $produit->code_produit)->first();
        if ($production) {
            $p->push([
                'nom' => $produit->nom,
                'prix' => $produit->prix,
                'quantite' => $production->quantite
            ]);
        }
    }

    if($produits->isEmpty()) {
        $produits = collect([]);
    }

    // Récupérer les informations complémentaires
   // Version améliorée
    $role = '';
    if ($employe->role === 'patissier') {
        $role = 'patisserie';
    } else if ($employe->role === 'boulanger') { 
        $role = 'boulangerie';
    }

$heure_actuelle = now();
    $all_produits = Produit_fixes::where('categorie', $role)->get();
    $info = User::where('id', $employe->id)->first();
    $nom = $info->name;
    $secteur = $info->secteur;
    
    // Récupérer les assignations pour le producteur aujourd'hui
    $assignments = Daily_assignments::where('producteur', $employe->id)->get();

    // Créer la collection des productions attendues
    $productions_attendues = collect();
    foreach ($assignments as $assignment) {
        $produit = Produit_fixes::where('code_produit', $assignment->produit)->first();
        if ($produit) {
            // Calculer la quantité déjà produite aujourd'hui pour ce produit spécifique
            $quantite_produite = Production::where('producteur', $employe->id)
                ->where('produit', $assignment->produit)
                ->whereDate('created_at', now()->toDateString())
                ->sum('quantite');

            if($quantite_produite == $assignment->expected_quantity){
                $assignment->status = 1;
            }    
            $s = $assignment->status;
            $status = ($s == 1) ? "Terminer" : "En attente";
            $productions_attendues->push([
                'nom' => $produit->nom,
                'quantite_attendue' => $assignment->expected_quantity,
                'quantite_produite' => $quantite_produite,
                'prix' => $produit->prix,
                'status' => $status,
                'progression' => ($quantite_produite / $assignment->expected_quantity) * 100
            ]);
        }
    }

    // Créer la collection des productions recommander en fonction de la journee
    // Collection des productions recommandées par jour
$jour_actuel = strtolower(now()->locale('fr')->dayName); // Obtient le jour actuel en français
$productions_recommandees = collect();

$suggestions = Production_suggerer_par_jour::where('day', $jour_actuel)
    ->get();

foreach ($suggestions as $suggestion) {
    $produit = Produit_fixes::where('code_produit', $suggestion->produit)->first();
    if ($produit) {
        $productions_recommandees->push([
            'nom' => $produit->nom,
            'quantite_recommandee' => $suggestion->quantity,
            'prix' => $produit->prix
        ]);
    }
}
$day = $jour_actuel;
    

    return view('pages/producteur/producteur_produit', 
        compact('p', 'all_produits', 'heure_actuelle', 'nom', 'secteur', 'productions_attendues','productions_recommandees','day')
    );
}

    public function production_suggerer() {

    }

    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'name' => 'required|string',
            'prix' => 'required|numeric',
            'qte' => 'required|numeric|min:1'
        ]);
    
        // Vérification de l'authentification
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')
                ->with('error', 'Veuillez vous connecter');
        }
    
        // Récupération du produit fixe
        $produit_fixe = Produit_fixes::where([
            ['nom', '=', $request->name],
            ['prix', '=', $request->prix]
        ])->first();
    
        if (!$produit_fixe) {
            return back()
                ->with('error', 'Produit non trouvé');
        }
    
        // Création de la production
        $production = Production::create([
            'producteur' => $employe->id,
            'produit' => $produit_fixe->code_produit,
            'quantite' => $request->qte,
            'created_at' => now()
        ]);
        //gerer le changement de status dans store
        return redirect()->route('producteur_produit')
            ->with('success', 'Production enregistrée avec succès');
    }


    public function fiche_production() {
        $employe = auth()->user();
    
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
    
        // Obtenir le premier et dernier jour du mois courant
        $debut_mois = now()->startOfMonth();
        $fin_mois = now()->endOfMonth();
    
        // Récupérer toutes les productions du mois pour tous les produits
        $productions_mois = Production::where('producteur', $employe->id)
            ->whereBetween('created_at', [$debut_mois, $fin_mois])
            ->get()
            ->groupBy('produit');
    
        // Collection pour stocker les statistiques de production
        $statistiques_production = collect();
    
        foreach ($productions_mois as $code_produit => $productions) {
            // Obtenir les informations du produit
            $produit = Produit_fixes::where('code_produit', $code_produit)->first();
            
            if ($produit) {
                // Calculer les statistiques pour ce produit
                $quantite_totale = $productions->sum('quantite');
                $valeur_totale = $quantite_totale * $produit->prix;
                
                // Grouper par jour pour voir l'évolution
                $productions_par_jour = $productions
                    ->groupBy(function($production) {
                        return $production->created_at->format('Y-m-d');
                    })
                    ->map(function($groupe) {
                        return $groupe->sum('quantite');
                    });
    
                // Calculer la moyenne journalière
                $moyenne_journaliere = $quantite_totale / $productions_par_jour->count();
    
                // Trouver le jour avec la production maximale
                $jour_max_production = $productions_par_jour->max();
                $date_max_production = $productions_par_jour
                    ->filter(function($quantite) use ($jour_max_production) {
                        return $quantite == $jour_max_production;
                    })
                    ->keys()
                    ->first();
    
                $statistiques_production->push([
                    'nom_produit' => $produit->nom,
                    'code_produit' => $code_produit,
                    'quantite_totale' => $quantite_totale,
                    'valeur_totale' => $valeur_totale,
                    'moyenne_journaliere' => round($moyenne_journaliere, 2),
                    'production_max' => [
                        'quantite' => $jour_max_production,
                        'date' => $date_max_production
                    ],
                    'productions_journalieres' => $productions_par_jour->toArray(),
                    'prix_unitaire' => $produit->prix
                ]);
            }
        }
    
        // Récupérer les informations de l'employé
        $info = User::where('id', $employe->id)->first();
        
        // Retour correct pour la vue avec un tableau de données
        return view('pages.producteur.producteur_fiche_production', [
            'statistiques' => $statistiques_production->toArray(),
            'mois_actuel' => now()->format('F Y'),
            'debut_mois' => $debut_mois->format('Y-m-d'),
            'fin_mois' => $fin_mois->format('Y-m-d'),
            'nom' => $info->name,
            'secteur' => $info->secteur,
            'num_tel' => $info->num_tel,
        ]);
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
    $commandes = Commande::where('categorie', $role)->get();    
    return view('pages/producteur/producteur_commande', compact('nom', 'secteur', 'commandes'));
}


    public function reserverMp() {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $info = User::where('id', $employe->id)->first();
        $nom = $info->name;
        $secteur = $info->secteur;
        return view('pages/producteur/producteur_reserverMp',compact('nom','secteur'));

    }

   
}