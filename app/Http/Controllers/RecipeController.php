<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with(['category', 'user'])->orderBy('name')->get();
        return view('recipes.index', compact('recipes'));
    }

    public function create()
    {
        $categories = RecipeCategory::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        return view('recipes.create', compact('categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preparation_time' => 'nullable|integer|min:0',
            'cooking_time' => 'nullable|integer|min:0',
            'rest_time' => 'nullable|integer|min:0',
            'yield_quantity' => 'nullable|integer|min:1',
            'difficulty_level' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:recipe_categories,id',
            'active' => 'boolean',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.unit' => 'nullable|string',
            'ingredients.*.notes' => 'nullable|string',
            'steps' => 'required|array|min:1',
            'steps.*.instruction' => 'required|string',
            'steps.*.tips' => 'nullable|string',
            'steps.*.time_required' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Create recipe
            $recipe = Recipe::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'preparation_time' => $validated['preparation_time'],
                'cooking_time' => $validated['cooking_time'],
                'rest_time' => $validated['rest_time'],
                'yield_quantity' => $validated['yield_quantity'],
                'difficulty_level' => $validated['difficulty_level'],
                'category_id' => $validated['category_id'],
                'user_id' => auth()->id(),
                'active' => $validated['active'] ?? true,
            ]);

            // Add ingredients
            foreach ($validated['ingredients'] as $index => $ingredientData) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredientData['id'],
                    'quantity' => $ingredientData['quantity'],
                    'unit' => $ingredientData['unit'] ?? null,
                    'notes' => $ingredientData['notes'] ?? null,
                    'order' => $index + 1,
                ]);
            }

            // Add steps
            foreach ($validated['steps'] as $index => $stepData) {
                RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'step_number' => $index + 1,
                    'instruction' => $stepData['instruction'],
                    'tips' => $stepData['tips'] ?? null,
                    'time_required' => $stepData['time_required'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('recipes.show', $recipe)
                ->with('success', 'Recette créée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['category', 'user', 'ingredients.ingredient', 'steps']);
        return view('recipes.show', compact('recipe'));
    }

    public function edit(Recipe $recipe)
    {
        $recipe->load(['ingredients.ingredient', 'steps']);
        $categories = RecipeCategory::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        return view('recipes.edit', compact('recipe', 'categories', 'ingredients'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'preparation_time' => 'nullable|integer|min:0',
            'cooking_time' => 'nullable|integer|min:0',
            'rest_time' => 'nullable|integer|min:0',
            'yield_quantity' => 'nullable|integer|min:1',
            'difficulty_level' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:recipe_categories,id',
            'active' => 'boolean',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.unit' => 'nullable|string',
            'ingredients.*.notes' => 'nullable|string',
            'steps' => 'required|array|min:1',
            'steps.*.instruction' => 'required|string',
            'steps.*.tips' => 'nullable|string',
            'steps.*.time_required' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Update recipe
            $recipe->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'preparation_time' => $validated['preparation_time'],
                'cooking_time' => $validated['cooking_time'],
                'rest_time' => $validated['rest_time'],
                'yield_quantity' => $validated['yield_quantity'],
                'difficulty_level' => $validated['difficulty_level'],
                'category_id' => $validated['category_id'],
                'active' => $validated['active'] ?? true,
            ]);

            // Delete existing ingredients and steps
            $recipe->ingredients()->delete();
            $recipe->steps()->delete();

            // Add ingredients
            foreach ($validated['ingredients'] as $index => $ingredientData) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredientData['id'],
                    'quantity' => $ingredientData['quantity'],
                    'unit' => $ingredientData['unit'] ?? null,
                    'notes' => $ingredientData['notes'] ?? null,
                    'order' => $index + 1,
                ]);
            }

            // Add steps
            foreach ($validated['steps'] as $index => $stepData) {
                RecipeStep::create([
                    'recipe_id' => $recipe->id,
                    'step_number' => $index + 1,
                    'instruction' => $stepData['instruction'],
                    'tips' => $stepData['tips'] ?? null,
                    'time_required' => $stepData['time_required'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('recipes.show', $recipe)
                ->with('success', 'Recette mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('recipes.index')
            ->with('success', 'Recette supprimée avec succès.');
    }

    public function instructions()
    {
        $recipes = Recipe::where('active', true)
            ->with(['category', 'ingredients.ingredient', 'steps'])
            ->orderBy('name')
            ->get();

        return view('recipes.instructions', compact('recipes'));
    }

    public function showInstructions(Recipe $recipe)
    {
        $recipe->load(['category', 'ingredients.ingredient', 'steps']);
        return view('recipes.show_instructions', compact('recipe'));
    }

    public function adminIndex()
    {
        return view('recipes.admin_index');
    }
}
