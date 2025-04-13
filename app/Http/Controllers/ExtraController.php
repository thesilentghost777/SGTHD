<?php

namespace App\Http\Controllers;

use App\Models\Extra;
use Illuminate\Http\Request;
use App\Traits\HistorisableActions;

class ExtraController extends Controller
{
    /**
     * Afficher la liste des extras
     */
    use HistorisableActions;

    public function index()
    {
        $extras = Extra::orderBy('secteur')->paginate(10);
        return view('extras.index', compact('extras'));
    }
    public function index2()
    {
        $extras = Extra::orderBy('secteur')->paginate(10);
        return view('extras.index2', compact('extras'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('extras.create');
    }

    /**
     * Enregistrer un nouvel extra
     */
    public function store(Request $request)
    {
        $validated = $request->validate(Extra::rules(), Extra::messages());

        Extra::create($validated);
        $user = auth()->user();
        $this->historiser("L'utilisateur {$user->name} a créé un reglement", 'create_extra');
        return redirect()->route('extras.index')
            ->with('success', 'Reglementation créé avec succès.');
    }

    /**
     * Afficher un extra spécifique
     */
    public function show(Extra $extra)
    {
        return view('extras.show', compact('extra'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit(Extra $extra)
    {
        return view('extras.edit', compact('extra'));
    }

    /**
     * Mettre à jour un extra
     */
    public function update(Request $request, Extra $extra)
    {
        $validated = $request->validate(Extra::rules(), Extra::messages());

        $extra->update($validated);

        return redirect()->route('extras.index')
            ->with('success', 'Extra modifié avec succès.');
    }

    /**
     * Supprimer un extra
     */
    public function destroy(Extra $extra)
    {
        $extra->delete();

        return redirect()->route('extras.index')
            ->with('success', 'Reglementation supprimé avec succès.');
    }
}
