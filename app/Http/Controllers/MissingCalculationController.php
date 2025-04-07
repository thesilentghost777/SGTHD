<?php

namespace App\Http\Controllers;

use App\Models\MissingCalculation;
use App\Models\MissingItem;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MissingCalculationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductGroup $group)
    {
        // Check if the user owns this group
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        $calculations = $group->missingCalculations()->orderBy('created_at', 'desc')->get();
        return view('inventory.calculations.index', compact('group', 'calculations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ProductGroup $group)
    {
        // Check if the user owns this group
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if there's already an open calculation
        $openCalculation = $group->missingCalculations()->where('status', 'open')->first();
        if ($openCalculation) {
            return redirect()->route('inventory.calculations.show', $openCalculation)
                ->with('info', 'Vous avez déjà une session de calcul ouverte.');
        }

        return view('inventory.calculations.create', compact('group'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ProductGroup $group)
    {
        // Check if the user owns this group
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if there's already an open calculation
        $openCalculation = $group->missingCalculations()->where('status', 'open')->first();
        if ($openCalculation) {
            return redirect()->route('inventory.calculations.show', $openCalculation)
                ->with('info', 'Vous avez déjà une session de calcul ouverte.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $validated['product_group_id'] = $group->id;
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'open';
        $validated['total_amount'] = 0;

        $calculation = MissingCalculation::create($validated);

        return redirect()->route('inventory.calculations.show', $calculation)
            ->with('success', 'Session de calcul de manquants créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MissingCalculation $calculation)
    {
        $group = $calculation->productGroup;

        // Check if the user owns the group this calculation belongs to
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        $missingItems = $calculation->missingItems()->with('product')->get();
        $products = $group->products()->whereNotIn('id', $missingItems->pluck('product_id'))->get();

        return view('inventory.calculations.show', compact('calculation', 'group', 'missingItems', 'products'));
    }

    /**
     * Add a new item to the calculation
     */
    public function addItem(Request $request, MissingCalculation $calculation)
    {
        $group = $calculation->productGroup;

        // Check if the user owns the group this calculation belongs to
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if the calculation is still open
        if ($calculation->status !== 'open') {
            return redirect()->route('inventory.calculations.show', $calculation)
                ->with('error', 'Cette session de calcul est fermée. Vous ne pouvez plus ajouter d\'articles.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'expected_quantity' => 'required|integer|min:0',
            'actual_quantity' => 'required|integer|min:0',
        ]);

        // Check if the product belongs to this group
        $product = Product::findOrFail($validated['product_id']);
        if ($product->product_group_id !== $group->id) {
            abort(403);
        }

        // Calculate missing quantity and amount
        $missingQuantity = $validated['expected_quantity'] - $validated['actual_quantity'];
        $amount = $missingQuantity * $product->price;

        DB::transaction(function () use ($calculation, $validated, $missingQuantity, $amount) {
            // Create the missing item
            MissingItem::create([
                'missing_calculation_id' => $calculation->id,
                'product_id' => $validated['product_id'],
                'expected_quantity' => $validated['expected_quantity'],
                'actual_quantity' => $validated['actual_quantity'],
                'missing_quantity' => $missingQuantity,
                'amount' => $amount
            ]);

            // Update the total amount in the calculation
            $calculation->total_amount += $amount;
            $calculation->save();
        });

        return redirect()->route('inventory.calculations.show', $calculation)
            ->with('success', 'Article ajouté avec succès.');
    }

    /**
     * Update an existing item in the calculation
     */
    public function updateItem(Request $request, MissingItem $item)
    {
        $calculation = $item->missingCalculation;
        $group = $calculation->productGroup;

        // Check if the user owns the group this calculation belongs to
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if the calculation is still open
        if ($calculation->status !== 'open') {
            return redirect()->route('inventory.calculations.show', $calculation)
                ->with('error', 'Cette session de calcul est fermée. Vous ne pouvez plus modifier d\'articles.');
        }

        $validated = $request->validate([
            'expected_quantity' => 'required|integer|min:0',
            'actual_quantity' => 'required|integer|min:0',
        ]);

        // Calculate new missing quantity and amount
        $missingQuantity = $validated['expected_quantity'] - $validated['actual_quantity'];
        $amount = $missingQuantity * $item->product->price;
        $oldAmount = $item->amount;

        DB::transaction(function () use ($calculation, $item, $validated, $missingQuantity, $amount, $oldAmount) {
            // Update the missing item
            $item->update([
                'expected_quantity' => $validated['expected_quantity'],
                'actual_quantity' => $validated['actual_quantity'],
                'missing_quantity' => $missingQuantity,
                'amount' => $amount
            ]);

            // Update the total amount in the calculation
            $calculation->total_amount = $calculation->total_amount - $oldAmount + $amount;
            $calculation->save();
        });

        return redirect()->route('inventory.calculations.show', $calculation)
            ->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Delete an item from the calculation
     */
    public function deleteItem(MissingItem $item)
    {
        $calculation = $item->missingCalculation;
        $group = $calculation->productGroup;

        // Check if the user owns the group this calculation belongs to
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if the calculation is still open
        if ($calculation->status !== 'open') {
            return redirect()->route('inventory.calculations.show', $calculation)
                ->with('error', 'Cette session de calcul est fermée. Vous ne pouvez plus supprimer d\'articles.');
        }

        DB::transaction(function () use ($calculation, $item) {
            // Update the total amount in the calculation
            $calculation->total_amount -= $item->amount;
            $calculation->save();

            // Delete the item
            $item->delete();
        });

        return redirect()->route('inventory.calculations.show', $calculation)
            ->with('success', 'Article supprimé avec succès.');
    }

    /**
     * Close a calculation session
     */
    public function close(MissingCalculation $calculation)
    {
        $group = $calculation->productGroup;

        // Check if the user owns the group this calculation belongs to
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if the calculation is still open
        if ($calculation->status !== 'open') {
            return redirect()->route('inventory.calculations.show', $calculation)
                ->with('error', 'Cette session de calcul est déjà fermée.');
        }

        // Close the calculation
        $calculation->status = 'closed';
        $calculation->save();

        return redirect()->route('inventory.calculations.show', $calculation)
            ->with('success', 'Session de calcul fermée avec succès.');
    }
}
