<?php

namespace App\Http\Controllers;

use App\Models\SoldeCP;
use App\Models\HistoriqueSoldeCP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\HistorisableActions;


class SoldeCPController extends Controller
{
    use HistorisableActions;

    public function index()
    {
        $nom = auth()->user()->name;
        $role = auth()->user()->role;
        $solde = SoldeCP::getSoldeActuel();
        $historique = HistoriqueSoldeCP::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('solde-cp.index', compact('solde', 'historique', 'nom', 'role'));
    }

    public function ajuster()
    {
        $nom = auth()->user()->name;
        $role = auth()->user()->role;
        $solde = SoldeCP::getSoldeActuel();
        return view('solde-cp.ajuster', compact('solde', 'nom', 'role'));
    }

    public function storeAjustement(Request $request)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric',
            'operation' => 'required|in:ajouter,soustraire,fixer',
            'description' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            $user = auth()->user();
            $solde = SoldeCP::getSoldeActuel();
            $montantInitial = $solde->montant;
            $nouveauMontant = $montantInitial;
            if ($validated['montant'] < 0) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Soyons serieux dans ce que nous faisons Le montant ne peut etre negatif')
                    ->withInput();
            }
            $montantOperation = abs($validated['montant']);
            $typeOperation = 'ajustement';

            // Calculer le nouveau solde
            switch ($validated['operation']) {
                case 'ajouter':
                    $nouveauMontant += $montantOperation;
                    $this->historiser("L'utilisateur {$user->name} a ajouter  {$montantOperation} au solde cp", 'modify_solde_cp');

                    break;

                case 'soustraire':
                    if ($montantInitial < $montantOperation) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'Le montant à soustraire est supérieur au solde actuel.')
                            ->withInput();
                    }
                    $nouveauMontant -= $montantOperation;
                    $this->historiser("L'utilisateur {$user->name} a soustrait  {$montantOperation} au solde cp", 'modify_solde_cp');

                    break;

                case 'fixer':
                    $nouveauMontant = $montantOperation;
                    $this->historiser("L'utilisateur {$user->name} a ajuster le solde cp a {$montantOperation}", 'modify_solde_cp');
                    break;
            }

            // Mettre à jour le solde

            $solde->montant = $nouveauMontant;
            $solde->derniere_mise_a_jour = now();
            $solde->description = $validated['description'];
            $solde->save();

            // Enregistrer l'historique
            HistoriqueSoldeCP::create([
                'montant' => $validated['montant'],
                'type_operation' => $typeOperation,
                'operation_id' => null,
                'solde_avant' => $montantInitial,
                'solde_apres' => $nouveauMontant,
                'user_id' => auth()->id(),
                'description' => $validated['description']
            ]);
            DB::commit();

            return redirect()->route('solde-cp.index')
                ->with('success', 'Ajustement du solde effectué avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Une erreur est survenue: ' . $e->getMessage())
                ->withInput();
        }
    }
}
