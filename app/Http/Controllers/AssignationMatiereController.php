<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Matiere;
use App\Enums\UniteMinimale;
use Illuminate\Http\Request;
use App\Services\UniteConversionService;
use App\Models\AssignationMatiere;
use App\Models\FactureComplexe;
use App\Models\FactureComplexeDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\NotificationController;
use App\Traits\HistorisableActions;

class AssignationMatiereController extends Controller
{
    use HistorisableActions;

    protected $conversionService;
    protected $notificationController;

    public function __construct(UniteConversionService $conversionService, NotificationController $notificationController)
    {
        $this->conversionService = $conversionService;
        $this->notificationController = $notificationController;
    }


    public function index()
    {
        $assignations = AssignationMatiere::with(['producteur', 'matiere'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('assignations.index', compact('assignations'));
    }

    public function create()
    {
        $producteurs = User::whereIn('role', ['boulanger', 'patissier'])->get();
        //selectionner les matiere  dont le nom ne commence pas par 'Taule'
        $matieres = Matiere::where('nom', 'not like', 'Taule%')
            ->orderBy('nom')
            ->get();
        $unites = $this->conversionService->obtenirConversions();

        return view('assignations.create', compact('producteurs', 'matieres', 'unites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producteur_id' => 'required|exists:users,id',
            'matieres' => 'required|array|min:1',
            'matieres.*.id' => 'required|exists:Matiere,id',
            'matieres.*.quantite' => 'required|numeric|min:0.001',
            'matieres.*.unite' => 'required|string',
        ]);
        //si le delai n'est pas defini , le faire manuellement
        if (!$request->filled('date_limite')) {
            $request->merge(['date_limite' => Carbon::now()->addDay()]);
        }

        $producteurId = $request->producteur_id;
        $dateLimite = $request->filled('date_limite') ? $request->date_limite : Carbon::now()->addDay();
        $authUser = Auth::user();
        $producteur = User::findOrFail($producteurId);

        // Vérifier la compatibilité des unités avant de créer les assignations
        foreach ($request->matieres as $index => $matiereData) {
            $matiere = Matiere::findOrFail($matiereData['id']);
            $uniteAssignee = $matiereData['unite'];
            $uniteMinimale = $matiere->unite_minimale;

            // Vérifier si les unités sont compatibles
            [$estCompatible, $messageErreur] = $this->conversionService->verifierCompatibilite($uniteAssignee, $uniteMinimale);

            if (!$estCompatible) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        "matieres.{$index}.unite" => "Incompatibilité d'unités pour {$matiere->nom}: {$messageErreur}"
                    ]);
            }

            // Vérifier que la quantité est positive
            if ($matiereData['quantite'] <= 0) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        "matieres.{$index}.quantite" => "La quantité doit être positive pour {$matiere->nom}."
                    ]);
            }
        }

        // Démarrer une transaction pour garantir l'intégrité des données
        DB::beginTransaction();

        try {
            // Générer un ID de lot unique pour regrouper toutes les assignations
            $idLot = Str::uuid()->toString();
            $dateCourante = Carbon::now()->toDateString();

            // Variables pour la facture du complexe
            $matieresComplexe = [];
            $montantTotalComplexe = 0;
            $assignationsIds = [];
            $assignationsCreees = [];
            $stockInsuffisant = [];

            // Si toutes les vérifications sont passées, on traite les assignations
            foreach ($request->matieres as $matiereData) {
                $matiere = Matiere::findOrFail($matiereData['id']);

                // Convertir la quantité en unité minimale pour vérifier le stock
                $quantiteAssignee = $matiereData['quantite'];
                $uniteAssignee = $matiereData['unite'];

                // Convertir en unité classique pour vérifier le stock disponible
                list($estCompatible, $messageErreur) = $this->conversionService->verifierCompatibilite(
                    $uniteAssignee,
                    $matiere->unite_classique
                );

                if (!$estCompatible) {
                    throw new \Exception("Incompatibilité entre unités: {$messageErreur}");
                }

                $quantiteEnUniteClassique = $this->conversionService->convertir(
                    $quantiteAssignee,
                    $uniteAssignee,
                    $matiere->unite_classique
                );

                // Calculer le nombre d'unités nécessaires
                $nombreUnites = $quantiteEnUniteClassique / $matiere->quantite_par_unite;

                // Vérifier si une assignation existe déjà pour cette matière et ce producteur
                $assignationExistante = AssignationMatiere::where('producteur_id', $producteurId)
                    ->where('matiere_id', $matiere->id)
                    ->where('date_limite_utilisation', '>=', Carbon::now())
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Si le stock est insuffisant, ajouter à la liste des erreurs
                if ($matiere->quantite < $nombreUnites) {
                    $stockInsuffisant[] = [
                        'nom' => $matiere->nom,
                        'demande' => $nombreUnites,
                        'disponible' => $matiere->quantite
                    ];
                    continue; // Passer à la matière suivante
                }

                // Si une assignation existe, mettre à jour la quantité
                if ($assignationExistante) {
                    // Convertir la nouvelle quantité dans la même unité que l'existante
                    $quantiteConvertie = $this->conversionService->convertir(
                        $quantiteAssignee,
                        $uniteAssignee,
                        $assignationExistante->unite_assignee
                    );

                    // Mettre à jour l'assignation existante
                    $assignationExistante->quantite_assignee += $quantiteConvertie;
                    $assignationExistante->quantite_restante += $quantiteConvertie;
                    $assignationExistante->date_limite_utilisation = $dateLimite;
                    $assignationExistante->save();

                    $assignation = $assignationExistante;
                    $assignationsCreees[] = [
                        'type' => 'mise_a_jour',
                        'matiere' => $matiere->nom,
                        'quantite' => $quantiteAssignee,
                        'unite' => $uniteAssignee,
                        'total' => $assignationExistante->quantite_assignee,
                        'unite_totale' => $assignationExistante->unite_assignee
                    ];
                } else {
                    // Créer une nouvelle assignation
                    $assignation = AssignationMatiere::create([
                        'producteur_id' => $producteurId,
                        'matiere_id' => $matiere->id,
                        'quantite_assignee' => $quantiteAssignee,
                        'unite_assignee' => $uniteAssignee,
                        'quantite_restante' => $quantiteAssignee,
                        'date_limite_utilisation' => $dateLimite,
                    ]);

                    $assignationsCreees[] = [
                        'type' => 'nouvelle',
                        'matiere' => $matiere->nom,
                        'quantite' => $quantiteAssignee,
                        'unite' => $uniteAssignee
                    ];
                }

                $assignationsIds[] = $assignation->id;

                // Réduire le stock de la matière
                $matiere->quantite -= $nombreUnites;
                $matiere->save();


            }

            // Si des erreurs de stock insuffisant, annuler la transaction
            if (!empty($stockInsuffisant)) {
                DB::rollBack();

                $messages = [];
                foreach ($stockInsuffisant as $erreur) {
                    $messages[] = "Stock insuffisant pour {$erreur['nom']}. Demande: {$erreur['demande']} unités, Disponible: {$erreur['disponible']} unités";
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', implode("<br>", $messages));
            }



            // Historiser l'action
            $actionDetails = "Assignation de matières à {$producteur->name} : ";
            foreach ($assignationsCreees as $info) {
                if ($info['type'] === 'nouvelle') {
                    $actionDetails .= "{$info['quantite']} {$info['unite']} de {$info['matiere']}, ";
                } else {
                    $actionDetails .= "{$info['quantite']} {$info['unite']} ajoutés à {$info['matiere']} (total: {$info['total']} {$info['unite_totale']}), ";
                }
            }
            $actionDetails = rtrim($actionDetails, ", ");

            $this->historiser($actionDetails, 'create_assignationmp');

            // Envoyer une notification au producteur
            $request->merge([
                'recipient_id' => $producteurId,
                'subject' => 'Assignation de matières premières',
                'message' => "Vous avez reçu une nouvelle assignation de matières premières. Veuillez consulter votre espace personnel pour plus de détails."
            ]);
            $this->notificationController->send($request);

            // Tout s'est bien passé, valider la transaction
            DB::commit();

            return redirect()->route('assignations.index')
                ->with('success', 'Assignation de matières premières effectuée avec succès.');
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();

            // Journaliser l'erreur
            \Log::error('Erreur lors de l\'assignation de matières: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'assignation des matières: ' . $e->getMessage());
        }
    }

    // ... keep existing code (edit method)

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantite' => 'required|numeric|min:0.001',
            'unite' => 'required|string',
        ]);

        $assignation = AssignationMatiere::findOrFail($id);
        $matiere = $assignation->matiere;

        // Vérifier si les unités sont compatibles
        [$estCompatible, $messageErreur] = $this->conversionService->verifierCompatibilite(
            $request->unite,
            $matiere->unite_minimale
        );

        if (!$estCompatible) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'unite' => "Incompatibilité d'unités: {$messageErreur}"
                ]);
        }

        DB::beginTransaction();

        try {
            $assignation->update([
                'quantite_assignee' => $request->quantite,
                'unite_assignee' => $request->unite,
                'quantite_restante' => $request->quantite,
                'date_limite_utilisation' => $request->filled('date_limite') ? $request->date_limite : null,
            ]);

            // Si la matière provient du complexe et qu'il y a une facture associée, mettre à jour le détail
            if ($matiere->provientDuComplexe()) {
                $detail = FactureComplexeDetail::where('assignation_id', $assignation->id)->first();

                if ($detail) {
                    // Calculer le nouveau montant
                    $quantiteUniteMinimale = $this->conversionService->convertir(
                        $request->quantite,
                        $request->unite,
                        $matiere->unite_minimale
                    );

                    $prix = $matiere->prix_complexe ?: $matiere->prix_par_unite_minimale;
                    $montant = $quantiteUniteMinimale * $prix;

                    // Mettre à jour le détail
                    $detail->update([
                        'quantite' => $request->quantite,
                        'unite' => $request->unite,
                        'prix_unitaire' => $prix,
                        'montant' => $montant,
                    ]);

                    // Mettre à jour le montant total de la facture
                    $facture = $detail->facture;
                    $nouveauTotal = $facture->details->sum('montant');

                    $facture->update(['montant_total' => $nouveauTotal]);
                }
            }

            DB::commit();

            return redirect()->route('assignations.index')
                ->with('success', 'Assignation mise à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Erreur lors de la mise à jour de l\'assignation: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour. Veuillez réessayer.');
        }
    }


    public function edit($id)
    {
        $assignation = AssignationMatiere::with(['producteur', 'matiere'])->findOrFail($id);
        $unites = $this->conversionService->obtenirConversions();

        return view('assignations.edit', compact('assignation', 'unites'));
    }


    public function facture($id)
    {
        $assignation = AssignationMatiere::with(['producteur', 'matiere'])->findOrFail($id);

        // Calculer le prix total
        $matiere = $assignation->matiere;
        $quantiteUniteMinimale = $this->conversionService->convertir(
            $assignation->quantite_assignee,
            $assignation->unite_assignee,
            $matiere->unite_minimale
        );

        $prixTotal = $quantiteUniteMinimale * $matiere->prix_par_unite_minimale;

        return view('assignations.facture', compact('assignation', 'prixTotal'));
    }

    public function mesAssignations()
    {
        $userId = Auth::id();
        $assignations = AssignationMatiere::with('matiere')
            ->where('producteur_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
            // Récupérer également les réservations en cours pour affichage
        $reservations = \App\Models\ReservationMp::with('matiere')
        ->where('producteur_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('assignations.mes-assignations', compact('assignations','reservations'));
    }

    public function resumeQuantites()
    {
        // Récupérer toutes les assignations avec leurs relations
        $assignations = AssignationMatiere::with(['matiere', 'producteur'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Initialiser le tableau pour stocker le résumé par date
        $resumeParDate = [];

        foreach ($assignations as $assignation) {
            // Utiliser la date de création comme clé pour regrouper
            $date = $assignation->created_at->format('Y-m-d');

            // Initialiser l'entrée pour cette matière première si elle n'existe pas encore
            if (!isset($resumeParDate[$date][$assignation->matiere_id])) {
                $resumeParDate[$date][$assignation->matiere_id] = [
                    'matiere' => $assignation->matiere,
                    'quantite_totale' => 0,
                    'unite' => $assignation->matiere->unite_minimale->value,
                    'prix_total' => 0,
                    'details' => []
                ];
            }

            // Convertir la quantité en unité minimale si nécessaire
            $quantiteConvertie = $assignation->quantite_assignee;

            // Si l'unité d'assignation est différente de l'unité minimale, convertir
            if ($assignation->unite_assignee !== $assignation->matiere->unite_minimale->value) {
                // Utiliser l'enum UniteMinimale pour obtenir le taux de conversion
                $tauxConversion = $assignation->matiere->unite_minimale::getConversionRate(
                    $assignation->unite_assignee,
                    $assignation->matiere->unite_minimale->value
                );
                $quantiteConvertie = $assignation->quantite_assignee * $tauxConversion;
            }

            // Calculer le prix pour cette assignation (supposant que le prix est stocké par unité minimale)
            $prix = $quantiteConvertie * $assignation->matiere->prix_par_unite_minimale;

            // Ajouter les détails de cette assignation
            $resumeParDate[$date][$assignation->matiere_id]['details'][] = [
                'producteur' => $assignation->producteur,
                'quantite' => $assignation->quantite_assignee,
                'unite' => $assignation->unite_assignee,
                'quantite_convertie' => $quantiteConvertie,
                'prix' => $prix
            ];

            // Mettre à jour les totaux
            $resumeParDate[$date][$assignation->matiere_id]['quantite_totale'] += $quantiteConvertie;
            $resumeParDate[$date][$assignation->matiere_id]['prix_total'] += $prix;
        }

        // Convertir le tableau associatif en tableau indexé pour chaque date
        foreach ($resumeParDate as $date => $matieres) {
            $resumeParDate[$date] = array_values($matieres);
        }

        // Trier par date (plus récent en premier)
        krsort($resumeParDate);

        return view('assignations.resume-quantites', compact('resumeParDate'));
    }


}
