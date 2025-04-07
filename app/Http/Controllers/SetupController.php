<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complexe;
use App\Models\User;
use App\Models\ACouper;
use App\Models\Configuration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SetupController extends Controller
{
    /**
     * Display the initial setup form for Complexe
     */

     public function index()
    {
        /*// Check if setup has already been completed
        if (Complexe::count() > 0) {
            return redirect()->route('dashboard');
        }/*/

        return "ok";
    }

    public function showSetupForm()
    {
        /*// Check if setup has already been completed
        if (Complexe::count() > 0) {
            return redirect()->route('dashboard');
        }/*/

        return view('setup.create');
    }

    /**
     * Process the initial setup form submission
     */
    public function processSetup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:50',
            'localisation' => 'required|string|max:50',
            'revenu_mensuel' => 'nullable|numeric|min:0',
            'revenu_annuel' => 'nullable|numeric|min:0',
            'solde' => 'nullable|numeric|min:0',
            'caisse_sociale' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier si un complexe avec id_comp = 1 existe déjà
        $complexe = Complexe::find(1);
        $oldCaisseSociale = 0;

        // Si le complexe existe, sauvegarder l'ancienne valeur de caisse_sociale
        if ($complexe) {
            $oldCaisseSociale = $complexe->caisse_sociale;
        } else {
            $complexe = new Complexe();
            $complexe->id_comp = 1;
        }

        // Mettre à jour les attributs du complexe
        $complexe->nom = $request->nom;
        $complexe->localisation = $request->localisation;
        $complexe->revenu_mensuel = $request->revenu_mensuel ?? 0;
        $complexe->revenu_annuel = $request->revenu_annuel ?? 0;
        $complexe->solde = $request->solde ?? 0;
        $complexe->caisse_sociale = $request->caisse_sociale ?? 0;
        $complexe->save();

        // Mettre à jour tous les enregistrements ACouper avec la nouvelle valeur de caisse_sociale
        $this->updateAcouperCaisseSociale($complexe->caisse_sociale);

        // Mettre à jour le flag first_config à true
        $configuration = Configuration::find(1);
        if ($configuration) {
            $configuration->first_config = true;
            $configuration->save();
        }

        return redirect()->route('extras.index')
            ->with('success', 'Configuration initiale réussie! Vous pouvez maintenant configurer les paramètres supplémentaires.');
    }

    /**
     * Display the form to edit complex information
     */
    public function edit()
    {
        $complexe = Complexe::first();

        if (!$complexe) {
            return redirect()->route('setup.create')
                ->with('error', 'Veuillez d\'abord configurer votre complexe.');
        }

        return view('setup.edit', compact('complexe'));
    }

    /**
     * Update the complex information
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:50',
            'localisation' => 'required|string|max:50',
            'revenu_mensuel' => 'nullable|numeric|min:0',
            'revenu_annuel' => 'nullable|numeric|min:0',
            'solde' => 'nullable|numeric|min:0',
            'caisse_sociale' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $complexe = Complexe::first();
        $oldCaisseSociale = $complexe->caisse_sociale;

        $complexe->nom = $request->nom;
        $complexe->localisation = $request->localisation;
        $complexe->revenu_mensuel = $request->revenu_mensuel ?? $complexe->revenu_mensuel;
        $complexe->revenu_annuel = $request->revenu_annuel ?? $complexe->revenu_annuel;
        $complexe->solde = $request->solde ?? $complexe->solde;
        $complexe->caisse_sociale = $request->caisse_sociale ?? $complexe->caisse_sociale;
        $complexe->save();

        // Si la valeur de la caisse sociale a changé, mettre à jour tous les enregistrements ACouper
        if ($oldCaisseSociale != $complexe->caisse_sociale) {
            $this->updateAcouperCaisseSociale($complexe->caisse_sociale);
        }

        return redirect()->route('setup.edit')
            ->with('success', 'Informations du complexe mises à jour avec succès.');
    }

    /**
     * Update caisse_sociale in all ACouper records
     */
    private function updateAcouperCaisseSociale($newValue)
    {
        // Mettre à jour la caisse_sociale dans tous les enregistrements ACouper
        ACouper::query()->update(['caisse_sociale' => $newValue]);
    }
}
