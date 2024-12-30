<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProduitRecu;
use App\Models\Produit_fixes;
use App\Models\Production;
use App\Models\User;
use App\Models\VersementCsg;
use App\Models\Transaction_vente;
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
        'nom'=>'required',
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
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
      return view('pages\serveur\ serveur-ajouterProduit_recu',compact('Employe','produitR','heure_actuelle'));
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
                            'total_ventes'=>$request->quantite * $request->prix ,
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
        $produitR=Produit_fixes::all();
        $heure_actuelle = now();
        $heure_actuelle->setTimezone('UTC');
        return view('pages\serveur\ serveur-enrProduit_vendu',compact('produitR','heure_actuelle'));
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
            $versement=User::all();
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
}