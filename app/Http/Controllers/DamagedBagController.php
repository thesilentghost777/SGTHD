<?php

namespace App\Http\Controllers;

use App\Models\Bag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DamagedBagController extends Controller
{
    /**
     * Affiche la liste des sacs en stock avec la possibilité de déclarer des avaries
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $bags = Bag::where('stock_quantity', '>', 0)
                  ->orderBy('name')
                  ->get();

        return view('bags.damaged.index', compact('bags'));
    }

    /**
     * Affiche le formulaire pour déclarer des sacs avariés
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function create($id)
    {
        $bag = Bag::findOrFail($id);

        // Vérifier que le sac a du stock
        if ($bag->stock_quantity <= 0) {
            return redirect()->route('damaged-bags.index')
                ->with('error', 'Ce sac n\'a pas de stock disponible.');
        }

        return view('bags.damaged.create', compact('bag'));
    }

    /**
     * Enregistre la déclaration de sacs avariés
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $id)
    {
        $bag = Bag::findOrFail($id);

        // Validation de la requête
        $validated = $request->validate([
            'damaged_quantity' => 'required|integer|min:1|max:' . $bag->stock_quantity,
            'reason' => 'required|string|max:255',
        ]);

        try {
            // Mettre à jour le stock en déduisant la quantité avariée
            $bag->stock_quantity -= $validated['damaged_quantity'];
            $bag->save();

            // Enregistrer l'opération dans un journal d'activité (si nécessaire)
            // Ici, on pourrait créer un modèle DamagedBagLog pour garder une trace

            Log::info('Sacs avariés déclarés', [
                'bag_id' => $bag->id,
                'bag_name' => $bag->name,
                'quantity' => $validated['damaged_quantity'],
                'user_id' => Auth::id(),
                'reason' => $validated['reason']
            ]);

            return redirect()->route('damaged-bags.index')
                ->with('success', 'La quantité de sacs avariés a été correctement enregistrée.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement des sacs avariés', [
                'bag_id' => $bag->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement des sacs avariés.')
                ->withInput();
        }
    }
}