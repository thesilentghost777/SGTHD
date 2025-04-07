<?php

namespace App\Http\Controllers;

use App\Models\Bag;
use App\Models\BagTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BagController extends Controller
{
    public function index()
    {
        $nom = auth()->user()->name;
        $bags = Bag::all();
        return view('bags.index', compact('bags','nom'));
    }

    public function create()
    {
        return view('bags.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'alert_threshold' => 'required|integer|min:0'
        ]);

        Bag::create($validated);

        return redirect()->route('bags.index2')
            ->with('success', 'Sac créé avec succès.');
    }

    public function receive()
    {
        $nom = auth()->user()->name;
        $bags = Bag::all();
        return view('bags.receive', compact('bags','nom'));
    }

    public function storeReceived(Request $request)
    {
        $validated = $request->validate([
            'bag_id' => 'required|exists:bags,id',
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date'
        ]);

        $bag = Bag::findOrFail($validated['bag_id']);

        BagTransaction::create([
            'bag_id' => $validated['bag_id'],
            'type' => 'received',
            'quantity' => $validated['quantity'],
            'transaction_date' => $validated['transaction_date']
        ]);

        $bag->increment('stock_quantity', $validated['quantity']);

        return redirect()->route('bags.receive')
            ->with('success', 'Réception enregistrée avec succès.');
    }

    public function sell()
    {
        $nom = auth()->user()->name;
        $bags = Bag::all();
        return view('bags.sell', compact('bags','nom'));
    }

    public function storeSold(Request $request)
    {
        $validated = $request->validate([
            'bag_id' => 'required|exists:bags,id',
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date'
        ]);

        $bag = Bag::findOrFail($validated['bag_id']);

        if ($bag->stock_quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stock insuffisant.']);
        }

        BagTransaction::create([
            'bag_id' => $validated['bag_id'],
            'type' => 'sold',
            'quantity' => $validated['quantity'],
            'transaction_date' => $validated['transaction_date']
        ]);

        $bag->decrement('stock_quantity', $validated['quantity']);

        return redirect()->route('bags.sell')
            ->with('success', 'Vente enregistrée avec succès.');
    }
    public function index2()

    {
        $nom = auth()->user()->name;
        $bags = Bag::latest()->get();

        return view('bags.index2', compact('bags','nom'));

    }

    /**

     * Afficher le formulaire de création d'un sac.

     */

    public function create2()

    {

        return view('bags.create2');

    }

    /**

     * Stocker un nouveau sac dans la base de données.

     */

    public function store2(Request $request)

    {

        $validated = $request->validate([

            'name' => ['required', 'string', 'max:255', 'unique:bags,name'],

            'price' => ['required', 'numeric', 'min:0'],

            'stock_quantity' => ['required', 'integer', 'min:0'],

            'alert_threshold' => ['required', 'integer', 'min:1'],

        ]);

        Bag::create($validated);

        return redirect()->route('bags.index')

            ->with('success', 'Sac créé avec succès.');

    }

    /**

     * Afficher le formulaire d'édition d'un sac.

     */

    public function edit(Bag $bag)

    {

        return view('bags.edit', compact('bag'));

    }

    /**

     * Mettre à jour le sac dans la base de données.

     */

    public function update(Request $request, Bag $bag)

    {

        $validated = $request->validate([

            'name' => ['required', 'string', 'max:255', Rule::unique('bags')->ignore($bag)],

            'price' => ['required', 'numeric', 'min:0'],

            'stock_quantity' => ['required', 'integer', 'min:0'],

            'alert_threshold' => ['required', 'integer', 'min:1'],

        ]);

        $bag->update($validated);

        return redirect()->route('bags.index2')

            ->with('success', 'Sac mis à jour avec succès.');

    }

    /**

     * Supprimer le sac de la base de données.

     */

    public function destroy(Bag $bag)

    {

        $bag->delete();

        return redirect()->route('bags.index2')

            ->with('success', 'Sac supprimé avec succès.');

    }

    public function show(Bag $bag)
    {
        return view('bags.show', compact('bag'));
    }
}
