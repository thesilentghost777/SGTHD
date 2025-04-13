<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Matiere;
use App\Models\SoldeCP;
use App\Models\HistoriqueSoldeCP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HistorisableActions;


class DepenseController extends Controller
{
    use HistorisableActions;

    public function index()
    {
        $nom = auth()->user()->name;
        $role = auth()->user()->role;
        $depenses = Depense::with(['user', 'matiere'])->latest('date')->get();
        return view('depenses.index', compact('depenses','nom','role'));
    }

    public function index2()
    {
        $nom = auth()->user()->name;
        $role = auth()->user()->role;
        $depenses = Depense::with(['user', 'matiere'])->latest('date')->get()->where('type', 'livraison_matiere');
        return view('depenses.index2', compact('depenses','nom','role'));
    }

    public function create()
    {
        $matieres = Matiere::all();
        $solde = SoldeCP::getSoldeActuel();
        $nom = auth()->user()->name;
        $role = auth()->user()->role;

        return view('depenses.create', compact('matieres', 'solde', 'nom', 'role'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:achat_matiere,livraison_matiere,reparation,autre,depense_fiscale',
            'date' => 'required|date',
            'idm' => 'required_if:type,achat_matiere,livraison_matiere|exists:Matiere,id|nullable',
            'prix' => 'required_if:type,reparation|numeric|nullable',
            'quantite' => 'required_if:type,achat_matiere,livraison_matiere|numeric|nullable'
        ]);

        // Calculer le prix pour achat/livraison de matière
        if (in_array($validated['type'], ['achat_matiere', 'livraison_matiere'])) {
            $matiere = Matiere::findOrFail($validated['idm']);
            $validated['prix'] = $matiere->prix_unitaire * $validated['quantite'];
        }

        // Valider automatiquement sauf pour les livraisons
        $validated['valider'] = $validated['type'] !== 'livraison_matiere';
        $validated['auteur'] = auth()->id();

        try {
            DB::beginTransaction();

            // Vérifier si le solde est suffisant pour les dépenses d'achat ou réparation
            if (in_array($validated['type'], ['achat_matiere', 'reparation','depense_fiscale','autre'])) {
                $soldeActuel = SoldeCP::getSoldeActuel();

                if ($soldeActuel->montant < $validated['prix']) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Solde insuffisant pour effectuer cette dépense.')
                        ->withInput();
                }
                if ($validated['prix'] < 0) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Soyons serieux dans ce que nous faisons Le prix ne peut etre negatif')
                        ->withInput();
                }

                // Créer la dépense
                $depense = Depense::create($validated);

                // Mettre à jour le solde
                HistoriqueSoldeCP::logTransaction(
                    $validated['prix'],
                    'depense',
                    $depense->id,
                    "Dépense de type {$validated['type']} - {$validated['nom']}"
                );
            } else {
                // Créer la dépense sans affecter le solde (livraison)
                $depense = Depense::create($validated);
            }
            $user = auth()->user();
            $this->historiser("L'utilisateur {$user->name} a créé une depense {$validated['nom']} cp de {$validated['prix']} pour une depense de type {$validated['type']} ", 'create_depense_cp');
            DB::commit();

            return redirect()->route('depenses.index')
                ->with('success', 'Dépense enregistrée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Depense $depense)
    {
        $matieres = Matiere::all();
        $nom = auth()->user()->name;
        $role = auth()->user()->role;
        return view('depenses.edit', compact('depense', 'matieres', 'nom', 'role'));
    }

    public function update(Request $request, Depense $depense)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:achat_matiere,livraison_matiere,reparation,depense_fiscale,autre',
            'date' => 'required|date',
            'idm' => 'required_if:type,achat_matiere,livraison_matiere|exists:Matiere,id|nullable',
            'prix' => 'required_if:type,reparation|numeric|nullable',
            'quantite' => 'required_if:type,achat_matiere,livraison_matiere|numeric|nullable'
        ]);

        if (in_array($validated['type'], ['achat_matiere', 'livraison_matiere'])) {
            $matiere = Matiere::findOrFail($validated['idm']);
            $validated['prix'] = $matiere->prix_unitaire * $validated['quantite'];
        }

        try {
            DB::beginTransaction();

            // Si c'était une dépense qui affectait le solde, on récupère l'ancien montant
            $ancienMontant = 0;
            $nouveauMontant = $validated['prix'];
            if ($validated['prix'] < 0) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Soyons serieux dans ce que nous faisons Le prix ne peut etre negatif')
                    ->withInput();
            }

            if (in_array($depense->type, ['achat_matiere', 'reparation'])) {
                $ancienMontant = $depense->prix;
            }

            // Mettre à jour la dépense
            $depense->update($validated);

            // Ajuster le solde si nécessaire (uniquement pour les dépenses d'achat ou réparation)
            if (in_array($validated['type'], ['achat_matiere', 'reparation'])) {
                $difference = $nouveauMontant - $ancienMontant;

                // Si le nouveau montant est plus élevé, vérifier si le solde est suffisant
                if ($difference > 0) {
                    $soldeActuel = SoldeCP::getSoldeActuel();

                    if ($soldeActuel->montant < $difference) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'Solde insuffisant pour effectuer cette modification.')
                            ->withInput();
                    }


                    // Mettre à jour le solde
                    HistoriqueSoldeCP::logTransaction(
                        $difference,
                        'depense',
                        $depense->id,
                        "Ajustement de dépense - {$validated['nom']}"
                    );
                } elseif ($difference < 0) {
                    // Si le nouveau montant est moins élevé, rembourser la différence au solde
                    HistoriqueSoldeCP::logTransaction(
                        abs($difference),
                        'versement',
                        $depense->id,
                        "Remboursement suite à ajustement de dépense - {$validated['nom']}"
                    );
                }
            }

            DB::commit();

            return redirect()->route('depenses.index')
                ->with('success', 'Dépense mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Depense $depense)
    {
        try {
            DB::beginTransaction();

            // Si c'était une dépense qui affectait le solde, on rembourse le montant
            if (in_array($depense->type, ['achat_matiere', 'reparation','depense_fiscale','autre'])) {
                HistoriqueSoldeCP::logTransaction(
                    $depense->prix,
                    'versement',
                    null,
                    "Remboursement suite à suppression de dépense - {$depense->nom}"
                );
            }

            // Supprimer la dépense
            $depense->delete();

            DB::commit();

            return redirect()->route('depenses.index')
                ->with('success', 'Dépense supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    public function validerLivraison(Depense $depense)
    {
        if ($depense->type !== 'livraison_matiere') {
            return back()->with('error', 'Cette dépense n\'est pas une livraison.');
        }

        $depense->update(['valider' => true]);

        return redirect()->route('depenses.index')
            ->with('success', 'Livraison validée avec succès.');
    }
}
