<?php

namespace App\Http\Controllers;

use App\Models\RecipeCategory;
use Illuminate\Http\Request;

class RecipeCategoryController extends Controller
{
    public function index()
    {
        $categories = RecipeCategory::orderBy('name')->get();
        return view('recipes.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('recipes.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        RecipeCategory::create($validated);

        return redirect()->route('recipe.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(RecipeCategory $category)
    {
        return view('recipes.categories.edit', compact('category'));
    }

    public function update(Request $request, RecipeCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('recipe.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(RecipeCategory $category)
    {
        $category->delete();

        return redirect()->route('recipe.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}