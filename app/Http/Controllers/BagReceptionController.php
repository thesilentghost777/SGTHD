<?php

namespace App\Http\Controllers;

use App\Models\BagAssignment;

use App\Models\BagReception;

use Illuminate\Http\Request;

class BagReceptionController extends Controller

{

    /**

     * Afficher le formulaire de réception des sacs.

     */

    public function create()

    {

        $user = auth()->user();

        $assignments = BagAssignment::with('bag')

            ->where('user_id', $user->id)

            ->get();



        $recentReceptions = BagReception::with(['assignment.bag', 'assignment.user'])

            ->whereHas('assignment', function ($query) use ($user) {

                $query->where('user_id', $user->id);

            })

            ->latest()

            ->take(5)

            ->get();



        return view('bags.receptions.create', compact('assignments', 'recentReceptions'));

    }

    /**

     * Stocker une nouvelle réception de sacs.

     */

    public function store(Request $request)

    {

        $validated = $request->validate([

            'bag_assignment_id' => ['required', 'exists:bag_assignments,id'],

            'quantity_received' => ['required', 'integer', 'min:0'],

            'notes' => ['nullable', 'string'],

        ]);

        $assignment = BagAssignment::findOrFail($validated['bag_assignment_id']);



        // Vérifier que l'utilisateur connecté est bien le destinataire de l'assignation

        if ($assignment->user_id !== auth()->id()) {

            return back()->withErrors([

                'bag_assignment_id' => 'Vous n\'êtes pas autorisé à enregistrer cette réception.'

            ])->withInput();

        }

        BagReception::create($validated);

        return redirect()->route('bag.receptions.create')

            ->with('success', 'Réception enregistrée avec succès.');

    }

    /**

     * Afficher le formulaire de modification d'une réception.

     */

    public function edit(BagReception $reception)

    {

        // Vérifier que l'utilisateur connecté est bien le destinataire de l'assignation

        if ($reception->assignment->user_id !== auth()->id()) {

            return abort(403, 'Vous n\'êtes pas autorisé à modifier cette réception.');

        }



        return view('bags.receptions.edit', compact('reception'));

    }

    /**

     * Mettre à jour une réception de sacs.

     */

    public function update(Request $request, BagReception $reception)

    {

        // Vérifier que l'utilisateur connecté est bien le destinataire de l'assignation

        if ($reception->assignment->user_id !== auth()->id()) {

            return abort(403, 'Vous n\'êtes pas autorisé à modifier cette réception.');

        }



        $validated = $request->validate([

            'quantity_received' => ['required', 'integer', 'min:0'],

            'notes' => ['nullable', 'string'],

        ]);

        $reception->update($validated);

        return redirect()->route('bag.receptions.create')

            ->with('success', 'Réception mise à jour avec succès.');

    }

}
