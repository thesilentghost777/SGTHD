<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\User;
use App\Models\ReservationMp;
use App\Models\AssignationMatiere;
use App\Services\UniteConversionService;
use App\Traits\HistorisableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NotificationController;

class ReservationMpController extends Controller
{
    use HistorisableActions;

    protected $notificationController;
    protected $conversionService;

    public function __construct(NotificationController $notificationController, UniteConversionService $conversionService)
    {
        $this->notificationController = $notificationController;
        $this->conversionService = $conversionService;
    }
    public function index()
    {
        $employe = auth()->user();
        $nom = $employe->name;
        $role = $employe->role;
        $reservations = ReservationMp::with(['producteur', 'matiere'])
            ->where('statut', 'en_attente')
            ->get();
        $matieres = Matiere::all();

        return view('pages.chef_production.gestion_reservation', compact('reservations','matieres','nom','role'));
    }

    public function create()
    {
        //selectionner les matieres dont le nom ne commence pas par 'Taule'
        $matieres = Matiere::where('nom', 'not like', 'Taule%')->get();
        return view('pages.producteur.reserver-mp', compact('matieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matiere_id' => 'required|exists:Matiere,id',
            'quantite_demandee' => 'required|numeric|min:0.001',
            'unite_demandee' => 'required|string'
        ]);
        //si le delai n'a pas ete defini , le mettre a un jour
        if (!isset($validated['delai'])) {
            $validated['delai'] = now()->addDay();
        }
        //si la date de delai est inferieure a la date actuelle ou la quantite negative , on retourne une erreur
        if ($validated['delai'] < now() || $validated['quantite_demandee'] < 0) {
            return redirect()->back()->with('error', 'Erreur dans la date de delai ou la quantite demandée');
        }
        try {
            DB::beginTransaction();

            $producteurId = Auth::id();
            $matiere = Matiere::findOrFail($validated['matiere_id']);

            // Vérifier si une réservation ou assignation existe déjà pour cette matière aujourd'hui
            $reservationExistante = ReservationMp::where('producteur_id', $producteurId)
                ->where('matiere_id', $matiere->id)
                ->whereDate('created_at', today())
                ->where('statut', 'en_attente')
                ->first();

            $assignationExistante = AssignationMatiere::where('producteur_id', $producteurId)
                ->where('matiere_id', $matiere->id)
                ->whereDate('created_at', today())
                ->first();

            // Vérifier la compatibilité des unités
            [$estCompatible, $messageErreur] = $this->conversionService->verifierCompatibilite(
                $validated['unite_demandee'],
                $matiere->unite_minimale
            );

            if (!$estCompatible) {
                return redirect()->back()->with('error', 'Incompatibilité d\'unités: ' . $messageErreur);
            }

            // Convertir la demande en unité minimale
            $quantiteEnUniteMinimale = $this->conversionService->convertir(
                $validated['quantite_demandee'],
                $validated['unite_demandee'],
                $matiere->unite_minimale
            );

            // Si une réservation existe déjà, mettre à jour la quantité
            if ($reservationExistante) {
                $reservationExistante->quantite_demandee += $quantiteEnUniteMinimale;
                $reservationExistante->save();

                // Historiser la mise à jour
                $this->historiser(
                    "La réservation de {$matiere->nom} a été mise à jour avec une quantité supplémentaire",
                    'update'
                );

                $messageSuccess = 'La quantité a été ajoutée à votre réservation existante';
            } else {
                // Créer une nouvelle réservation
                $reservation = new ReservationMp([
                    'matiere_id' => $validated['matiere_id'],
                    'quantite_demandee' => $quantiteEnUniteMinimale,
                    'unite_demandee' => is_object($matiere->unite_minimale) ? $matiere->unite_minimale->value : $matiere->unite_minimale
                ]);

                $reservation->producteur_id = $producteurId;
                $reservation->save();

                // Historiser la création
                $this->historiser(
                    "Une réservation de {$matiere->nom} a été créée",
                    'create'
                );

                $messageSuccess = 'Demande de réservation envoyée avec succès';
            }

            // Notifier l'administrateur de la réservation
            $administrateurs = User::where('role', 'chef_production')->get();
            foreach ($administrateurs as $admin) {
                $request->merge([
                    'recipient_id' => $admin->id,
                    'subject' => 'Nouvelle demande de réservation',
                    'message' => "L'utilisateur " . Auth::user()->name . " a effectué une demande de réservation pour {$matiere->nom}. Veuillez la traiter dans les plus brefs délais via l'application."
                ]);
                $this->notificationController->send($request);
            }

            DB::commit();
            return redirect()->back()->with('success', $messageSuccess);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    public function validerReservation(Request $request, ReservationMp $reservation)
    {
        try {
            DB::beginTransaction();

            // Vérifier que la réservation est en attente
            if ($reservation->statut !== 'en_attente') {
                return redirect()->back()->with('error', 'Cette réservation a déjà été traitée.');
            }

            // Récupérer la matière concernée
            $matiere = Matiere::findOrFail($reservation->matiere_id);
            $producteur = User::findOrFail($reservation->producteur_id);

            // Vérifier si une assignation existe déjà pour cette matière aujourd'hui
            $assignationExistante = AssignationMatiere::where('producteur_id', $reservation->producteur_id)
                ->where('matiere_id', $matiere->id)
                ->whereDate('created_at', today())
                ->first();

            // La quantité demandée est déjà en unité minimale dans notre DB
            // Convertir cette quantité en unité classique
            list($estCompatible, $messageErreur) = $this->conversionService->verifierCompatibilite(
                $matiere->unite_minimale,
                $matiere->unite_classique
            );

            if (!$estCompatible) {
                return redirect()->back()->with('error', 'Incompatibilité entre unités minimale et classique: ' . $messageErreur);
            }

            $quantiteEnUniteClassique = $this->conversionService->convertir(
                $reservation->quantite_demandee,
                $matiere->unite_minimale,
                $matiere->unite_classique
            );

            // Calculer le nombre d'unités (sacs, boîtes) nécessaires
            $nombreUnites = $quantiteEnUniteClassique / $matiere->quantite_par_unite;

            // Vérifier si le stock est suffisant
            if ($matiere->quantite < $nombreUnites) {
                return redirect()->back()->with('error',
                    'Stock insuffisant. Demande: ' . $nombreUnites . ' unités, ' .
                    'Disponible: ' . $matiere->quantite . ' unités');
            }

            // Déduire la quantité du stock (nombre d'unités)
            $matiere->quantite -= $nombreUnites;
            $matiere->save();

            // Traiter l'assignation (nouvelle ou mise à jour)
            if ($assignationExistante) {
                // Mettre à jour l'assignation existante
                $assignationExistante->quantite_assignee += $reservation->quantite_demandee;
                $assignationExistante->quantite_restante += $reservation->quantite_demandee;
                $assignationExistante->date_limite_utilisation = now()->addDay(); // Limite d'utilisation de 1 jour
                $assignationExistante->save();

                $this->historiser(
                    "L'assignation existante pour {$matiere->nom} a été mise à jour avec une quantité supplémentaire",
                    'update'
                );
            } else {
                // Créer une nouvelle assignation
                $assignation = new AssignationMatiere();
                $assignation->producteur_id = $reservation->producteur_id;
                $assignation->matiere_id = $reservation->matiere_id;
                $assignation->quantite_assignee = $reservation->quantite_demandee;
                $assignation->unite_assignee = is_object($matiere->unite_minimale) ? $matiere->unite_minimale->value : $matiere->unite_minimale;
                $assignation->quantite_restante = $reservation->quantite_demandee;
                $assignation->date_limite_utilisation = now()->addDay(); // Limite d'utilisation de 1 jour
                $assignation->save();

                $this->historiser(
                    "Une nouvelle assignation pour {$matiere->nom} a été créée suite à la validation d'une réservation",
                    'create'
                );
            }

            // Mettre à jour le statut de la réservation
            $reservation->statut = 'approuvee';
            $reservation->save();

            // Notifier le producteur
            $request->merge([
                'recipient_id' => $reservation->producteur_id,
                'subject' => 'Votre réservation a été approuvée',
                'message' => "Votre demande de réservation pour {$matiere->nom} a été approuvée. La matière est disponible pour utilisation jusqu'au " . now()->addDay()->format('d/m/Y') . "."
            ]);
            $this->notificationController->send($request);

            $this->historiser(
                "La réservation de {$matiere->nom} par {$producteur->name} a été approuvée",
                'validate'
            );

            DB::commit();
            return redirect()->back()->with('success',
                'Réservation approuvée avec succès. ' . $nombreUnites . ' unités ' .
                'ont été déduites du stock.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'approbation: ' . $e->getMessage());
        }
    }

    public function refuserReservation(Request $request, ReservationMp $reservation)
    {
        try {
            DB::beginTransaction();

            // Vérifier que la réservation est en attente
            if ($reservation->statut !== 'en_attente') {
                return redirect()->back()->with('error', 'Cette réservation a déjà été traitée.');
            }

            $matiere = Matiere::findOrFail($reservation->matiere_id);
            $producteur = User::findOrFail($reservation->producteur_id);

            // Mettre à jour le statut et ajouter un commentaire
            $reservation->statut = 'refusee';
            $reservation->commentaire = $request->commentaire;
            $reservation->save();

            // Notifier le producteur
            $request->merge([
                'recipient_id' => $reservation->producteur_id,
                'subject' => 'Votre réservation a été refusée',
                'message' => "Votre demande de réservation pour {$matiere->nom} a été refusée. Motif: {$request->commentaire}"
            ]);
            $this->notificationController->send($request);

            $this->historiser(
                "La réservation de {$matiere->nom} par {$producteur->name} a été refusée",
                'reject'
            );

            DB::commit();
            return redirect()->back()->with('success', 'Réservation refusée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors du refus: ' . $e->getMessage());
        }
    }
}
