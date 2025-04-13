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
use App\Models\Ingredient;
use App\Models\Daily_assignments;
use App\Models\AssignationsMatiere;
use \App\Models\ProduitStock;
use Carbon\Carbon;
use App\Models\ACouper;
use App\Http\Controllers\NotificationController;
use App\Traits\HistorisableActions;

class Chef_productionController extends Controller
{


    use HistorisableActions;

    public function __construct(NotificationController $notificationController)
	{
    		$this->notificationController = $notificationController;
	}
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

        // Créer l'entrée correspondante dans produit_stocks avec des quantités à 0
        DB::table('produit_stocks')->insert([
            'id_produit' => $produit->code_produit,
            'quantite_en_stock' => 0,
            'quantite_invendu' => 0,
            'quantite_avarie' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $user = auth()->user();
        $this->historiser("L'utilisateur {$user->name} a créé le produit {$validated['nom']}", 'create_produit');
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
        $matieres = Matiere::where('nom', 'NOT LIKE', 'Taule%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unites_minimales = UniteMinimale::values();
        $unites_classiques = UniteClassique::values();

        return view('pages.chef_production.gestion_matieres', compact('matieres', 'unites_minimales', 'unites_classiques','nom','role'));
    }

    public function storeMatiere(MatierePremRequest $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:50',
                'quantite_par_unite' => 'required|numeric|min:0',
                'quantite' => 'required|numeric|min:0',
                'prix_unitaire' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Vérifier si le nom existe déjà
            if (Matiere::where('nom', $request->nom)->exists()) {
                return redirect()->back()->withErrors(['error' => 'Une matière avec ce nom existe déjà']);
            }

            // Validation supplémentaire des unités compatibles
            $unites_permises = UniteMinimale::getUniteClassiquePermise($request->unite_minimale);
            if (!in_array($request->unite_classique, $unites_permises)) {
                return redirect()->back()->withErrors(['error' => 'Combinaison d\'unités invalide']);
            }

            // Créer la matière
            $matiere = Matiere::create($request->validated());

            // Ajouter comme ingrédient dans la table ingredients
            Ingredient::create([
                'name' => $matiere->nom,
                'unit' => $request->unite_classique
            ]);

            // Historiser
            $user = auth()->user();
            $date = Carbon::now();
            $this->historiser("La matière première '{$matiere->nom}' a été créée par {$user->name}", 'create_matiere');

            DB::commit();
            return redirect()->back()->with('success', 'Matière première ajoutée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()]);
        }
    }

    public function updateMatiere(MatierePremRequest $request, Matiere $matiere)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:50',
                'prix' => 'required|numeric|min:0',
                'quantite_par_unite' => 'required|numeric|min:0',
                'quantite' => 'required|numeric|min:0',
                'prix_unitaire' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Vérifier si le nom existe déjà (sauf pour cette matière)
            if (Matiere::where('nom', $request->nom)->where('id', '!=', $matiere->id)->exists()) {
                return redirect()->back()->withErrors(['error' => 'Une matière avec ce nom existe déjà']);
            }

            // Validation supplémentaire des unités compatibles
            $unites_permises = UniteMinimale::getUniteClassiquePermise($request->unite_minimale);
            if (!in_array($request->unite_classique, $unites_permises)) {
                return redirect()->back()->withErrors(['error' => 'Combinaison d\'unités invalide']);
            }

            // Sauvegarder l'ancien nom pour la recherche dans la table ingredients
            $oldName = $matiere->nom;

            // Mettre à jour la matière
            $matiere->update($request->validated());

            // Mettre à jour l'ingrédient correspondant
            $ingredient = Ingredient::where('name', $oldName)->first();
            if ($ingredient) {
                $ingredient->name = $matiere->nom;
                $ingredient->unit = $request->unite_classique;
                $ingredient->save();
            } else {
                // Si l'ingrédient n'existe pas, le créer
                Ingredient::create([
                    'name' => $matiere->nom,
                    'unit' => $request->unite_classique
                ]);
            }

            // Historiser
            $user = auth()->user();
            $date = Carbon::now();
            $this->historiser("La matière première '{$matiere->nom}' a été mise à jour par {$user->name}", 'update_matiere');

            DB::commit();
            return redirect()->back()->with('success', 'Matière première mise à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
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
            // Validate request data
            $validated = $request->validate([
                'libelle' => 'required|string|max:50',
                'produit' => 'required|exists:Produit_fixes,code_produit',
                'quantite' => 'required|integer|min:1',
                'date_commande' => 'required|date',
                'categorie' => 'required|in:patissier,boulanger'
            ]);

            \Log::info('Données validées', ['data' => $validated]);

            // Check if there is enough stock
            $stock = ProduitStock::where('id_produit', $request->produit)->first();

            // Create new command
            $commande = new Commande();
            $commande->libelle = $request->libelle;
            $commande->produit = $request->produit;
            $commande->quantite = $request->quantite;
            $commande->date_commande = $request->date_commande;
            $commande->categorie = $request->categorie;
            $commande->valider = false;

            \Log::info('Avant sauvegarde');
            $commande->save();
            \Log::info('Après sauvegarde');

            // Historize the action
            $produit = Produit_fixes::find($request->produit);
            if (!$produit) {
                throw new \Exception('Produit non trouvé');
            }
            $user = auth()->user();
            $this->historiser("Une nouvelle commande de {$commande->libelle} : {$commande->quantite} {$produit->nom} a été créée par {$user->name}", 'create_commande');

            // Notify pointeurs (users with 'pointeur' role)
            $pointeurs = User::where('role', 'pointeur')->get();
            foreach ($pointeurs as $pointeur) {
                $request->merge([
                    'recipient_id' => $pointeur->id,
                    'subject' => 'Nouvelle commande à valider',
                    'message' => "Une nouvelle commande de {$commande->libelle} : {$commande->quantite} {$produit->nom} a été créée et nécessite votre validation."
                ]);

                $this->notificationController->send($request);
            }

            $producteurs = User::where('role', $request->categorie)->get();
            foreach ($producteurs as $p) {
                $request->merge([
                    'recipient_id' => $p->id,
                    'subject' => 'Nouvelle commande à valider',
                    'message' => "Une nouvelle commande de {$commande->libelle} : {$commande->quantite} {$produit->nom} a été defini."
                ]);

                $this->notificationController->send($request);
            }

            return redirect()->back()->with('success', 'Commande ajoutée avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la commande : ' . $e->getMessage());
            return redirect()->back()
                ->withErrors([$e->getMessage()])
                ->withInput();
        }
    }

    public function validateCommande($id)
    {
        try {
            $commande = Commande::findOrFail($id);

            // Check if command is already validated
            if ($commande->valider) {
                return redirect()->back()->with('error', 'Cette commande a déjà été validée');
            }

            // Get product stock
            $stock = ProduitStock::where('id_produit', $commande->produit)->first();

            if (!$stock || $stock->quantite_en_stock < $commande->quantite) {
                return redirect()->back()->with('error', 'Stock insuffisant pour valider cette commande');
            }

            // Update stock
            $stock->quantite_en_stock -= $commande->quantite;
            $stock->save();

            // Update command status
            $commande->valider = true;
            $commande->save();

            // Historize the action
            $user = auth()->user();
            $this->historiser("La commande de {$commande->quantite} {$commande->libelle} a été validée par {$user->name}", 'update');

            // Notify chef_production
            $chefProduction = \App\Models\User::where('role', 'chef_production')->get();

            foreach ($chefProduction as $chef) {
                $request->merge([
                    'recipient_id' => $chef->id,
                    'subject' => 'Commande validée et livrée',
                    'message' => "La commande #{$commande->id} de {$commande->quantite} {$commande->libelle} a été validée et livrée. Le stock a été mis à jour en conséquence."
                ]);

                $this->notificationController->send($request);
            }

            return redirect()->back()->with('success', 'Commande validée avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la validation de la commande : ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
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
        $utilisations = DB::table('Utilisation')
            ->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
            ->select(
                'Utilisation.id_lot',
                'Utilisation.quantite_produit'
            )
            ->whereDate('Utilisation.created_at', Carbon::today())
            ->get();

        $productionsParLot = [];
        $totalProduction = 0;

        foreach ($utilisations as $utilisation) {
            $idLot = $utilisation->id_lot;

            // Si ce lot n'a pas encore été traité, on ajoute sa production au total
            if (!isset($productionsParLot[$idLot])) {
                $productionsParLot[$idLot] = true;
                $totalProduction += $utilisation->quantite_produit;
            }
        }

        return $totalProduction;
    }

    // Méthode mise à jour pour calculer correctement le bénéfice brut par lot
    private function getBeneficeBrut()
    {
        try {
            $utilisations = DB::table('Utilisation')
                ->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
                ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
                ->select(
                    'Utilisation.id_lot',
                    'Produit_fixes.prix as prix_produit',
                    'Utilisation.quantite_produit',
                    'Matiere.prix_par_unite_minimale',
                    'Utilisation.quantite_matiere'
                )
                ->whereDate('Utilisation.created_at', Carbon::today())
                ->get();

            $productionsParLot = [];
            $beneficeBrutTotal = 0;

            foreach ($utilisations as $utilisation) {
                $idLot = $utilisation->id_lot;

                if (!isset($productionsParLot[$idLot])) {
                    $productionsParLot[$idLot] = [
                        'quantite_produit' => $utilisation->quantite_produit,
                        'prix_unitaire' => $utilisation->prix_produit,
                        'valeur_production' => $utilisation->quantite_produit * $utilisation->prix_produit,
                        'cout_matieres' => 0
                    ];
                }

                // Accumule le coût des matières pour ce lot
                $productionsParLot[$idLot]['cout_matieres'] +=
                    $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale;
            }

            // Calcul du bénéfice brut total (valeur production - coût matières) pour tous les lots
            foreach ($productionsParLot as $production) {
                $beneficeBrutTotal += ($production['valeur_production'] - $production['cout_matieres']);
            }

            return $beneficeBrutTotal;
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

        // Productions par heure - Regroupement par lot pour éviter les doublons
        $productions = DB::table('Utilisation')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%H:00:00") as timestamp'),
                'id_lot',
                'quantite_produit'
            )
            ->whereDate('created_at', $today)
            ->get()
            ->groupBy(function($item) {
                // Regrouper par heure et par lot
                return $item->timestamp . '-' . $item->id_lot;
            })
            ->map(function($lotGroup) {
                // Ne prendre que la première entrée pour chaque lot (par heure)
                $item = $lotGroup->first();
                return [
                    'timestamp' => $item->timestamp,
                    'id_lot' => $item->id_lot,
                    'total' => $item->quantite_produit
                ];
            })
            ->values()
            ->groupBy('timestamp')
            ->map(function($group) {
                return [
                    'timestamp' => $group->first()['timestamp'],
                    'total' => $group->sum('total')
                ];
            })
            ->values();

        // Pertes/Gaspillage par heure - Avec prise en compte correcte des lots
        $pertes = DB::table('Utilisation as u')
            ->join('Matiere_recommander as mr', function($join) {
                $join->on('u.produit', '=', 'mr.produit')
                     ->on('u.matierep', '=', 'mr.matierep');
            })
            ->select(
                DB::raw('DATE_FORMAT(u.created_at, "%H:00:00") as timestamp'),
                'u.id_lot',
                DB::raw('CASE
                    WHEN u.quantite_matiere > (mr.quantite * (u.quantite_produit / mr.quantitep))
                    THEN ((u.quantite_matiere - (mr.quantite * (u.quantite_produit / mr.quantitep))) / (mr.quantite * (u.quantite_produit / mr.quantitep)) * 100)
                    ELSE 0
                END as perte')
            )
            ->whereDate('u.created_at', $today)
            ->get()
            ->groupBy(function($item) {
                // Regrouper par heure et par lot
                return $item->timestamp . '-' . $item->id_lot;
            })
            ->map(function($lotGroup) {
                // Ne prendre que la première entrée pour chaque lot (par heure)
                return $lotGroup->first();
            })
            ->values()
            ->groupBy('timestamp')
            ->map(function($group) {
                return [
                    'timestamp' => $group->first()->timestamp,
                    'perte' => $group->avg('perte')
                ];
            })
            ->values();

        // Bénéfices par heure - Avec prise en compte correcte des lots
        $benefices = DB::table('Utilisation as u')
            ->join('Produit_fixes as p', 'u.produit', '=', 'p.code_produit')
            ->join('Matiere as m', 'u.matierep', '=', 'm.id')
            ->select(
                DB::raw('DATE_FORMAT(u.created_at, "%H:00:00") as timestamp'),
                'u.id_lot',
                'u.quantite_produit',
                'p.prix as prix_produit',
                'u.quantite_matiere',
                'm.prix_par_unite_minimale'
            )
            ->whereDate('u.created_at', $today)
            ->get()
            ->groupBy(function($item) {
                // Regrouper par heure et par lot
                return $item->timestamp . '-' . $item->id_lot;
            })
            ->map(function($lotGroup) {
                // Pour chaque lot, calculer le bénéfice
                $lot = $lotGroup->first();
                $valeurProduction = $lot->quantite_produit * $lot->prix_produit;
                $coutMatieres = 0;

                // Calculer le coût total des matières pour ce lot
                foreach ($lotGroup as $item) {
                    $coutMatieres += $item->quantite_matiere * $item->prix_par_unite_minimale;
                }

                return [
                    'timestamp' => $lot->timestamp,
                    'benefice' => $valeurProduction - $coutMatieres
                ];
            })
            ->values()
            ->groupBy('timestamp')
            ->map(function($group) {
                return [
                    'timestamp' => $group->first()['timestamp'],
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
        //verifier si la production est deja assignée pour le meme produit
        $existingAssignment = Daily_assignments::where('producteur', $validated['producteur'])
            ->where('produit', $validated['produit'])
            ->whereDate('assignment_date', Carbon::today())
            ->first();
        //si la production a deja ete assigner , alors , ajouter la quantite
        if ($existingAssignment) {
            $existingAssignment->expected_quantity += $validated['quantite'];
            $existingAssignment->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Production mise à jour avec succès'
            ]);
        }
        $assignment = new Daily_assignments();
        $assignment->chef_production = auth()->id();
        $assignment->producteur = $validated['producteur'];
        $assignment->produit = $validated['produit'];
        $assignment->expected_quantity = $validated['quantite'];
        $assignment->assignment_date = Carbon::today();
        $assignment->status = 0;
        $assignment->save();
        //notifier le producteur
        $producteur = User::find($validated['producteur']);
        $request->merge([
            'recipient_id' => $producteur->id,
            'subject' => 'Nouvelle production assignée',
            'message' => "Vous avez été assigné à produire {$validated['quantite']} unités de {$assignment->produitFixe->nom}."
        ]);
        $this->notificationController->send($request);
        //historiser
        $user = auth()->user();
        $this->historiser("La production de {$assignment->produitFixe->nom} a été assignée à {$producteur->name} par {$user->name}", 'assigner_production');
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
        $employees = User::where('secteur', 'production')
        ->orWhere('secteur', 'alimentation')
        ->get();
return view('pages.manquant', compact('employees','nom','role'));
    }

    public function storemanquant(Request $request)
    {

    }


public function versementsEnAttente()
{
    $employe = Auth::user();
    $nom = $employe->name;
    $role = $employe->role;

    $versements = DB::table('Versement_csg as v')
        ->join('users as verseur', 'v.verseur', '=', 'verseur.id')
        ->join('users as encaisseur', 'v.encaisseur', '=', 'encaisseur.id')
        ->where('v.status', 'en_attente')
        ->select('v.*',
                'verseur.name as nom_verseur',
                'encaisseur.name as nom_encaisseur')
        ->orderBy('v.date', 'desc')
        ->get();

    return view('pages.chef_production.versements_validation', compact('versements', 'nom', 'role'));
}

public function validerVersement(Request $request, $code_vcsg)
{
    try {
        DB::beginTransaction();

        // Récupération du versement
        $versement = DB::table('Versement_csg')->where('code_vcsg', $code_vcsg)->first();
        if (!$versement) {
            throw new \Exception("Versement non trouvé");
        }

        // Mise à jour du status du versement
        DB::table('Versement_csg')
            ->where('code_vcsg', $code_vcsg)
            ->update([
                'status' => $request->action === 'valider' ? 'valide' : 'rejete',
                'commentaire' => $request->commentaire
            ]);

        // Si le versement est validé, mettre à jour le solde du CP
        if ($request->action === 'valider') {
            // Récupération du solde actuel
            $soldeCP = DB::table('solde_cp')->first();

            if (!$soldeCP) {
                throw new \Exception("Solde CP non trouvé");
            }

            $soldeAvant = $soldeCP->montant;
            $soldeApres = $soldeAvant + $versement->somme;

            // Mise à jour du solde
            DB::table('solde_cp')
                ->where('id', $soldeCP->id)
                ->update([
                    'montant' => $soldeApres,
                    'derniere_mise_a_jour' => now(),
                    'updated_at' => now()
                ]);

            // Ajout dans l'historique
            DB::table('historique_solde_cp')->insert([
                'montant' => $versement->somme,
                'type_operation' => 'versement',
                'operation_id' => $code_vcsg,
                'solde_avant' => $soldeAvant,
                'solde_apres' => $soldeApres,
                'user_id' => auth()->id(), // Utilisateur qui effectue la validation
                'description' => "Validation du versement #$code_vcsg : {$versement->libelle}",
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        DB::commit();

        $message = $request->action === 'valider' ?
            'Versement validé avec succès et solde CP mis à jour' :
            'Versement rejeté avec succès';

        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors du traitement: ' . $e->getMessage()
        ], 500);
    }
}

public function choix_classement(){
    return view('pages.choix-classement');
}

}
