<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit_fixes;
use App\Models\Matiere;
use App\Models\MatiereRecommander;
use App\Enums\UniteMinimale;
use App\Enums\UniteClassique;
use App\Http\Requests\MatierePremRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Commande;
use App\Models\Utilisation;
use App\Models\User;
use App\Models\Daily_assignments;
use App\Models\AssignationsMatiere;
use Carbon\Carbon;
use App\Models\ACouper;

class Chef_productionController extends Controller
{

    public function gestionProduits()
    {
        $employe = Auth::user();
        $nom = $employe->name;
        $role = $employe->role;
        $produits = Produit_fixes::orderBy('created_at', 'desc')->paginate(10);
        return view('pages.chef_production.gestion_produits', compact('produits','nom','role'));
    }

    public function storeProduit(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:50',
                'prix' => 'required|numeric|min:0',
                'categorie' => 'required|string|in:boulangerie,patisserie'
            ]);

            DB::beginTransaction();

            $produit = Produit_fixes::create($validated);

            DB::commit();

            return redirect()->back()->with('success', 'Produit ajouté avec succès');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Erreur de validation: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'ajout du produit: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de l\'ajout du produit: ' . $e->getMessage()])
                ->withInput();
        }
    }
    public function updateProduit(Request $request, $code_produit)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:50',
                'prix' => 'required|numeric|min:0',
                'categorie' => 'required|string|in:boulangerie,patisserie'
            ]);

            DB::beginTransaction();

            $produit = Produit_fixes::where('code_produit', $code_produit)->firstOrFail();
            $produit->update($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Produit mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyProduit($code_produit)
    {
        try {
            DB::beginTransaction();

            $produit = Produit_fixes::where('code_produit', $code_produit)->firstOrFail();

            // Vérifier les relations
            if ($produit->utilisations()->exists() ||
                DB::table('Commande')->where('produit', $code_produit)->exists()) {
                throw new \Exception(
                    "Impossible de supprimer le produit « {$produit->nom} » car il est actuellement " .
                    "utilisé dans des commandes ou des productions en cours. " .
                    "Veuillez d'abord supprimer toutes les références à ce produit avant de le supprimer."
                );
            }

            $produit->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Produit supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }


    // Gestion des Matières Premières
    public function gestionMatieres()
    {
        $employe = Auth::user();
        $nom = $employe->name;
        $role = $employe->role;
        $matieres = Matiere::orderBy('created_at', 'desc')->paginate(10);
        $unites_minimales = UniteMinimale::values();
        $unites_classiques = UniteClassique::values();

        return view('pages.chef_production.gestion_matieres', compact('matieres', 'unites_minimales', 'unites_classiques','nom','role'));
    }

    public function storeMatiere(MatierePremRequest $request)
    {
        try {
            DB::beginTransaction();
            // Validation supplémentaire des unités compatibles
            $unites_permises = UniteMinimale::getUniteClassiquePermise($request->unite_minimale);
            if (!in_array($request->unite_classique, $unites_permises)) {
                return redirect()->back()->withErrors(['error' => 'Combinaison d\'unités invalide']);
            }
            Matiere::create($request->validated());
            DB::commit();
            return redirect()->back()->with('success', 'Matière première ajoutée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()]);
        }
    }

    public function editMatiere($id)
{
    try {
        $matiere = Matiere::findOrFail($id);
        return response()->json($matiere);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Matière première non trouvée'], 404);
    }
}

public function updateMatiere(MatierePremRequest $request, Matiere $matiere)
{
    try {
        DB::beginTransaction();

        // Validation supplémentaire des unités compatibles
        $unites_permises = UniteMinimale::getUniteClassiquePermise($request->unite_minimale);
        if (!in_array($request->unite_classique, $unites_permises)) {
            return redirect()->back()->withErrors(['error' => 'Combinaison d\'unités invalide']);
        }

        $matiere->update($request->validated());
        DB::commit();

        return redirect()->back()->with('success', 'Matière première mise à jour avec succès');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
    }
}

    public function destroyMatiere(Matiere $matiere)
    {
        try {
            DB::beginTransaction();
            $matiere->delete();
            DB::commit();

            return redirect()->back()->with('success', 'Matière première supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }
    public function dashboard() {
        return view('pages.chef_production.chef_production_dashboard');
    }
    public function createcommande()
    {
        $employe = Auth::user();
        $nom = $employe->name;
        $role = $employe->role;
        $produits = Produit_fixes::all();
        $commandes = Commande::all();
        return view('pages.chef_production.ajouter-commande', compact('produits', 'commandes', 'role', 'nom'));
    }

    public function storeCommande(Request $request)
    {
        try {
            $validated = $request->validate([
                'libelle' => 'required|string|max:50',
                'produit' => 'required|exists:produit_fixes,code_produit',
                'quantite' => 'required|integer|min:1',
                'date_commande' => 'required|date',
                'categorie' => 'required|in:patisserie,boulangerie'
            ]);

            \Log::info('Données validées :', $validated);  // Ajout de logging

            $commande = new Commande();
            $commande->libelle = $request->libelle;
            $commande->produit = $request->produit;
            $commande->quantite = $request->quantite;
            $commande->date_commande = $request->date_commande;
            $commande->categorie = $request->categorie;
            $commande->valider = false; // Définition explicite de la valeur par défaut
            $commande->save(); // Enregistrement dans la base de données


            \Log::info('Avant sauvegarde');  // Ajout de logging
            $commande->save();
            \Log::info('Après sauvegarde');  // Ajout de logging

            return redirect()->back()->with('success', 'Commande ajoutée avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la commande : ' . $e->getMessage());  // Ajout de logging
            return redirect()->back()
                ->withErrors([$e->getMessage()])
                ->withInput();
        }
    }

    public function editcommande($id)
    {
        $commande = Commande::findOrFail($id);
        $produits = Produit_fixes::all();
        return view('pages.chef_production.modifier-commande', compact('commande', 'produits'));
    }

    public function updatecommande(Request $request, $id)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:50',
            'produit' => 'required|exists:Produit_fixes,code_produit',
            'quantite' => 'required|integer|min:1',
            'date_commande' => 'required|date',
            'categorie' => 'required|string'
        ]);

        $commande = Commande::findOrFail($id);
        $commande->update($validated);

        return redirect()->route('chef.commandes.create')->with('success', 'Commande mise à jour avec succès');
    }

    public function destroycommande($id)
    {
        try {
            $commande = Commande::findOrFail($id);
            $commande->delete();
            return response()->json(['status' => 'success', 'message' => 'Commande supprimée avec succès']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }

    public function index()
    {
        $employe = Auth::user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $nom = $employe->name;
        $role = $employe->role;
        $today = Carbon::today();

        // Production aujourd'hui
        $productionJour = $this->getProductionJournaliere();

        // Bénéfice brut
        $beneficeBrut = $this->getBeneficeBrut();

        // Rendement
        $rendementData = $this->getRendement();

        // Pertes
        $pertes = $this->getPertes();

        // Gaspillage
        $gaspillage = $this->getGaspillageMatiere();

        // Données pour les graphiques
        $graphData = $this->getGraphData();

        // Productions en cours
        $productionsEnCours = $this->getProductionsEnCours();

        // Liste des produits pour le formulaire d'assignation
        $produits = Produit_fixes::all();
        $producteurs = User::where('role', 'boulanger')
        ->orWhere('role', 'patissier')
        ->get();


        return view('pages.chef_production.chef_production_dashboard', compact(
            'productionJour',
            'beneficeBrut',
            'rendementData',
            'pertes',
            'gaspillage',
            'graphData',
            'productionsEnCours',
            'produits',
            'producteurs',
            'nom',
            'role'
        ));
    }

    private function getProductionJournaliere()
    {
        return Utilisation::whereDate('created_at', Carbon::today())
    ->groupBy('id_lot')
    ->select(DB::raw('SUM(quantite_produit) as total_production'))
    ->get()
    ->sum('total_production');

    }

private function getBeneficeBrut()
{
    try {
        $beneficeBrut = DB::table('Utilisation as u')
            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')
            ->select(
                'u.id_lot',
                DB::raw('SUM(u.quantite_produit * p.prix) as benefice_brut_par_lot')
            )
            ->whereDate('u.created_at', Carbon::today())
            ->groupBy('u.id_lot') // Regrouper par lot
            ->pluck('benefice_brut_par_lot') // Récupérer les bénéfices pour chaque lot
            ->sum(); // Faire la somme pour obtenir le total

        return $beneficeBrut ?? 0; // Retourner 0 si aucun résultat trouvé
    } catch (\Exception $e) {
        // Enregistrer l'erreur dans les logs
        \Log::error('Erreur lors du calcul du bénéfice brut : ' . $e->getMessage());
        return 0; // Retourner 0 en cas d'erreur
    }
}


    private function getRendement()
    {
        $beneficeReel = $this->getBeneficeBrut();

        $beneficeAttendu = DB::table('Daily_assignments as da')
            ->join('Produit_fixes as p', 'da.produit', '=', 'p.code_produit')
            ->whereDate('assignment_date', Carbon::today())
            ->select(DB::raw('SUM(da.expected_quantity * p.prix) as benefice_attendu'))
            ->value('benefice_attendu') ?? 0;

        return [
            'pourcentage' => $beneficeAttendu > 0 ? ($beneficeReel / $beneficeAttendu) * 100 : 0,
            'reel' => $beneficeReel,
            'attendu' => $beneficeAttendu
        ];
    }

    private function getPertes()
    {
            $coutMatieresUtilisees = DB::table('Utilisation as u')
                ->join('Matiere as m', 'u.matierep', '=', 'm.id')
                ->whereDate('u.created_at', Carbon::today())
                ->select(DB::raw('SUM(u.quantite_matiere * m.prix_par_unite_minimale) as cout_total'))
                ->value('cout_total') ?? 0;

            return $coutMatieresUtilisees;
        }


        private function getGaspillageMatiere()
        {
            $today = Carbon::today();

            // Récupérer les utilisations groupées par produit, matière première et heure
            $utilisations = DB::table('Utilisation as u')
                ->join('Matiere as m', 'u.matierep', '=', 'm.id')
                ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')
                ->select(
                    DB::raw('HOUR(u.created_at) as heure'),
                    'u.produit',
                    'u.matierep',
                    DB::raw('SUM(u.quantite_matiere) as total_utilisee')
                )
                ->whereDate('u.created_at', $today)
                ->groupBy(DB::raw('HOUR(u.created_at)'), 'u.produit', 'u.matierep')
                ->get();

            $gaspillageParHeure = [];

            foreach ($utilisations as $utilisation) {
                $heure = $utilisation->heure;
                $produitId = $utilisation->produit;
                $matiereId = $utilisation->matierep;
                $quantiteUtilisee = $utilisation->total_utilisee;

                $recommandation = DB::table('Matiere_recommander')
                    ->where('produit', $produitId)
                    ->where('matierep', $matiereId)
                    ->first();

                if ($recommandation) {
                    $quantiteRecommandee = $recommandation->quantite;

                    if ($quantiteUtilisee > $quantiteRecommandee) {
                        $gaspillage = ($quantiteUtilisee - $quantiteRecommandee) / $quantiteRecommandee * 100;

                        // Ajouter au gaspillage pour l'heure
                        if (!isset($gaspillageParHeure[$heure])) {
                            $gaspillageParHeure[$heure] = 0;
                        }
                        $gaspillageParHeure[$heure] += $gaspillage;
                    }
                }
            }

            // Assurez-vous de retourner un tableau avec les 24 heures de la journée
            $gaspillageParHeureComplet = [];
            for ($i = 0; $i < 24; $i++) {
                $gaspillageParHeureComplet[$i] = $gaspillageParHeure[$i] ?? 0;
            }

            return $gaspillageParHeureComplet;
        }




        public function getGraphData()
        {
            $today = Carbon::today();

            // Productions par heure
            $productions = DB::table('Utilisation')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%H:00:00") as timestamp'),
                    DB::raw('id_lot'),
                    DB::raw('SUM(quantite_produit) as total')
                )
                ->whereDate('created_at', $today)
                ->groupBy('id_lot', DB::raw('DATE_FORMAT(created_at, "%H:00:00")'))
                ->orderBy('timestamp')
                ->get()
                ->groupBy('timestamp')
                ->map(function ($group) {
                    return [
                        'timestamp' => $group->first()->timestamp,
                        'total' => $group->sum('total')
                    ];
                })
                ->values();

            // Pertes/Gaspillage par heure
            $pertes = DB::table('Utilisation as u')
                ->join('Matiere_recommander as mr', function($join) {
                    $join->on('u.produit', '=', 'mr.produit')
                         ->on('u.matierep', '=', 'mr.matierep');
                })
                ->select(
                    DB::raw('DATE_FORMAT(u.created_at, "%H:00:00") as timestamp'),
                    DB::raw('u.id_lot'),
                    DB::raw('SUM(CASE
                        WHEN u.quantite_matiere > mr.quantite
                        THEN ((u.quantite_matiere - mr.quantite) / mr.quantite * 100)
                        ELSE 0
                    END) as perte')
                )
                ->whereDate('u.created_at', $today)
                ->groupBy('id_lot', DB::raw('DATE_FORMAT(u.created_at, "%H:00:00")'))
                ->orderBy('timestamp')
                ->get()
                ->groupBy('timestamp')
                ->map(function ($group) {
                    return [
                        'timestamp' => $group->first()->timestamp,
                        'perte' => $group->avg('perte')
                    ];
                })
                ->values();

            // Bénéfices par heure
            $benefices = DB::table('Utilisation as u')
                ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')
                ->join('Matiere as m', 'u.matierep', '=', 'm.id')
                ->select(
                    DB::raw('DATE_FORMAT(u.created_at, "%H:00:00") as timestamp'),
                    DB::raw('u.id_lot'),
                    DB::raw('SUM(u.quantite_produit * p.prix) - SUM(u.quantite_matiere * m.prix_par_unite_minimale) as benefice')
                )
                ->whereDate('u.created_at', $today)
                ->groupBy('id_lot', DB::raw('DATE_FORMAT(u.created_at, "%H:00:00")'))
                ->orderBy('timestamp')
                ->get()
                ->groupBy('timestamp')
                ->map(function ($group) {
                    return [
                        'timestamp' => $group->first()->timestamp,
                        'benefice' => $group->sum('benefice')
                    ];
                })
                ->values();

            // Calcul du gaspillage moyen
            $gaspillageTotal = $pertes->avg('perte') ?? 0;

            // Assurer que toutes les heures sont représentées (de 00:00 à 23:00)
            $heuresCompletes = collect(range(0, 23))->map(function ($heure) {
                return str_pad($heure, 2, '0', STR_PAD_LEFT) . ':00:00';
            });

            $productions = $this->completerHeures($productions, $heuresCompletes);
            $pertes = $this->completerHeures($pertes, $heuresCompletes);
            $benefices = $this->completerHeures($benefices, $heuresCompletes);

            return [
                'productions' => $productions,
                'pertes' => $pertes,
                'benefices' => $benefices,
                'gaspillage' => round($gaspillageTotal, 2)
            ];
        }

        private function completerHeures($donnees, $heuresCompletes)
        {
            $donneesParHeure = $donnees->pluck('timestamp')->flip();

            return $heuresCompletes->map(function ($heure) use ($donnees, $donneesParHeure) {
                if (isset($donneesParHeure[$heure])) {
                    return $donnees[$donneesParHeure[$heure]];
                }

                return [
                    'timestamp' => $heure,
                    'total' => 0,
                    'perte' => 0,
                    'benefice' => 0
                ];
            })->values();
        }



    private function getProductionsEnCours()
    {
        $assignments = Daily_assignments::with(['produitFixe'])
            ->whereDate('assignment_date', Carbon::today())
            ->get();

        if ($assignments->isEmpty()) {
            return Utilisation::with(['produitFixe'])
                ->whereDate('created_at', Carbon::today())
                ->select('produit', DB::raw('SUM(quantite_produit) as total_produit'))
                ->groupBy('produit','id_lot')
                ->get();
        }

        return $assignments->map(function ($assignment) {
            $productionActuelle = Utilisation::where([
                'produit' => $assignment->produit,
                'producteur' => $assignment->producteur
            ])
            ->whereDate('created_at', Carbon::today())
            ->sum('quantite_produit');

            return [
                'produit' => $assignment->produitFixe->nom,
                'quantite_actuelle' => $productionActuelle,
                'quantite_attendue' => $assignment->expected_quantity,
                'progression' => ($productionActuelle / $assignment->expected_quantity) * 100,
                'status' => $assignment->status
            ];
        });
    }

    public function assignerProduction(Request $request)
    {
        $validated = $request->validate([
            'producteur' => 'required|exists:users,id',
            'produit' => 'required|exists:Produit_fixes,code_produit',
            'quantite' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $assignment = new Daily_assignments();
        $assignment->chef_production = auth()->id();
        $assignment->producteur = $validated['producteur'];
        $assignment->produit = $validated['produit'];
        $assignment->expected_quantity = $validated['quantite'];
        $assignment->assignment_date = Carbon::today();
        $assignment->status = 0;
        $assignment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Production assignée avec succès'
        ]);
    }

    public function createmanquant()
    {
        $employe = Auth::user();
        $nom = $employe->name;
        $role = $employe->role;
        $employees = User::where('role', 'patissier')
        ->orWhere('role', 'boulanger')
        ->get();
return view('pages.manquant', compact('employees','nom','role'));
    }

    public function storemanquant(Request $request)
    {
        $request->validate([
            'id_employe' => 'required|exists:users,id',
            'manquants' => 'required|numeric|min:0',
        ]);

        Acouper::create([
            'id_employe' => $request->id_employe,
            'manquants' => $request->manquants,
            'date' => now(),
        ]);

        return redirect()->route('manquant.create')->with('success', 'Manquant attribué avec succès.');
    }
}
