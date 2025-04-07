<?php

namespace App\Http\Controllers;

use App\Models\Bag;

use App\Models\User;

use App\Models\BagAssignment;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class BagAssignmentController extends Controller

{

    /**

     * Afficher le formulaire d'assignation des sacs.

     */

    public function create()

    {

        $bags = Bag::where('stock_quantity', '>', 0)->get();

        $servers = User::where('secteur', 'vente')->get();

        $recentAssignments = BagAssignment::with(['bag', 'user'])

            ->latest()

            ->take(5)

            ->get();



        return view('bags.assignments.create', compact('bags', 'servers', 'recentAssignments'));

    }

    /**

     * Stocker une nouvelle assignation de sacs.

     */

    public function store(Request $request)

    {

        $validated = $request->validate([

            'bag_id' => ['required', 'exists:bags,id'],

            'user_id' => ['required', 'exists:users,id'],

            'quantity_assigned' => ['required', 'integer', 'min:1'],

            'notes' => ['nullable', 'string'],

        ]);

        $bag = Bag::findOrFail($validated['bag_id']);

        // Vérifier si la quantité demandée est disponible

        if ($bag->stock_quantity < $validated['quantity_assigned']) {

            return back()->withErrors([

                'quantity_assigned' => 'La quantité demandée dépasse la quantité disponible en stock.'

            ])->withInput();

        }

        DB::transaction(function () use ($validated, $bag) {

            // Créer l'assignation

            BagAssignment::create($validated);



            // Mettre à jour le stock

            $bag->decrement('stock_quantity', $validated['quantity_assigned']);

        });

        return redirect()->route('bag.assignments.create')

            ->with('success', 'Sacs assignés avec succès.');

    }

    /**

     * Afficher le formulaire de modification d'une assignation.

     */

    public function edit(BagAssignment $assignment)

    {

        $bags = Bag::all();

        $servers = User::where('role', 'serveur')->get();



        return view('bags.assignments.edit', compact('assignment', 'bags', 'servers'));

    }

    /**

     * Mettre à jour une assignation de sacs.

     */

    public function update(Request $request, BagAssignment $assignment)

    {

        $validated = $request->validate([

            'quantity_assigned' => ['required', 'integer', 'min:1'],

            'notes' => ['nullable', 'string'],

        ]);

        $bag = $assignment->bag;

        $oldQuantity = $assignment->quantity_assigned;

        $newQuantity = $validated['quantity_assigned'];

        $difference = $newQuantity - $oldQuantity;

        // Vérifier si l'augmentation est possible avec le stock actuel

        if ($difference > 0 && $bag->stock_quantity < $difference) {

            return back()->withErrors([

                'quantity_assigned' => 'La quantité demandée dépasse la quantité disponible en stock.'

            ])->withInput();

        }

        DB::transaction(function () use ($validated, $assignment, $bag, $difference) {

            // Mettre à jour l'assignation

            $assignment->update($validated);



            // Ajuster le stock si nécessaire

            if ($difference != 0) {

                $bag->increment('stock_quantity', -$difference);

            }

        });

        return redirect()->route('bag.assignments.create')

            ->with('success', 'Assignation mise à jour avec succès.');

    }

}
