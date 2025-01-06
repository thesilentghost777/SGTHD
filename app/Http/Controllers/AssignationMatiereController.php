<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Matiere;
use App\Models\AssignationMatiere;
use App\Services\UniteConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->where('utilisee', false)
            ->with(['matiere'])
            ->get();

        return view('pages.producteur.mes_assignations', compact('assignations'));
    }

    public function create()
    {
        $producteurs = User::whereIn('role', ['patissier', 'boulanger'])->get();
        $matieres = Matiere::all();
        return view('pages.chef_production.assigner_matiere', compact('producteurs', 'matieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'producteur_id' => 'required|exists:users,id',
            'matiere_id' => 'required|exists:Matiere,id',
            'quantite_assignee' => 'required|numeric|min:0.001',
            'unite_assignee' => 'required|string',
            'date_limite_utilisation' => 'required|date|after:today'
        ]);

        $matiere = Matiere::findOrFail($validated['matiere_id']);

        // Convertir la quantité assignée en unité minimale
        $quantiteMinimale = $this->uniteConversionService->convertir(
            $validated['quantite_assignee'],
            $validated['unite_assignee'],
            $matiere->unite_minimale
        );

        // Calculer la quantité à déduire en unités classiques
        $quantiteADeduire = $quantiteMinimale / $matiere->quantite_par_unite;

        // Vérifier si la quantité est disponible
        if ($matiere->quantite < $quantiteADeduire) {
            return redirect()->back()->with('error', 'Stock insuffisant');
        }

        // Créer l'assignation
        AssignationMatiere::create($validated);

        // Mettre à jour le stock
        $matiere->quantite -= $quantiteADeduire;
        $matiere->save();

        return redirect()->back()->with('success', 'Matière première assignée avec succès');
    }
}
