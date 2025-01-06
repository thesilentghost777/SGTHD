<?php

namespace App\Http\Controllers;

use App\Models\Salaire;
use App\Models\AvanceSalaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaireController extends Controller
{
    public function reclamerAs()
    {
        $employe = auth()->user();

        if(!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        // Vérification de la date
        if (now()->day < 9) {
            return view('pages.error_as', [
                'error' => 'Vous ne pouvez pas réclamer l\'AS avant le 9 de chaque mois',
                'hasRequest' => false
            ]);
        }

        // Vérification si l'employé a déjà une avance ce mois-ci
        $hasRequest = AvanceSalaire::where('id_employe', $employe->id)
            ->whereMonth('mois_as', now()->month)
            ->exists();

        if ($hasRequest) {
            return view('pages.error_as', [
                'error' => 'Vous avez déjà soumis une demande pour ce mois-ci',
                'hasRequest' => true
            ]);
        }

        // Si toutes les conditions sont satisfaites
        $as = new AvanceSalaire();
        return view('salaires.reclamer-as', compact('as'));
    }

    public function store_demandes_AS(Request $request)
    {
        $request->validate([
            'sommeAs' => 'required|numeric|min:0'
        ]);

        $salaire = Salaire::where('id_employe', Auth::id())->first();

        if (!$salaire) {
            return redirect()->back()->with('error', 'Aucun salaire trouvé.');
        }

        $sommeRestante = $salaire->somme - $request->sommeAs;

        if ($sommeRestante < 5000) {
            return redirect()->back()->with('error', 'Le montant demandé est trop élevé.');
        }

        AvanceSalaire::create([
            'id_employe' => Auth::id(),
            'sommeAs' => $request->sommeAs,
            'flag' => false,
            'mois_as' => now()
        ]);

        return redirect()->route('voir-status')->with('success', 'Demande envoyée avec succès.');
    }

    public function voir_Status()
    {
        $as = AvanceSalaire::where('id_employe', Auth::id())
            ->whereMonth('created_at', now()->month)
            ->first();

        return view('salaires.status', compact('as'));
    }

    public function validerAs()
    {

        $demandes = AvanceSalaire::with('employe')
            ->where('flag', false)
            ->get();

        return view('salaires.valider-as', compact('demandes'));
    }

    public function store_validation(Request $request)
    {

        $request->validate([
            'as_id' => 'required|exists:avance_salaires,id',
            'decision' => 'required|boolean'
        ]);

        $as = AvanceSalaire::findOrFail($request->as_id);
        $as->flag = $request->decision;
        $as->save();

        return redirect()->back()->with('success', 'Décision enregistrée.');
    }

    public function validation_retrait()
    {
        $as = AvanceSalaire::where('id_employe', Auth::id())
            ->where('flag', true)
            ->where('retrait_valide', false)
            ->first();

        return view('salaires.validation-retrait', compact('as'));
    }

    public function recup_retrait(Request $request)
    {
        $as = AvanceSalaire::findOrFail($request->as_id);
        $as->retrait_demande = true;
        $as->save();

        return redirect()->back()->with('success', 'Demande de retrait enregistrée.');
    }

    public function valider_retraitcp()
    {

        $demandes = AvanceSalaire::with('employe')
            ->where('retrait_demande', true)
            ->where('retrait_valide', false)
            ->get();

        return view('salaires.valider-retrait-cp', compact('demandes'));
    }

    public function recup_retrait_cp(Request $request)
    {

        $as = AvanceSalaire::findOrFail($request->as_id);
        $as->retrait_valide = true;
        $as->save();

        // Mise à jour du salaire
        $user = User::find($as->id_employe);
        $user->avance_salaire += $as->sommeAs;
        $user->save();

        $salaire = Salaire::where('id_employe', $as->id_employe)->first();
        $salaire->somme_effective_mois -= $as->sommeAs;
        $salaire->save();

        return redirect()->back()->with('success', 'Retrait validé avec succès.');
    }

    public function form_salaire()
    {

        $employes = User::all();
        return view('salaires.form', compact('employes'));
    }

    public function store_salaire(Request $request)
    {

        $request->validate([
            'id_employe' => 'required|exists:users,id',
            'somme' => 'required|numeric|min:0'
        ]);

        Salaire::updateOrCreate(
            ['id_employe' => $request->id_employe],
            [
                'somme' => $request->somme,
                'somme_effective_mois' => $request->somme
            ]
        );

        return redirect()->back()->with('success', 'Salaire enregistré avec succès.');
    }
}
