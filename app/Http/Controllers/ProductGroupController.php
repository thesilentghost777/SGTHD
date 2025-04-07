<?php

namespace App\Http\Controllers;

use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = ProductGroup::where('user_id', Auth::id())->get();
        return view('inventory.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $validated['user_id'] = Auth::id();

        $group = ProductGroup::create($validated);

        return redirect()->route('inventory.groups.show', $group)
            ->with('success', 'Groupe créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductGroup $group)
    {
        // Check if the user owns this group
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        $products = $group->products()->get();
        $calculations = $group->missingCalculations()->orderBy('created_at', 'desc')->get();

        return view('inventory.groups.show', compact('group', 'products', 'calculations'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductGroup $group)
    {
        // Check if the user owns this group
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        return view('inventory.groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductGroup $group)
    {
        // Check if the user owns this group
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $group->update($validated);

        return redirect()->route('inventory.groups.show', $group)
            ->with('success', 'Groupe mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductGroup $group)
    {
        // Check if the user owns this group
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete the group and all associated products and calculations
        $group->delete();

        return redirect()->route('inventory.groups.index')
            ->with('success', 'Groupe supprimé avec succès');
    }
}
