<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use App\Models\ACouper;
use App\Models\Announcement;
use App\Models\AvanceSalaire;
use App\Models\Deli;
use App\Models\DeliUser;
use App\Models\Horaire;
use App\Models\Message;
use App\Models\Prime;
use App\Models\Ration;
use App\Models\RationClaim;
use App\Models\EmployeeRation;
use App\Models\ReposConge;
use App\Models\Salaire;
use App\Models\User;
use App\Traits\HistorisableActions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployeeApiController extends Controller
{
    use HistorisableActions;

    /**
     * Affiche les manquants de l'employé
     */
    public function showManquants()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $info = User::where('id', $employe->id)->first();
        $manquants = ACouper::where('id_employe', $employe->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->first();

        return response()->json([
            'manquants' => $manquants ? $manquants->manquants : 0,
            'nom' => $info->name,
            'secteur' => $info->secteur
        ]);
    }

    /**
     * Affiche les primes de l'employé
     */
    public function getPrimes()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $primes = Prime::where('id_employe', $employe->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        $totalPrimes = $primes->sum('montant');

        return response()->json([
            'primes' => $primes,
            'totalPrimes' => $totalPrimes,
            'hasPrimes' => $primes->count() > 0
        ]);
    }

    /**
     * Affiche les horaires de l'employé
     */
    public function getHoraires()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise grand'], 401);
        }

        $horaires = Horaire::where('employe', $employe->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $serverTime = now()->format('Y-m-d H:i:s');

        // Vérifier si l'employé a déjà marqué son arrivée aujourd'hui
        $arriveeMarquee = Horaire::where('employe', $employe->id)
            ->whereNull('depart')
            ->whereDate('arrive', now()->toDateString())
            ->exists();

        return response()->json([
            'horaires' => $horaires,
            'serverTime' => $serverTime,
            'arriveeMarquee' => $arriveeMarquee
        ]);
    }

    /**
     * Marque l'arrivée de l'employé
     */
    public function marquerArrivee()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        // Vérifier s'il n'y a pas déjà une entrée non terminée
        $horaireExistant = Horaire::where('employe', $user->id)
            ->whereNull('depart')
            ->first();

        if ($horaireExistant) {
            return response()->json([
                'error' => 'Vous avez déjà marqué votre arrivée'
            ], 400);
        }

        // Créer une nouvelle entrée avec seulement l'heure d'arrivée
        $horaire = Horaire::create([
            'employe' => $user->id,
            'arrive' => now(),
            'depart' => null
        ]);

        // Historisation de l'action
        $this->historiser("L'utilisateur {$user->name} a marqué son arrivée à {$horaire->arrive->format('H:i')}", 'arrive');

        return response()->json([
            'success' => 'Heure d\'arrivée enregistrée',
            'horaire' => $horaire
        ]);
    }

    /**
     * Marque le départ de l'employé
     */
    public function marquerDepart()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $horaire = Horaire::where('employe', $user->id)
            ->whereNull('depart')
            ->latest()
            ->first();

        if (!$horaire) {
            return response()->json([
                'error' => 'Aucune entrée d\'arrivée trouvée. Impossible de marquer le départ sans avoir marqué l\'arrivée.'
            ], 400);
        }

        $horaire->update(['depart' => now()]);

        // Historisation de l'action
        $this->historiser("L'utilisateur {$user->name} a marqué son départ à {$horaire->depart->format('H:i')}", 'depart');

        return response()->json([
            'success' => 'Heure de départ enregistrée',
            'horaire' => $horaire
        ]);
    }

    /**
     * Enregistre manuellement les horaires
     */
    public function enregistrerHoraire(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $validator = Validator::make($request->all(), [
            'arrive' => 'required|date_format:H:i',
            'depart' => 'required|date_format:H:i|after:arrive'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $today = Carbon::today();
        $arrive = Carbon::createFromFormat('H:i', $request->arrive)->setDate(
            $today->year,
            $today->month,
            $today->day
        );

        $depart = Carbon::createFromFormat('H:i', $request->depart)->setDate(
            $today->year,
            $today->month,
            $today->day
        );

        $horaire = Horaire::create([
            'employe' => $user->id,
            'arrive' => $arrive,
            'depart' => $depart
        ]);

        // Historisation de l'action
        $this->historiser("L'utilisateur {$user->name} a enregistré manuellement ses horaires: arrivée à {$horaire->arrive->format('H:i')}, départ à {$horaire->depart->format('H:i')}", 'enregistrement_manuel');

        return response()->json([
            'success' => 'Horaires enregistrés avec succès',
            'horaire' => $horaire
        ]);
    }

    /**
     * Récupère les jours de repos et congés de l'employé
     */
    public function getReposConges()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $reposConges = ReposConge::where('employe_id', $user->id)
            ->get();

        return response()->json([
            'reposConges' => $reposConges,
            'nom' => $user->name,
            'role' => $user->role
        ]);
    }

    /**
     * Récupère la fiche de paie de l'employé
     */
    public function getFichePaie()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $salaire = Salaire::where('id_employe', $employe->id)->first();
        if (!$salaire) {
            return response()->json(['error' => 'Aucun salaire trouvé pour cet employé'], 404);
        }

        $mois = Carbon::now();

        // Récupérer les déductions
        $deductions = ACouper::where('id_employe', $employe->id)
                            ->where('date', '<=', now())
                            ->first();

        // Récupérer les primes
        $primes = Prime::where('id_employe', $employe->id)->get();
        $totalPrimes = $primes->sum('montant');

        $as = DB::table('avance_salaires')
            ->where('id_employe', $employe->id)
            ->where('retrait_valide', true)
            ->value('sommeAs') ?? 0;

        //retirer aussi les delis
         // Récupérer les incidents (delis)
         $incidents = DeliUser::where('user_id', $employe->id)
         ->whereMonth('date_incident', $mois->month)
         ->whereYear('date_incident', $mois->year)
         ->with('deli')
         ->get();
        $totalDelis = $incidents->sum(function($incident) {
            return $incident->deli->montant ?? 0;
        });
        // Calculer le salaire net
        $fichePaie = [
            'salaire_base' => $salaire->somme,
            'avance_salaire' => $as,
            'deductions' => [
                'manquants' => $deductions->manquants ?? 0,
                'caisse_sociale' => $deductions->caisse_sociale ?? 0,
                'remboursement' => $deductions->remboursement ?? 0,
                'delis' => $totalDelis,
            ],
            'primes' => $totalPrimes,
            'salaire_net' => $salaire->somme - $as
                            - ($deductions->manquants ?? 0)
                            - ($deductions->remboursement ?? 0)
                            - ($deductions->caisse_sociale ?? 0)
                            - ($totalDelis ?? 0)
                            + $totalPrimes,
            'retrait_demande' => $salaire->retrait_demande
        ];

        return response()->json([
            'employe' => $employe,
            'salaire' => $salaire,
            'mois' => $mois->format('F Y'),
            'fichePaie' => $fichePaie
        ]);
    }

    /**
     * Demande de retrait du salaire
     */
    public function demandeRetrait()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $salaire = Salaire::where('id_employe', $employe->id)->first();
        if (!$salaire) {
            return response()->json(['error' => 'Aucun salaire trouvé pour cet employé'], 404);
        }

        $salaire->retrait_demande = true;
        $salaire->save();

        return response()->json([
            'success' => 'Demande de retrait envoyée avec succès',
            'salaire' => $salaire
        ]);
    }

    /**
     * Récupère les informations de prêt de l'employé
     */
    public function getLoanInfo()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $loanData = ACouper::where('id_employe', $employe->id)->first();

        if (!$loanData) {
            $loanData = ACouper::create([
                'id_employe' => $employe->id,
                'pret' => 0,
                'remboursement' => 0,
                'date' => Carbon::now()
            ]);
        }

        return response()->json([
            'loanData' => $loanData
        ]);
    }

    /**
     * Demande de prêt
     */
    public function requestLoan(Request $request)
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $validator = Validator::make($request->all(), [
            'montant' => 'required|numeric|min:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loanData = ACouper::where('id_employe', $employe->id)->first();

        if ($loanData && $loanData->pret > 0) {
            return response()->json([
                'error' => 'Vous avez déjà un prêt en cours.'
            ], 400);
        }

        // Création d'une demande de prêt en attente
        $loanRequest = DB::table('loan_requests')->insert([
            'user_id' => $employe->id,
            'amount' => $request->montant,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => 'Votre demande de prêt a été soumise et est en attente d\'approbation.'
        ]);
    }

    /**
     * Récupère les informations de ration de l'employé
     */
    public function getRationInfo()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $today = Carbon::today();

        // Vérifier si l'employé est présent aujourd'hui
        $horaire = Horaire::where('employe', $employe->id)
            ->whereDate('arrive', $today)
            ->whereNotNull('arrive')
            ->first();

        $canClaimRation = $horaire !== null;

        // Vérifier si l'employé a déjà réclamé sa ration aujourd'hui
        $alreadyClaimed = RationClaim::where('employee_id', $employe->id)
            ->whereDate('date_reclamation', $today)
            ->exists();

        // Obtenir la ration de l'employé
        $ration = EmployeeRation::where('employee_id', $employe->id)->first();
        $montantRation = $ration ? $ration->montant : 0;

        if (!$ration) {
            // Si aucune ration spécifique n'est définie, utiliser la ration par défaut
            $rationDefaut = Ration::first();
            $montantRation = $rationDefaut ? $rationDefaut->montant_defaut : 0;
        }

        // Historique des réclamations
        $historique = RationClaim::where('employee_id', $employe->id)
            ->orderBy('date_reclamation', 'desc')
            ->take(30)
            ->get();

        return response()->json([
            'employee' => $employe,
            'canClaimRation' => $canClaimRation,
            'alreadyClaimed' => $alreadyClaimed,
            'montantRation' => $montantRation,
            'historique' => $historique
        ]);
    }

    /**
     * Réclamer une ration
     */
    public function claimRation()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $today = Carbon::today();

        // Vérifier si l'employé est présent aujourd'hui
        $horaire = Horaire::where('employe', $employe->id)
            ->whereDate('arrive', $today)
            ->whereNotNull('arrive')
            ->first();

        if (!$horaire) {
            return response()->json([
                'error' => 'Vous devez avoir marqué votre arrivée pour réclamer votre ration'
            ], 400);
        }

        // Vérifier si l'employé a déjà réclamé sa ration aujourd'hui
        $alreadyClaimed = RationClaim::where('employee_id', $employe->id)
            ->whereDate('date_reclamation', $today)
            ->exists();

        if ($alreadyClaimed) {
            return response()->json([
                'error' => 'Vous avez déjà réclamé votre ration aujourd\'hui'
            ], 400);
        }

        // Obtenir le montant de la ration
        $ration = EmployeeRation::where('employee_id', $employe->id)->first();
        $montantRation = 0;

        if ($ration) {
            $montantRation = $ration->montant;
        } else {
            $rationDefaut = Ration::first();
            $montantRation = $rationDefaut ? $rationDefaut->montant_defaut : 0;
        }

        // Créer la réclamation
        $claim = RationClaim::create([
            'employee_id' => $employe->id,
            'date_reclamation' => $today,
            'montant' => $montantRation,
            'heure_reclamation' => now()
        ]);

        $this->historiser("L'employé {$employe->name} a réclamé sa ration du {$today->format('d/m/Y')}", 'claim_ration');

        return response()->json([
            'success' => 'Ration réclamée avec succès',
            'claim' => $claim
        ]);
    }

    /**
     * Récupère les informations d'AS de l'employé
     */
    public function getAvanceSalaireStatus()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $as = AvanceSalaire::where('id_employe', $employe->id)
            ->whereMonth('created_at', now()->month)
            ->first();

        return response()->json([
            'as' => $as
        ]);
    }

    /**
     * Vérifie si l'employé peut demander une avance sur salaire
     */
    public function checkAsEligibility()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        // Vérification de la date
        $isEligible = now()->day >= 9;

        // Vérification si l'employé a déjà une avance ce mois-ci
        $hasRequest = AvanceSalaire::where('id_employe', $employe->id)
            ->whereMonth('mois_as', now()->month)
            ->where('flag', true)
            ->exists();

        return response()->json([
            'isEligible' => $isEligible && !$hasRequest,
            'reason' => !$isEligible ? 'Vous ne pouvez pas réclamer l\'AS avant le 9 de chaque mois' :
                      ($hasRequest ? 'Vous avez déjà soumis une demande pour ce mois-ci' : null)
        ]);
    }

    /**
     * Demande d'avance sur salaire
     */
    public function requestAvanceSalaire(Request $request)
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $validator = Validator::make($request->all(), [
            'sommeAs' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérification de l'éligibilité
        if (now()->day < 9) {
            return response()->json([
                'error' => 'Vous ne pouvez pas réclamer l\'AS avant le 9 de chaque mois'
            ], 400);
        }

        $hasRequest = AvanceSalaire::where('id_employe', $employe->id)
            ->whereMonth('mois_as', now()->month)
            ->where('flag', true)
            ->exists();

        if ($hasRequest) {
            return response()->json([
                'error' => 'Vous avez déjà soumis une demande pour ce mois-ci'
            ], 400);
        }

        $salaire = Salaire::where('id_employe', $employe->id)->first();

        if (!$salaire) {
            return response()->json([
                'error' => 'Aucun salaire trouvé.'
            ], 404);
        }

        // Vérification que le montant demandé n'est pas supérieur au salaire
        if ($request->sommeAs > $salaire->somme) {
            return response()->json([
                'error' => 'Le montant demandé ne peut pas être supérieur à votre salaire.'
            ], 400);
        }

        // Création ou mise à jour de l'avance sur salaire
        $avanceSalaire = AvanceSalaire::updateOrCreate(
            ['id_employe' => $employe->id],
            [
                'sommeAs' => $request->sommeAs,
                'flag' => false,
                'retrait_demande' => false,
                'retrait_valide' => false,
                'mois_as' => now()
            ]
        );

        // Historiser l'action
        $this->historiser("L'utilisateur {$employe->name} a demandé une avance sur salaire de {$request->sommeAs}", 'avance_salaire');

        return response()->json([
            'success' => 'Demande d\'avance sur salaire envoyée avec succès.',
            'avanceSalaire' => $avanceSalaire
        ]);
    }

    /**
     * Demande de retrait d'avance sur salaire
     */
    public function retraitAvanceSalaire(Request $request)
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $validator = Validator::make($request->all(), [
            'as_id' => 'required|exists:avance_salaires,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $as = AvanceSalaire::findOrFail($request->as_id);

        // Vérifier que l'AS appartient à l'utilisateur
        if ($as->id_employe !== $employe->id) {
            return response()->json([
                'error' => 'Cette avance sur salaire ne vous appartient pas.'
            ], 403);
        }

        // Vérifier que l'AS est approuvée mais pas encore demandée pour retrait
        if (!$as->flag || $as->retrait_demande) {
            return response()->json([
                'error' => 'Cette avance sur salaire n\'est pas éligible pour un retrait.'
            ], 400);
        }

        $as->retrait_demande = true;
        $as->save();

        $this->historiser("L'utilisateur {$employe->name} a demandé le retrait de son avance sur salaire de {$as->sommeAs}", 'retrait_as');

        return response()->json([
            'success' => 'Demande de retrait enregistrée.',
            'avanceSalaire' => $as
        ]);
    }

    /**
     * Envoyer un message
     */
    public function sendMessage(Request $request)
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'category' => 'required|string|in:report,complaint-private,suggestion'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $messageData = [
            'message' => $request->message,
            'type' => $request->category,
            'date_message' => now(),
            'name' => $request->category != 'complaint-private' ? $employe->name : 'null'
        ];

        // Créer le message
        $message = Message::create($messageData);

        $this->historiser("L'utilisateur {$employe->name} a créé un message de type {$request->category}", 'create_message');

        return response()->json([
            'success' => 'Message transmis avec succès',
            'message' => $message
        ]);
    }

    /**
     * Récupérer les annonces
     */
    public function getAnnouncements()
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $announcements = Announcement::with(['reactions', 'user'])
            ->latest()
            ->limit(2)
            ->get();

        $isDg = $employe->secteur === 'administration';

        return response()->json([
            'announcements' => $announcements,
            'isDg' => $isDg
        ]);
    }

    /**
     * Réagir à une annonce
     */
    public function reactToAnnouncement(Request $request)
    {
        $employe = auth()->user();
        if (!$employe) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required|exists:announcements,id',
            'comment' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Vérifier si l'annonce existe
        $announcement = Announcement::find($request->announcement_id);
        if (!$announcement) {
            return response()->json(['error' => 'Annonce introuvable'], 404);
        }

        // Créer la réaction
        $reaction = DB::table('reactions')->insert([
            'announcement_id' => $request->announcement_id,
            'user_id' => $employe->id,
            'comment' => $request->comment,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->historiser("L'utilisateur {$employe->name} a réagi à l'annonce #{$request->announcement_id}", 'reaction');

        return response()->json([
            'success' => 'Réaction ajoutée avec succès'
        ]);
    }
}
