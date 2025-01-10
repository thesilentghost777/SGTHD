<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Matiere;
use App\Models\AssignationMatiere;
use App\Services\UniteConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignationMatiereController extends Controller
{
    protected $uniteConversionService;

    public function __construct(UniteConversionService $uniteConversionService)
    {
        $this->uniteConversionService = $uniteConversionService;
    }

    public function index()
    {
        $assignations = AssignationMatiere::where('producteur_id', Auth::id())
            ->whereNot('quantite_restante', 0)
            ->with(['matiere'])
            ->get();

        return view('pages.producteur.mes_assignations', compact('assignations'));
    }

    public function create()
    {
        $producteurs = User::whereIn('role', ['patissier', 'boulanger'])->get();
        $matieres = Matiere::all();
        $assignations = AssignationMatiere::with(['producteur', 'matiere'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pages.chef_production.assigner_matiere', compact('producteurs', 'matieres','assignations'));
    }

    public function storeassignation(Request $request)
    {
        $request->validate([
            'producteur_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:Matiere,id',
            'quantite_assignee' => 'required|numeric|min:0.001',
            'unite_assignee' => 'required|string',
            'date_limite_utilisation' => 'required|date'
        ]);

        // Vérifier le stock disponible
        $matiere = Matiere::findOrFail($request->matiere_id);
        if ($matiere->quantite*$matiere->quantite_par_unite < $request->quantite_assignee) {
            return back()->withErrors(['message' => 'Stock insuffisant pour cette assignation.']);
        }

        DB::beginTransaction();
        try {
            // Créer l'assignation
            AssignationMatiere::create([
                'producteur_id' => $request->producteur_id,
                'matiere_id' => $request->matiere_id,
                'quantite_assignee' => $request->quantite_assignee,
                'quantite_restante' => $request->quantite_assignee,
                'unite_assignee' => $request->unite_assignee,
                'date_limite_utilisation' => $request->date_limite_utilisation
            ]);

            // Mettre à jour le stock
            /*converti dabord en unite classique*/ //revoir cette section pour que la convertion reussisse
            $qte_converti = $this->uniteConversionService->convertir(
                $request->quantite_assignee,
                $request->unite_assignee,
                $matiere->unite_classique
            );
            // Mettre à jour le stock
            $qte_representer = $qte_converti / $matiere->quantite_par_unite;
            $matiere->quantite -= $qte_representer ;
            $matiere->save();

            DB::commit();
            return redirect()->back()->with('success', 'Assignation créée avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['message' => 'Une erreur est survenue lors de l\'assignation.']);
        }
    }

    public function update(Request $request, AssignationMatiere $assignation)
    {
        $request->validate([
            'quantite_assignee' => 'required|numeric|min:0.001',
            'unite_assignee' => 'required|string',
            'date_limite_utilisation' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            // Calculer la différence de quantité
            $difference = $request->quantite_assignee - $assignation->quantite_assignee;


            // Vérifier le stock si on augmente la quantité
            if ($difference > 0) {
                $matiere = Matiere::findOrFail($assignation->matiere_id);
                if ($matiere->quantite < $difference) {
                    return back()->withErrors(['message' => 'Stock insuffisant pour cette modification.']);
                }
                $matiere->quantite -= $difference;
                $matiere->save();
            } elseif ($difference < 0) {
                // Remettre la différence en stock
                $matiere = Matiere::findOrFail($assignation->matiere_id);
                $matiere->quantite += abs($difference);
                $matiere->save();
            }

            // Mettre à jour l'assignation
            $assignation->update([
                'quantite_assignee' => $request->quantite_assignee,
                'quantite_restante' => $request->quantite_assignee,
                'unite_assignee' => $request->unite_assignee,
                'date_limite_utilisation' => $request->date_limite_utilisation
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Assignation mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['message' => 'Une erreur est survenue lors de la mise à jour.']);
        }
    }

    public function destroy(AssignationMatiere $assignation)
    {
        DB::beginTransaction();
        try {
            // Remettre la quantité en stock
            $matiere = Matiere::findOrFail($assignation->matiere_id);
            $matiere->quantite += $assignation->quantite_restante;
            $matiere->save();

            // Supprimer l'assignation
            $assignation->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Assignation supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['message' => 'Une erreur est survenue lors de la suppression.']);
        }
    }

}
