<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit_fixes;
use App\Models\Matiere;
use App\Models\MatiereRecommander;
use App\Enums\UniteMinimale;
use App\Enums\UniteClassique;
use App\Http\Requests\MatierePremRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Chef_productionController extends Controller
{

    public function gestionProduits()
    {
        $produits = Produit_fixes::orderBy('created_at', 'desc')->paginate(10);
        return view('pages.chef_production.gestion_produits', compact('produits'));
    }

    public function storeProduit(Request $request)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:50',
                'prix' => 'required|numeric|min:0',
                'categorie' => 'required|string|in:boulangerie,patisserie'
            ]);

            DB::beginTransaction();

            $produit = Produit_fixes::create($validated);

            DB::commit();

            return redirect()->back()->with('success', 'Produit ajouté avec succès');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Erreur de validation: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'ajout du produit: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Erreur lors de l\'ajout du produit: ' . $e->getMessage()])
                ->withInput();
        }
    }
    public function updateProduit(Request $request, $code_produit)
    {
        try {
            $validated = $request->validate([
                'nom' => 'required|string|max:50',
                'prix' => 'required|numeric|min:0',
                'categorie' => 'required|string|in:boulangerie,patisserie'
            ]);

            DB::beginTransaction();

            $produit = Produit_fixes::where('code_produit', $code_produit)->firstOrFail();
            $produit->update($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Produit mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyProduit($code_produit)
    {
        try {
            DB::beginTransaction();

            $produit = Produit_fixes::where('code_produit', $code_produit)->firstOrFail();

            // Vérifier les relations
            if ($produit->utilisations()->exists() ||
                DB::table('Commande')->where('produit', $code_produit)->exists()) {
                throw new \Exception(
                    "Impossible de supprimer le produit « {$produit->nom} » car il est actuellement " .
                    "utilisé dans des commandes ou des productions en cours. " .
                    "Veuillez d'abord supprimer toutes les références à ce produit avant de le supprimer."
                );
            }

            $produit->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Produit supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }


    // Gestion des Matières Premières
    public function gestionMatieres()
    {
        $matieres = Matiere::orderBy('created_at', 'desc')->paginate(10);
        $unites_minimales = UniteMinimale::values();
        $unites_classiques = UniteClassique::values();

        return view('pages.chef_production.gestion_matieres', compact('matieres', 'unites_minimales', 'unites_classiques'));
    }

    public function storeMatiere(MatierePremRequest $request)
    {
        try {
            DB::beginTransaction();
            // Validation supplémentaire des unités compatibles
            $unites_permises = UniteMinimale::getUniteClassiquePermise($request->unite_minimale);
            if (!in_array($request->unite_classique, $unites_permises)) {
                return redirect()->back()->withErrors(['error' => 'Combinaison d\'unités invalide']);
            }
            Matiere::create($request->validated());
            DB::commit();
            return redirect()->back()->with('success', 'Matière première ajoutée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()]);
        }
    }

    public function editMatiere($id)
{
    try {
        $matiere = Matiere::findOrFail($id);
        return response()->json($matiere);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Matière première non trouvée'], 404);
    }
}

public function updateMatiere(MatierePremRequest $request, Matiere $matiere)
{
    try {
        DB::beginTransaction();

        // Validation supplémentaire des unités compatibles
        $unites_permises = UniteMinimale::getUniteClassiquePermise($request->unite_minimale);
        if (!in_array($request->unite_classique, $unites_permises)) {
            return redirect()->back()->withErrors(['error' => 'Combinaison d\'unités invalide']);
        }

        $matiere->update($request->validated());
        DB::commit();

        return redirect()->back()->with('success', 'Matière première mise à jour avec succès');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()]);
    }
}

    public function destroyMatiere(Matiere $matiere)
    {
        try {
            DB::beginTransaction();
            $matiere->delete();
            DB::commit();

            return redirect()->back()->with('success', 'Matière première supprimée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }
    public function dashboard() {
        return view('pages.chef_production.chef_production_dashboard');
    }
}
