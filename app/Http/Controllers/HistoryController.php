<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Affiche la liste de l'historique
     */
    public function index()
    {
        $histories = History::with('user')->latest()->paginate(20);
        return view('history.index', compact('histories'));
    }

    /**
     * Historise une action
     *
     * @param string $description Description de l'action
     * @param string|null $actionType Type d'action (create, update, delete, etc.)
     * @return History
     */
    public function historiser($description, $actionType = null)
    {
        $history = new History();
        $history->description = $description;
        $history->action_type = $actionType;

        // Ajoute l'utilisateur connecté s'il existe
        if (Auth::check()) {
            $history->user_id = Auth::id();
        }

        // Enregistre l'adresse IP
        $history->ip_address = request()->ip();

        $history->save();

        return $history;
    }

    /**
     * Supprimer un élément d'historique (réservé aux administrateurs)
     */
    public function destroy(History $history)
    {
        $this->authorize('delete-history'); // Assurez-vous de définir cette policy
        $history->delete();

        return redirect()->route('history.index')
            ->with('success', 'Entrée d\'historique supprimée avec succès');
    }
}