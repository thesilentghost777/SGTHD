<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\Produit_fixes;
use App\Models\MatiereRecommander;
use App\Services\RecipeCalculatorService;
use App\Enums\UniteMinimale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecetteController extends Controller
{
    protected $calculator;

    public function __construct(RecipeCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    public function index()
    {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $produits = Produit_fixes::all();

        return view('pages.recettes.index', [
            'produits' => $produits,
            'nom' => $employe->name,
            'secteur' => $employe->secteur,
            'unites' => UniteMinimale::cases()
        ]);
    }

    public function create()
    {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        $role = $employe->role === 'patissier' ? 'patisserie' :
               ($employe->role === 'boulanger' ? 'boulangerie' : '');

        return view('pages.recettes.create', [
            'produits' => Produit_fixes::all(),
            'matieres' => Matiere::all(),
            'unites' => UniteMinimale::cases(),
            'nom' => $employe->name,
            'secteur' => $employe->secteur
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'produit' => 'required|exists:Produit_fixes,code_produit',
                'quantitep' => 'required|integer|min:1',
                'matieres' => 'required|array',
                'matieres.*.matiere_id' => 'required|exists:Matiere,id',
                'matieres.*.quantite' => 'required|numeric|min:0',
                'matieres.*.unite' => 'required|string'
            ]);

            foreach ($request->matieres as $matiere) {
                MatiereRecommander::create([
                    'produit' => $request->produit,
                    'matierep' => $matiere['matiere_id'],
                    'quantitep' => $request->quantitep,
                    'quantite' => $matiere['quantite'],
                    'unite' => $matiere['unite']
                ]);
            }

            DB::commit();
            return redirect()->route('recettes.index')->with('success', 'Recette ajoutée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de l\'ajout de la recette: ' . $e->getMessage());
        }
    }

    public function calculateIngredients(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:Produit_fixes,code_produit',
            'quantite_cible' => 'required|numeric|min:0'
        ]);

        $recette = MatiereRecommander::where('produit', $request->produit_id)
            ->with('matiere')
            ->get()
            ->groupBy('produit')
            ->map(function ($ingredients) {
                return [
                    'quantitep' => $ingredients->first()->quantitep,
                    'ingredients' => $ingredients->map(function ($ingredient) {
                        return [
                            'nom' => $ingredient->matiere->nom,
                            'quantite' => $ingredient->quantite,
                            'unite' => $ingredient->unite
                        ];
                    })->toArray()
                ];
            })
            ->first();

        if (!$recette) {
            return response()->json(['error' => 'Recette non trouvée'], 404);
        }

        $ingredients = $this->calculator->calculateAllIngredientsForRecipe(
            $recette,
            $request->quantite_cible
        );

        return response()->json(['ingredients' => $ingredients]);
    }
    public function destroy($produitId)
{
    try {
        DB::beginTransaction();

        $recettes = MatiereRecommander::where('produit', $produitId)->get();

        if ($recettes->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Recette non trouvée'
            ], 404);
        }

        MatiereRecommander::where('produit', $produitId)->delete();

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Recette supprimée avec succès'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
        ], 500);
    }
}
}
