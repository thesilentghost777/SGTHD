<?php

namespace App\Http\Controllers;

use App\Models\Utilisation;
use App\Models\AssignationMatiere;
use App\Models\Produit_fixes;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AvarieController extends Controller
{
    /**
     * Affiche le formulaire pour enregistrer une avarie
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $produits = Produit_fixes::orderBy('nom')->get();
        $matieres = Matiere::orderBy('nom')->get();
        $user = Auth::user();
        $nom = $user->name;
        $secteur = $user->secteur;

        return view('producteur.avaries.create', compact('produits', 'matieres', 'nom', 'secteur'));
    }

    /**
     * Enregistre une nouvelle avarie de production
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produit' => 'required|exists:Produit_fixes,code_produit',
            'quantite_produit' => 'required|numeric|min:1',
            'matieres' => 'required|array|min:1',
            'matieres.*.matiere_id' => 'required|exists:Matiere,id',
            'matieres.*.quantite' => 'required|numeric|min:0.001',
            'matieres.*.unite' => 'required|string',
            'avarie_reutilisee' => 'nullable'
        ]);

        // Générer un ID de lot pour l'avarie
        $idLot = 'AV-' . date('Ymd') . '-' . Str::random(5);

        // Déterminer si l'avarie sera réutilisée
        $avarieReutilisee = $request->has('avarie_reutilisee');

        try {
            DB::beginTransaction();

            // Enregistrer l'utilisation avec quantité négative (pour signaler une avarie)
            // pour chaque matière utilisée dans l'avarie
            foreach ($validated['matieres'] as $matiereData) {
                $matiere = Matiere::findOrFail($matiereData['matiere_id']);


                    // Convertir la quantité si nécessaire selon l'unité minimale de la matière
                    $quantiteConvertie = $matiereData['quantite'];

                    if ($matiereData['unite'] !== $matiere->unite_minimale->value) {
                        $tauxConversion = $matiere->unite_minimale::getConversionRate(
                            $matiereData['unite'],
                            $matiere->unite_minimale->value
                        );
                        $quantiteConvertie = $matiereData['quantite'] * $tauxConversion;
                    }

                if (!$avarieReutilisee) {
                // Créer l'enregistrement d'utilisation avec quantité négative
                $utilisation = new Utilisation();
                $utilisation->id_lot = $idLot;
                $utilisation->produit = $validated['produit'];
                $utilisation->matierep = $matiereData['matiere_id'];
                $utilisation->producteur = Auth::id();
                $utilisation->quantite_produit = 0;
                $utilisation->quantite_matiere = $quantiteConvertie;
                $utilisation->unite_matiere = $matiere->unite_minimale->value;
                $utilisation->save();

                // Si l'avarie n'est pas réutilisée, on retire la quantité des assignations

                    // Convertir la quantité si nécessaire selon l'unité minimale de la matière
                    $quantiteConvertie = $matiereData['quantite'];

                    if ($matiereData['unite'] !== $matiere->unite_minimale->value) {
                        $tauxConversion = $matiere->unite_minimale::getConversionRate(
                            $matiereData['unite'],
                            $matiere->unite_minimale->value
                        );
                        $quantiteConvertie = $matiereData['quantite'] * $tauxConversion;
                    }

                    // Trouver les assignations de cette matière pour ce producteur et retirer la quantité
                    $this->deduireQuantiteAssignee(
                        Auth::id(),
                        $matiereData['matiere_id'],
                        $quantiteConvertie
                    );
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Avarie enregistrée avec succès' .
                    ($avarieReutilisee ? ' et marquée pour réutilisation' : ''));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement de l\'avarie: ' . $e->getMessage());
        }
    }

    /**
     * Liste des avaries enregistrées par le producteur connecté
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer les avaries (utilisations avec quantité négative)
        $avaries = Utilisation::where('producteur', Auth::id())
            ->where('quantite_produit', '<', 0) // Identifier les avaries
            ->with(['produit_fixes', 'matiere'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('id_lot'); // Regrouper par lot

        return view('producteur.avaries.index', compact('avaries'));
    }

    /**
     * Déduit la quantité donnée des assignations de matière pour un producteur
     *
     * @param int $producteurId
     * @param int $matiereId
     * @param float $quantite
     * @return void
     */
    private function deduireQuantiteAssignee($producteurId, $matiereId, $quantite)
    {
        $assignations = AssignationMatiere::where('producteur_id', $producteurId)
            ->where('matiere_id', $matiereId)
            ->where('quantite_restante', '>', 0)
            ->orderBy('created_at', 'asc') // Utiliser d'abord les plus anciennes
            ->get();

        $quantiteRestanteADeduire = $quantite;

        foreach ($assignations as $assignation) {
            if ($quantiteRestanteADeduire <= 0) {
                break;
            }

            $quantiteDisponible = $assignation->quantite_restante;

            if ($quantiteDisponible >= $quantiteRestanteADeduire) {
                // Si l'assignation a suffisamment de quantité, on déduit tout
                $assignation->quantite_restante -= $quantiteRestanteADeduire;
                $quantiteRestanteADeduire = 0;
            } else {
                // Sinon, on prend tout ce qui est disponible
                $assignation->quantite_restante = 0;
                $quantiteRestanteADeduire -= $quantiteDisponible;
            }

            $assignation->save();
        }

        // Si après avoir parcouru toutes les assignations, il reste de la quantité à déduire,
        // c'est que le producteur n'a pas assez de matière assignée
        if ($quantiteRestanteADeduire > 0) {
            throw new \Exception("Quantité insuffisante de matière assignée pour ce producteur");
        }
    }
}