<?php

namespace App\Http\Controllers;

use App\Models\BagReception;

use App\Models\BagSale;

use Illuminate\Http\Request;

class BagSaleController extends Controller

{

    /**

     * Afficher le formulaire de déclaration des ventes de sacs.

     */

    public function create()

    {

        $user = auth()->user();



        // Récupérer les réceptions qui n'ont pas encore de vente associée

        $receptions = BagReception::with(['assignment.bag'])

            ->whereHas('assignment', function ($query) use ($user) {

                $query->where('user_id', $user->id);

            })

            ->whereDoesntHave('sale')

            ->get();



        $recentSales = BagSale::with(['reception.assignment.bag'])

            ->whereHas('reception.assignment', function ($query) use ($user) {

                $query->where('user_id', $user->id);

            })

            ->latest()

            ->take(5)

            ->get();



        return view('bags.sales.create', compact('receptions', 'recentSales'));

    }

    /**

     * Stocker une nouvelle déclaration de vente de sacs.

     */

    public function store(Request $request)

    {

        $validated = $request->validate([

            'bag_reception_id' => ['required', 'exists:bag_receptions,id'],

            'quantity_sold' => ['required', 'integer', 'min:0'],

            'quantity_unsold' => ['required', 'integer', 'min:0'],

            'notes' => ['nullable', 'string'],

        ]);

        $reception = BagReception::findOrFail($validated['bag_reception_id']);



        // Vérifier que l'utilisateur connecté est bien le destinataire de l'assignation

        if ($reception->assignment->user_id !== auth()->id()) {

            return back()->withErrors([

                'bag_reception_id' => 'Vous n\'êtes pas autorisé à enregistrer cette vente.'

            ])->withInput();

        }



        // Vérifier que cette réception n'a pas déjà une vente associée

        if ($reception->has_sale) {

            return back()->withErrors([

                'bag_reception_id' => 'Cette réception a déjà une vente associée.'

            ])->withInput();

        }



        // Vérifier que la somme des sacs vendus et invendus correspond à la quantité reçue

        $totalDeclared = $validated['quantity_sold'] + $validated['quantity_unsold'];

        if ($totalDeclared != $reception->quantity_received) {

            return back()->withErrors([

                'quantity_sold' => 'La somme des sacs vendus et invendus doit être égale à la quantité reçue (' . $reception->quantity_received . ').'

            ])->withInput();

        }

        BagSale::create($validated);

        return redirect()->route('bag.sales.create')

            ->with('success', 'Vente enregistrée avec succès.');

    }

    /**

     * Afficher le formulaire de modification d'une vente.

     */

    public function edit(BagSale $sale)

    {

        // Vérifier que l'utilisateur connecté est bien le destinataire de l'assignation

        if ($sale->reception->assignment->user_id !== auth()->id()) {

            return abort(403, 'Vous n\'êtes pas autorisé à modifier cette vente.');

        }



        return view('bags.sales.edit', compact('sale'));

    }

    /**

     * Mettre à jour une vente de sacs.

     */

    public function update(Request $request, BagSale $sale)

    {

        // Vérifier que l'utilisateur connecté est bien le destinataire de l'assignation

        if ($sale->reception->assignment->user_id !== auth()->id()) {

            return abort(403, 'Vous n\'êtes pas autorisé à modifier cette vente.');

        }



        $validated = $request->validate([

            'quantity_sold' => ['required', 'integer', 'min:0'],

            'quantity_unsold' => ['required', 'integer', 'min:0'],

            'notes' => ['nullable', 'string'],

        ]);



        // Vérifier que la somme des sacs vendus et invendus correspond à la quantité reçue

        $totalDeclared = $validated['quantity_sold'] + $validated['quantity_unsold'];

        if ($totalDeclared != $sale->reception->quantity_received) {

            return back()->withErrors([

                'quantity_sold' => 'La somme des sacs vendus et invendus doit être égale à la quantité reçue (' . $sale->reception->quantity_received . ').'

            ])->withInput();

        }

        $sale->update($validated);

        return redirect()->route('bag.sales.create')

            ->with('success', 'Vente mise à jour avec succès.');

    }

}
