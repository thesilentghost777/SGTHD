<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::query();
        $user = auth()->user();
        if ($user->role === 'calviste') {
            $query->where('type', 'boisson');
        }else if ($user->role === 'magasinier') {
            $query->where('type', 'magasin');
        }
        // Recherche par nom ou référence
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }


        // Filtrer par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $produits = $query->latest()->paginate(10);
        return view('stock.produits.index', compact('produits'));
    }

    public function indexByType($type)
    {
        if (!in_array($type, ['magasin', 'boisson'])) {
            abort(404);
        }

        $produits = Produit::where('type', $type)->latest()->paginate(10);
        return view('stock.produits.index', compact('produits'));
    }

    public function create()
    {
        return view('stock.produits.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|unique:produits',
            'type' => 'required|in:magasin,boisson',
            'prix_unitaire' => 'required|numeric|min:0',
            'seuil_alerte' => 'required|integer|min:1'
        ]);

        Produit::create($validated);

        return redirect()->route('produits.index')
            ->with('success', 'Produit ajouté avec succès');
    }

    public function show(Produit $produit)
    {
        $mouvements = $produit->mouvements()->latest()->take(10)->get();
        return view('stock.produits.show', compact('produit', 'mouvements'));
    }

    public function edit(Produit $produit)
    {
        return view('stock.produits.edit', compact('produit'));
    }

    public function update(Request $request, Produit $produit)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|unique:produits,reference,' . $produit->id,
            'prix_unitaire' => 'required|numeric|min:0',
            'seuil_alerte' => 'required|integer|min:1'
        ]);

        $produit->update($validated);

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour avec succès');
    }

    public function destroy(Produit $produit)
    {
        // Vérifier si le produit a des mouvements avant de supprimer
        if ($produit->mouvements()->count() > 0 || $produit->inventaires()->count() > 0) {
            return redirect()->route('produits.index')
                ->with('error', 'Impossible de supprimer ce produit car il a des mouvements ou inventaires associés');
        }

        $produit->delete();
        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé avec succès');
    }

    public function alertes()
    {
        $produits = Produit::whereRaw('quantite < seuil_alerte')
            ->latest()
            ->paginate(10);

        return view('stock.produits.alertes', compact('produits'));
    }
}
