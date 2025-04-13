<?php

namespace App\Http\Controllers;

use App\Models\BagSale;
use Illuminate\Http\Request;

class BagRecoveryController extends Controller
{
    /**
     * Afficher la liste des sacs invendus qui peuvent être récupérés.
     */

     public function ex(){
        // Récupérer toutes les ventes avec des sacs invendus qui n'ont pas encore été récupérés
        $sales = BagSale::with(['reception.assignment.bag', 'reception.assignment.user'])
            ->where('quantity_unsold', '>', 0)
            ->where('is_recovered', false)
            ->latest()
            ->get();

        // Regrouper par utilisateur (serveur)
        $salesByServer = $sales->groupBy('reception.assignment.user.name');

        return view('bags.recovery.index', compact('salesByServer'));
     }
    public function index()
    {
        // Récupérer toutes les ventes avec des sacs invendus qui n'ont pas encore été récupérés
        $sales = BagSale::with(['reception.assignment.bag', 'reception.assignment.user'])
            ->where('quantity_unsold', '>', 0)
            ->where('is_recovered', false)
            ->latest()
            ->get();

        // Regrouper par utilisateur (serveur)
        $salesByServer = $sales->groupBy('reception.assignment.user.name');

        return view('bags.recovery.index', compact('salesByServer'));
    }

    /**
     * Récupérer les sacs invendus.
     */
    public function recover(Request $request, BagSale $sale)
    {
        $validated = $request->validate([
            'quantity_to_recover' => ['required', 'integer', 'min:1', 'max:'.$sale->quantity_unsold],
        ]);

        // Vérifier que les sacs n'ont pas déjà été récupérés
        if ($sale->is_recovered) {
            return back()->withErrors(['message' => 'Ces sacs ont déjà été récupérés.']);
        }

        // Récupérer le sac associé et augmenter son stock
        $bag = $sale->bag;
        $bag->increaseStock($validated['quantity_to_recover']);

        // Si tous les sacs invendus ont été récupérés, marquer la vente comme récupérée
        if ($validated['quantity_to_recover'] == $sale->quantity_unsold) {
            $sale->is_recovered = true;
            $sale->save();
        }

        return redirect()->route('bag.recovery.index')
            ->with('success', 'Sacs récupérés avec succès. Le stock a été mis à jour.');
    }
}
