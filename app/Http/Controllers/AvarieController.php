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
use App\Traits\HistorisableActions;
use App\Models\ProduitStock;

class AvarieController extends Controller
{
    use HistorisableActions;

    protected $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    /**
     * Affiche le formulaire pour enregistrer une avarie
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $produits = Produit_fixes::orderBy('nom')->get();
        /*$matieres = Matiere::orderBy('nom')->get();*/
        #retourner toutes les matieres sauf celle commenxant par Taule
        $matieres = Matiere::where('nom', 'not like', 'Taule%')->orderBy('nom')->get();
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

                if ($matiereData['unite'] !== $matiere->unite_minimale) {
                    $tauxConversion = $matiere->unite_minimale::getConversionRate(
                        $matiereData['unite'],
                        $matiere->unite_minimale->toString()
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
                    $utilisation->unite_matiere = $matiere->unite_minimale;
                    $utilisation->save();

                    // Trouver les assignations de cette matière pour ce producteur et retirer la quantité
                    $deduction = $this->deduireQuantiteAssignee(
                        Auth::id(),
                        $matiereData['matiere_id'],
                        $quantiteConvertie
                    );

                    // Si la déduction échoue, s'arrêter
                    if (!$deduction || ($deduction instanceof \Illuminate\Http\RedirectResponse)) {
                        DB::rollBack();
                        return $deduction ?: redirect()->back()
                            ->withInput()
                            ->with('error', 'Erreur lors de la déduction de la quantité assignée pour la matière #'.$matiereData['matiere_id']);
                    }

                    $this->historiser(
                        "Avarie enregistrée : {$validated['quantite_produit']} unités du produit #{$validated['produit']} - LOT: $idLot",
                        'create',
                        $utilisation->id,
                        'avarie'
                    );
                }
            }

            // Mise à jour du stock d'avaries
            $stock = ProduitStock::where('id_produit', $validated['produit'])->first();
            if (!$stock) {
                $stock = ProduitStock::create([
                    'id_produit' => $validated['produit'],
                    'quantite_en_stock' => 0,
                    'quantite_invendu' => 0,
                    'quantite_avarie' => 0
                ]);
            }

            $stock->quantite_avarie += $validated['quantite_produit'];
            $stock->save();

            $this->historiser(
                "Stock d'avaries mis à jour pour le produit #{$validated['produit']} : +{$validated['quantite_produit']} unités",
                'update',
                $stock->id,
                'produit_stock'
            );

            DB::commit();

            return redirect()->back()
                ->with('success', 'Avarie enregistrée avec succès' .
                    ($avarieReutilisee ? ' et marquée pour réutilisation' : ''));

        } catch (\Exception $e) {
            DB::rollBack();

            $this->historiser(
                "Erreur lors de l'enregistrement d'une avarie : {$e->getMessage()}",
                'error'
            );

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

     protected function deduireQuantiteAssignee($producteurId, $matiereId, $quantite)
{
    // Récupérer la matière et son assignation
    $matiere = Matiere::find($matiereId);
    if (!$matiere) {
        return redirect()->back()
            ->with('error', 'Matière introuvable');
    }

    $assignation = AssignationMatiere::where('producteur_id', $producteurId)
        ->where('matiere_id', $matiereId)
        ->first();

    if (!$assignation) {
        return redirect()->back()
            ->with('error', 'Matière non assignée : ' . $matiere->nom);
    }

    // Vérifier si la quantité disponible est suffisante
    $assignation->quantite_restante -= $quantite;
    if ($assignation->quantite_restante < 0) {
        return redirect()->back()
            ->with('error', 'Quantité assignée insuffisante pour la matière : ' . $matiere->nom);
    }

    // Enregistrer les modifications
    $assignation->save();

    // Historiser la modification
    $this->historiser(
        "Quantité assignée réduite pour matière #{$matiereId} : -{$quantite} {$matiere->unite_classique}",
        'update',
        $assignation->id,
        'assignation_matiere'
    );

    return true;
}
}