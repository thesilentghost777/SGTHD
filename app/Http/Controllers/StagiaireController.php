<?php

namespace App\Http\Controllers;

use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use PDF;

class StagiaireController extends Controller
{
    public function index()
    {
        $stagiaires = Stagiaire::latest()->get();
        return view('stagiaires.index', compact('stagiaires'));
    }

    public function create()
    {
        return view('stagiaires.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:stagiaires',
            'telephone' => 'required|string',
            'ecole' => 'required|string',
            'niveau_etude' => 'required|string',
            'filiere' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'departement' => 'required|string',
            'nature_travail' => 'required|string',
            'type_stage' => 'required|in:academique,professionnel',
        ]);

        Stagiaire::create($validated);

        return redirect()->route('stagiaires.index')
            ->with('success', 'Stagiaire ajouté avec succès.');
    }

    public function edit(Stagiaire $stagiaire)
    {
        return view('stagiaires.edit', compact('stagiaire'));
    }

    public function update(Request $request, Stagiaire $stagiaire)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:stagiaires,email,'.$stagiaire->id,
            'telephone' => 'required|string',
            'ecole' => 'required|string',
            'niveau_etude' => 'required|string',
            'filiere' => 'required|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'departement' => 'required|string',
            'nature_travail' => 'required|string',
            'type_stage' => 'required|in:academique,professionnel',
        ]);

        $stagiaire->update($validated);

        return redirect()->route('stagiaires.index')
            ->with('success', 'Informations du stagiaire mises à jour.');
    }

    public function destroy(Stagiaire $stagiaire)
    {
        $stagiaire->delete();
        return redirect()->route('stagiaires.index')
            ->with('success', 'Stagiaire supprimé avec succès.');
    }

    public function setRemuneration(Request $request, Stagiaire $stagiaire)
    {
        $validated = $request->validate([
            'remuneration' => 'required|numeric|min:0',
        ]);

        $stagiaire->update($validated);

        return redirect()->route('stagiaires.index')
            ->with('success', 'Rémunération mise à jour.');
    }

    public function setAppreciation(Request $request, Stagiaire $stagiaire)
    {
        $validated = $request->validate([
            'appreciation' => 'required|string',
        ]);

        $stagiaire->update($validated);

        return redirect()->route('stagiaires.index')
            ->with('success', 'Appréciation ajoutée.');
    }

    public function generateReport(Stagiaire $stagiaire)
    {
        $pdf = PDF::loadView('stagiaires.report', compact('stagiaire'));

        $stagiaire->update(['rapport_genere' => true]);

        return $pdf->download('rapport_stage_'.$stagiaire->nom.'_'.$stagiaire->prenom.'.pdf');
    }
}
