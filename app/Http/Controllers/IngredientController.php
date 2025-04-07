<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::orderBy('name')->get();
        return view('recipes.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('recipes.ingredients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
        ]);

        Ingredient::create($validated);

        return redirect()->route('recipe.ingredients.index')
            ->with('success', 'Ingrédient créé avec succès.');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('recipes.ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
        ]);

        $ingredient->update($validated);

        return redirect()->route('recipe.ingredients.index')
            ->with('success', 'Ingrédient mis à jour avec succès.');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return redirect()->route('recipe.ingredients.index')
            ->with('success', 'Ingrédient supprimé avec succès.');
    }
}
