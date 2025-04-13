<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TauleInutilisee;
use App\Models\TypeTaule;
use App\Models\Matiere;
use App\Models\AssignationMatiere;
use App\Services\UniteConversionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TauleInutiliseeController extends Controller
{
    protected $uniteConversionService;

    public function __construct(UniteConversionService $uniteConversionService)
    {
        $this->uniteConversionService = $uniteConversionService;
    }

    public function index()
    {
        $taulesDuProducteur = TauleInutilisee::where('producteur_id', Auth::id())
            ->where('recuperee', false)
            ->with('typeTaule')
            ->get();

        $taulesDisponibles = TauleInutilisee::where('recuperee', false)
            ->where('producteur_id', '!=', Auth::id())
            ->with(['typeTaule', 'producteur'])
            ->get();

        $taulesRecuperees = TauleInutilisee::where('recuperee_par', Auth::id())
            ->with(['typeTaule', 'producteur', 'matiereCreee'])
            ->get();

        return view('taules.inutilisees.index', compact('taulesDuProducteur', 'taulesDisponibles', 'taulesRecuperees'));
    }

    public function create()
    {
        $typesTaules = TypeTaule::all();
        return view('taules.inutilisees.create', compact('typesTaules'));
    }

    public function recuperer(TauleInutilisee $tauleInutilisee)
    {
        if ($tauleInutilisee->recuperee) {
            return redirect()->back()
                ->with('error', 'Ces taules ont déjà été récupérées.');
        }

        if ($tauleInutilisee->producteur_id == Auth::id()) {
            return redirect()->back()
                ->with('error', 'Vous ne pouvez pas récupérer vos propres taules.');
        }

        DB::beginTransaction();

        try {
            // Mettre à jour le statut des taules
            $tauleInutilisee->update([
                'recuperee' => true,
                'recuperee_par' => Auth::id(),
                'date_recuperation' => now(),
            ]);

            // Assigner la matière au récupérateur
            AssignationMatiere::create([
                'producteur_id' => Auth::id(),
                'matiere_id' => $tauleInutilisee->matiere_creee_id,
                'quantite_assignee' => 1,
                'unite_assignee' => 'unite',
                'quantite_restante' => 1,
                'date_limite_utilisation' => now()->addDays(1), // 24h pour utiliser les taules
            ]);

            DB::commit();

            return redirect()->route('taules.inutilisees.index')
                ->with('success', 'Taules récupérées avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la récupération : ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type_taule_id' => 'required|exists:type_taules,id',
            'nombre_taules' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $typeTaule = TypeTaule::findOrFail($request->type_taule_id);

        // Calculer les matières premières correspondantes
        $nombreTaules = $request->nombre_taules;
        $quantiteFarine = $this->calculerQuantite($typeTaule->formule_farine, $nombreTaules);
        $quantiteEau = $this->calculerQuantite($typeTaule->formule_eau, $nombreTaules);
        $quantiteHuile = $this->calculerQuantite($typeTaule->formule_huile, $nombreTaules);
        $quantiteAutres = $this->calculerQuantite($typeTaule->formule_autres, $nombreTaules);

        // Créer une matière "Taules inutilisées"
        $prixTotal = $this->calculerPrixTotal($quantiteFarine, $quantiteEau, $quantiteHuile, $quantiteAutres);

        DB::beginTransaction();

        try {
            $matiere = Matiere::create([
                'nom' => 'Taules inutilisées - ' . $typeTaule->nom . ' - ' . now()->format('d/m/Y H:i'),
                'unite_minimale' => 'unite',
                'unite_classique' => 'unite',
                'quantite_par_unite' => 1,
                'quantite' => 1, // Une seule unité représentant l'ensemble des taules
                'prix_unitaire' => $prixTotal,
                'prix_par_unite_minimale' => $prixTotal,
            ]);

            $tauleInutilisee = TauleInutilisee::create([
                'producteur_id' => Auth::id(),
                'type_taule_id' => $request->type_taule_id,
                'nombre_taules' => $nombreTaules,
                'matiere_creee_id' => $matiere->id,
                'recuperee' => false,
            ]);

            DB::commit();

            return redirect()->route('taules.inutilisees.index')
                ->with('success', 'Taules inutilisées enregistrées avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage())
                ->withInput();
        }
    }

    private function calculerQuantite($formule, $nombreTaules)
    {
        if (empty($formule)) {
            return 0;
        }

        // Évaluer la formule mathématique avec le nombre de taules
        $formule = str_replace('n', $nombreTaules, $formule);

        // Sécuriser l'évaluation de la formule
        $sanitizedFormula = preg_replace('/[^0-9\+\-\*\/\.\(\)n]/', '', $formule);

        // Utiliser une méthode d'évaluation sécurisée pour calculer le résultat
        return eval("return $sanitizedFormula;");
    }

    private function calculerPrixTotal($quantiteFarine, $quantiteEau, $quantiteHuile, $quantiteAutres = 0)
    {
        // Récupérer les prix des matières premières
        $prixFarine = $this->getPrixMatiere('Farine', $quantiteFarine, 'kg');
        $prixEau = $this->getPrixMatiere('Eau', $quantiteEau, 'l');
        $prixHuile = $this->getPrixMatiere('Huile', $quantiteHuile, 'l');
        $prixAutres = $this->getPrixMatiere('Autres', $quantiteAutres, 'kg');

        return $prixFarine + $prixEau + $prixHuile + $prixAutres;
    }

    private function getPrixMatiere($nom, $quantite, $unite)
    {
        if ($quantite <= 0) {
            return 0;
        }

        $matiere = Matiere::where('nom', 'like', "%$nom%")->first();

        if (!$matiere) {
            // Prix par défaut si la matière n'est pas trouvée
            switch ($nom) {
                case 'Farine':
                    // Convertir kg en g avant de multiplier
                    $quantiteMinimale = $this->uniteConversionService->convertir($quantite, $unite, 'g');
                    return $quantiteMinimale * 0.5; // 500 FCFA par kg = 0.5 FCFA par g
                case 'Eau':
                    // Convertir l en ml avant de multiplier
                    $quantiteMinimale = $this->uniteConversionService->convertir($quantite, $unite, 'ml');
                    return $quantiteMinimale * 0.05; // 50 FCFA par l = 0.05 FCFA par ml
                case 'Huile':
                    // Convertir l en ml avant de multiplier
                    $quantiteMinimale = $this->uniteConversionService->convertir($quantite, $unite, 'ml');
                    return $quantiteMinimale * 1.2; // 1200 FCFA par l = 1.2 FCFA par ml
                case 'Autres':
                    // Convertir kg en g avant de multiplier
                    $quantiteMinimale = $this->uniteConversionService->convertir($quantite, $unite, 'g');
                    return $quantiteMinimale * 0.3; // 300 FCFA par kg = 0.3 FCFA par g
                default:
                    return 0;
            }
        }

        // Convertir la quantité dans l'unité minimale de la matière
        try {
            $quantiteMinimale = $this->uniteConversionService->convertir(
                $quantite,
                $unite,
                $matiere->unite_minimale
            );

            // Multiplier par le prix par unité minimale
            return $matiere->prix_par_unite_minimale * $quantiteMinimale;
        } catch (\Exception $e) {
            // En cas d'erreur de conversion, logger et utiliser le calcul d'origine
            Log::error("Erreur de conversion d'unité: " . $e->getMessage(), [
                'quantite' => $quantite,
                'unite_source' => $unite,
                'unite_cible' => $matiere->unite_minimale,
                'matiere' => $matiere->nom
            ]);

            // Retour au calcul original en cas d'échec
            return $matiere->prix_par_unite * $quantite;
        }
    }

    public function calculerMatieres(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type_taule_id' => 'required|exists:type_taules,id',
                'nombre_taules' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Veuillez vérifier les informations saisies.');
            }

            $typeTaule = TypeTaule::findOrFail($request->type_taule_id);
            $nombreTaules = $request->nombre_taules;

            // Calculer les quantités selon les formules
            $quantiteFarine = $this->calculerQuantite($typeTaule->formule_farine, $nombreTaules);
            $quantiteEau = $this->calculerQuantite($typeTaule->formule_eau, $nombreTaules);
            $quantiteHuile = $this->calculerQuantite($typeTaule->formule_huile, $nombreTaules);
            $quantiteAutres = $this->calculerQuantite($typeTaule->formule_autres, $nombreTaules);

            // Récupérer les prix des matières avec conversion d'unités
            $prixFarine = $this->getPrixMatiere('Farine', $quantiteFarine, 'kg');
            $prixEau = $this->getPrixMatiere('Eau', $quantiteEau, 'l');
            $prixHuile = $this->getPrixMatiere('Huile', $quantiteHuile, 'l');
            $prixAutres = $this->getPrixMatiere('Autres', $quantiteAutres, 'kg');

            $prixTotal = $prixFarine + $prixEau + $prixHuile + $prixAutres;

            // Préparer les données pour la vue
            $matieres = [];

            if ($quantiteFarine > 0) {
                $matieres[] = [
                    'nom' => 'Farine',
                    'quantite' => $quantiteFarine,
                    'unite' => 'kg',
                    'prix' => $prixFarine
                ];
            }

            if ($quantiteEau > 0) {
                $matieres[] = [
                    'nom' => 'Eau',
                    'quantite' => $quantiteEau,
                    'unite' => 'L',
                    'prix' => $prixEau
                ];
            }

            if ($quantiteHuile > 0) {
                $matieres[] = [
                    'nom' => 'Huile',
                    'quantite' => $quantiteHuile,
                    'unite' => 'L',
                    'prix' => $prixHuile
                ];
            }

            if ($quantiteAutres > 0) {
                $matieres[] = [
                    'nom' => 'Autres ingrédients',
                    'quantite' => $quantiteAutres,
                    'unite' => 'kg',
                    'prix' => $prixAutres
                ];
            }

            // Retourner à la vue avec les données calculées
            return redirect()->back()
                ->with('matieres', $matieres)
                ->with('prixTotal', $prixTotal)
                ->with('typeTaule', $typeTaule)
                ->with('nombreTaules', $nombreTaules)
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Erreur lors du calcul des matières', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors du calcul: ' . $e->getMessage())
                ->with('errorDetails', env('APP_DEBUG') ? $e->getTraceAsString() : 'Pour plus de détails, consultez les logs du serveur')
                ->withInput();
        }
    }
}