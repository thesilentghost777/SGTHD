<?php

namespace App\Http\Controllers;

use App\Models\Salaire;
use App\Models\AvanceSalaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prime;
use App\Models\ACouper;
use App\Models\Deli;
use App\Models\Evaluation;
use App\Models\DeliUser;
use App\Models\Complexe;
use App\Models\ManquantTemporaire;
use Carbon\Carbon;
use App\Traits\HistorisableActions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;

//au debut du mois on replace tous les compteurs de salaires a 0

class SalaireController extends Controller
{
    use HistorisableActions;
    protected $notificationController;
    protected $messageController;

    public function __construct(NotificationController $notificationController, MessageController $messageController)
    {
        $this->notificationController = $notificationController;
        $this->messageController = $messageController;
    }
    public function reclamerAs()
    {
        $employe = auth()->user();

        if(!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        // Vérification de la date
        if (now()->day < 9) {
            return view('pages.error_as', [
                'error' => 'Vous ne pouvez pas réclamer l\'AS avant le 9 de chaque mois',
                'hasRequest' => false
            ]);
        }
        // Vérification si l'employé a déjà une avance ce mois-ci
        $hasRequest = AvanceSalaire::where('id_employe', $employe->id)
            ->whereMonth('mois_as', now()->month)
            ->where('flag', true)
            ->exists();

        if ($hasRequest) {
            return view('pages.error_as', [
                'error' => 'Vous avez déjà soumis une demande pour ce mois-ci',
                'hasRequest' => true
            ]);
        }

        // Si toutes les conditions sont satisfaites
        $as = new AvanceSalaire();
        return view('salaires.reclamer-as', compact('as'));
    }

    public function store_demandes_AS(Request $request)
    {
        $request->validate([
            'sommeAs' => 'required|numeric|min:0'
        ]);

        $user = Auth::user();
        $salaire = Salaire::where('id_employe', $user->id)->first();

        if (!$salaire) {
            return redirect()->back()->with('error', 'Aucun salaire trouvé.');
        }

        // Vérification que le montant demandé n'est pas supérieur au salaire
        if ($request->sommeAs > $salaire->somme) {
            return redirect()->back()->with('error', 'Le montant demandé ne peut pas être supérieur à votre salaire.');
        }

        // Création ou mise à jour de l'avance sur salaire
        $avanceSalaire = AvanceSalaire::updateOrCreate(
            ['id_employe' => $user->id],
            [
                'sommeAs' => $request->sommeAs,
                'flag' => false,
                'retrait_demande' => false,
                'retrait_valide' => false,
                'mois_as' => now()
            ]
        );

        // Créer un signalement
        $signalementRequest = new Request([
            'message' => "Demande d'avance sur salaire de {$request->sommeAs} par {$user->name}",
            'category' => 'report'
        ]);
        $this->messageController->store_message($signalementRequest);

        // Envoyer une notification au DG
        $dg = User::getDG();
        if ($dg) {
            $notificationRequest = new Request([
                'recipient_id' => $dg->id,
                'subject' => "Nouvelle demande d'avance sur salaire",
                'message' => "L'employé {$user->name} a effectué une demande d'avance sur salaire d'un montant de {$request->sommeAs}. Veuillez examiner cette demande dans les meilleurs délais."
            ]);
            $this->notificationController->send($notificationRequest);
        }

        // Historiser l'action
        $this->historiser("L'utilisateur {$user->name} a demandé une avance sur salaire de {$request->sommeAs}", 'avance_salaire');

        return redirect()->route('voir-status')->with('success', 'Demande d\'avance sur salaire envoyée avec succès.');
    }

    public function voir_Status()
    {
        $as = AvanceSalaire::where('id_employe', Auth::id())
            ->whereMonth('created_at', now()->month)
            ->first();

        return view('salaires.status', compact('as'));
    }

    public function validerAs()
{
    // Récupérer le mois courant
    $currentMonth = now()->month;
    $currentYear = now()->year;

    // Récupérer les demandes en attente
    $demandes = AvanceSalaire::with('employe')
        ->where('flag', false)
        ->get();


    return view('salaires.valider-as', compact(
        'demandes'
    ));
}

public function store_validation(Request $request)
{
    $request->validate([
        'as_id' => 'required|exists:avance_salaires,id',
        'decision' => 'required|boolean'
    ]);

    $as = AvanceSalaire::findOrFail($request->as_id);

    // Si la décision est refusée (0), supprimer l'entrée
    if ($request->decision == 0) {
        // Récupérer l'employé concerné avant de supprimer
        $employe = User::findOrFail($as->id_employe);

        // Préparer le message de notification
        $sujet = "Demande d'avance sur salaire refusée";
        $message = "Votre demande d'avance sur salaire d'un montant de {$as->sommeAs} a été refusée. Veuillez contacter votre responsable pour plus d'informations.";

        // Historiser l'action avant suppression
        $currentUser = auth()->user();
        $this->historiser("L'utilisateur {$currentUser->name} a refusé la demande d'avance sur salaire de {$employe->name}", 'validation_avance');

        // Envoyer la notification à l'employé
        $notificationRequest = new Request([
            'recipient_id' => $as->id_employe,
            'subject' => $sujet,
            'message' => $message
        ]);
        $this->notificationController->send($notificationRequest);

        // Supprimer l'entrée
        $as->delete();
    } else {
        // Si approuvée, mettre à jour le flag
        $as->flag = $request->decision;
        $as->save();

        // Récupérer l'employé concerné
        $employe = User::findOrFail($as->id_employe);

        // Préparer le message de notification
        $sujet = "Demande d'avance sur salaire approuvée";
        $message = "Votre demande d'avance sur salaire d'un montant de {$as->sommeAs} a été approuvée. Le montant sera disponible selon les modalités habituelles.";

        // Historiser l'action
        $currentUser = auth()->user();
        $this->historiser("L'utilisateur {$currentUser->name} a approuvé la demande d'avance sur salaire de {$employe->name}", 'validation_avance');

        // Envoyer la notification à l'employé
        $notificationRequest = new Request([
            'recipient_id' => $as->id_employe,
            'subject' => $sujet,
            'message' => $message
        ]);
        $this->notificationController->send($notificationRequest);
    }

    return redirect()->back()->with('success', 'Décision enregistrée et employé notifié.');
}


    public function validation_retrait()
    {
        $as = AvanceSalaire::where('id_employe', Auth::id())
            ->where('flag', true)
            ->where('retrait_demande', false)
            ->where('retrait_valide', false)
            ->first();

        return view('salaires.validation-retrait', compact('as'));
    }

    public function recup_retrait(Request $request)
    {
        $as = AvanceSalaire::findOrFail($request->as_id);
        $as->retrait_demande = true;
        $as->save();
        $user = User::findOrFail($as->id_employe);
        $user->avance_salaire = $as->sommeAs;

        return redirect()->back()->with('success', 'Demande de retrait enregistrée.');
    }

    public function valider_retraitcp()
    {

        $demandes = AvanceSalaire::with('employe')
            ->where('retrait_demande', true)
            ->where('retrait_valide', false)
            ->get();

        return view('salaires.valider-retrait-cp', compact('demandes'));
    }

    public function recup_retrait_cp(Request $request)
    {

        $as = AvanceSalaire::findOrFail($request->as_id);
        $as->retrait_valide = true;
        $as->save();
        $salaire = Salaire::where('id_employe', $as->id_employe)->first();
        $salaire->flag = false;
        $salaire->retrait_demande = false;
        $salaire->retrait_valide = false;
        $salaire->save();
        //apres le payement de l'as on peut de nouveau reclamer le salaire

        return redirect()->back()->with('success', 'Retrait validé avec succès.');
    }

    public function form_salaire()
    {

        $employes = User::all();
        return view('salaires.form', compact('employes'));
    }

    public function store_salaire(Request $request)
    {

        $request->validate([
            'id_employe' => 'required|exists:users,id',
            'somme' => 'required|numeric|min:0'
        ]);

        Salaire::updateOrCreate(
            ['id_employe' => $request->id_employe],
            [
                'somme' => $request->somme,
                'somme_effective_mois' => $request->somme
            ]
        );

        return redirect()->back()->with('success', 'Salaire enregistré avec succès.');
    }
    /*salaire*/
    public function index()
    {
        $salaires = Salaire::with('employe')->get();
        return view('salaires.index', compact('salaires'));
    }

    public function create()
    {
        $employes = User::whereNotIn('id', function($query) {
            $query->select('id_employe')->from('salaires');
        })->get();

        return view('salaires.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_employe' => 'required|exists:users,id|unique:salaires,id_employe',
            'somme' => 'required|numeric|min:0',
        ]);

        Salaire::create([
            'id_employe' => $request->id_employe,
            'somme' => $request->somme,
        ]);

        $request->merge([
            'recipient_id' => $request->id_employe,
            'subject' => 'Salaire Mis a Jour',
            'message' => 'Bonjour votre salaire a ete mis a jour : '.$request->somme
        ]);

        // Appel de la méthode send
        $this->notificationController->send($request);
        return redirect()->route('salaires.index')
            ->with('success', 'Salaire créé avec succès');
    }

    public function edit(Salaire $salaire)
    {
        return view('salaires.edit', compact('salaire'));
    }

    public function update(Request $request, Salaire $salaire)
    {
        $request->validate([
            'somme' => 'required|numeric|min:0',
        ]);
        $id_employe = $salaire->id_employe;
        $salaire->update([
            'somme' => $request->somme,
        ]);
        $request->merge([
            'recipient_id' => $id_employe,
            'subject' => 'Salaire Mis a Jour',
            'message' => 'Bonjour votre salaire a ete mis a jour. Le nouveau Montant est '.$request->somme
        ]);

        // Appel de la méthode send
        $this->notificationController->send($request);


        return redirect()->route('salaires.index')
            ->with('success', 'Salaire mis à jour avec succès');
    }

    public function destroy(Salaire $salaire)
    {
        $salaire->delete();
        return redirect()->route('salaires.index')->with('success', 'Salaire supprimé avec succès');
    }

    public function fichePaie($id = null)
    {
        $employe = auth()->user();
        if ($id && auth()->user()->role === 'dg') {
            $employe = User::findOrFail($id);
        }

        $salaire = Salaire::where('id_employe', $employe->id)->firstOrFail();
        $mois = Carbon::now();

        // Récupérer les déductions
        $deductions = Acouper::where('id_employe', $employe->id)
                            ->where('date', '<=', now())
                            ->first();

        // Récupérer les incidents (delis)
        $incidents = DeliUser::where('user_id', $employe->id)
                            ->whereMonth('date_incident', $mois->month)
                            ->whereYear('date_incident', $mois->year)
                            ->with('deli')
                            ->get();
        $totalDelis = $incidents->sum(function($incident) {
            return $incident->deli->montant ?? 0;
        });

        // Récupérer l'avance sur salaire
        $avanceSalaire = DB::table('avance_salaires')
                            ->where('id_employe', $employe->id)
                            ->where('retrait_valide', true)
                            ->value('sommeAs') ?? 0;

        // Récupérer les primes
        $primes = Prime::where('id_employe', $employe->id)
                      ->whereMonth('created_at', $mois->month)
                      ->whereYear('created_at', $mois->year)
                      ->get();
        $totalPrimes = $primes->sum('montant');

        // Calculer le salaire net
        $fichePaie = [
            'salaire_base' => $salaire->somme,
            'avance_salaire' => $avanceSalaire,
            'deductions' => [
                'manquants' => $deductions->manquants ?? 0,
                'remboursement' => $deductions->remboursement ?? 0,
                'caisse_sociale' => $deductions->caisse_sociale ?? 0,
                'incidents' => $totalDelis,
            ],
            'primes' => $totalPrimes,
            'salaire_net' => $salaire->somme - $avanceSalaire
                            - ($deductions->manquants ?? 0)
                            - ($deductions->remboursement ?? 0)
                            - ($deductions->caisse_sociale ?? 0)
                            - $totalDelis
                            + $totalPrimes
        ];

        // Liste des incidents pour affichage détaillé
        $listeIncidents = $incidents->map(function($incident) {
            return [
                'date' => Carbon::parse($incident->date_incident)->format('d/m/Y'),
                'description' => $incident->deli->description ?? 'Incident non spécifié',
                'montant' => $incident->deli->montant ?? 0
            ];
        });

        return view('salaires.fiche-paie', compact('employe', 'salaire', 'mois', 'fichePaie', 'listeIncidents'));
    }


    public function demandeRetrait(Request $request, $id)
    {
        $id = auth()->user()->id;
        $salaire = Salaire::where('id_employe', $id)->first();
        $salaire->retrait_demande = true;
        $salaire->save();

        return redirect()->back()->with('success', 'Demande de retrait envoyée avec succès');
    }

    public function consulter_fichePaie()
    {
        $employe = auth()->user();
        /*if ($id && auth()->user()->role === 'admin') {
            $employe = User::findOrFail($id);
        }*/

        $salaire = Salaire::where('id_employe', $employe->id)->firstOrFail();
        $mois = Carbon::now();

        // Récupérer les déductions
        $deductions = Acouper::where('id_employe', $employe->id)
                            ->where('date', '<=', now())
                            ->first();

        // Récupérer les primes
        $primes = Prime::where('id_employe', $employe->id)->get();
        $totalPrimes = $primes->sum('montant');
        $as = DB::table('avance_salaires')
        ->where('id_employe', $employe->id)
        ->where('retrait_valide', true)
        ->value('sommeAs') ?? 0;
        // Calculer le salaire net
         // Récupérer les incidents (delis)
         $incidents = DeliUser::where('user_id', $employe->id)
         ->whereMonth('date_incident', $mois->month)
         ->whereYear('date_incident', $mois->year)
         ->with('deli')
         ->get();
        $totalDelis = $incidents->sum(function($incident) {
            return $incident->deli->montant ?? 0;
        });
        $fichePaie = [
            'salaire_base' => $salaire->somme,
            'avance_salaire' => $as,
            'deductions' => [
                'manquants' => $deductions->manquants ?? 0,
                'caisse_sociale' => $deductions->caisse_sociale ?? 0,
                'remboursement' => $deductions->remboursement ?? 0,
                'incidents' => $totalDelis,
            ],
            'primes' => $totalPrimes,
            'salaire_net' => $salaire->somme - ($as)
                            - ($deductions->manquants ?? 0)
                            - ($deductions->pret ?? 0)
                            - ($deductions->caisse_sociale ?? 0)
                            - $totalDelis
                            + $totalPrimes
        ];

        return view('salaires.fiche-paie2', compact('employe', 'salaire', 'mois', 'fichePaie'));
    }

    public function consulter_fiche_paie(){
        $employe = auth()->user();
        $salaire = Salaire::where('id_employe', $employe->id)->first();
        if ($salaire == null) {
            return redirect()->back()->with('error', 'Votre salaire n\'a pas encore ete defini ou enregistree par l\'administration');
        }
        $mois = Carbon::now();

        // Récupérer les déductions
        $deductions = Acouper::where('id_employe', $employe->id)
                            ->where('date', '<=', now())
                            ->first();

        // Récupérer les primes
        $primes = Prime::where('id_employe', $employe->id)->get();
        $as = DB::table('avance_salaires')
        ->where('id_employe', $employe->id)
        ->where('retrait_valide', true)
        ->value('sommeAs') ?? 0;
        $totalPrimes = $primes->sum('montant');

        // Calculer le salaire net
        $fichePaie = [
            'salaire_base' => $salaire->somme,
            'avance_salaire' => $as,
            'deductions' => [
                'manquants' => $deductions->manquants ?? 0,
                'caisse_sociale' => $deductions->caisse_sociale ?? 0,
                'remboursement' => $deductions->remboursement ?? 0,
            ],
            'primes' => $totalPrimes,
            'salaire_net' => $salaire->somme - ($as)
                            - ($deductions->manquants ?? 0)
                            - ($deductions->pret ?? 0)
                            - ($deductions->caisse_sociale ?? 0)
                            + $totalPrimes
        ];
        return view('salaires.fiche-paie2', compact('employe', 'salaire', 'mois', 'fichePaie'));
    }


    public function validerRetrait($id)
{
    #verifier si l'emplyer dispose de manquanttemporaire
    $manquantTemporaire = ManquantTemporaire::where('employe_id', $id)->first();
    #verifier si le manquant temporaire est valide
    if($manquantTemporaire->valide_par == null){
        $flag = true;
    }else{
        $flag = false;
    }
    if ($flag) {
        return redirect()->back()->with('error', 'Impossible de valider le retrait, l\'employé a des manquants temporaires.Veuillez les traiter d\'abord.');
    }
    return DB::transaction(function () use ($id) {
        #verifier si il y'a encore les manquants temporaire

        $salaire = Salaire::where('id_employe', $id)->first();
        $acouper = ACouper::where('id_employe', $id)->first();
        $avanceSalaire = AvanceSalaire::where('id_employe', $id)->first();
        $user = User::findOrFail($id);
        $complexe = Complexe::first(); // Récupérer le complexe (supposant qu'il n'y en a qu'un seul)

        // Montant du salaire avant réinitialisation
        $montantSalaire = 0;

        // Réinitialisation complète du salaire
        if ($salaire) {
            // Sauvegarde du montant du salaire avant réinitialisation
            $montantSalaire = $salaire->somme;

            // Réinitialisation de tous les flags et statuts
            $salaire->retrait_valide = true;
            $salaire->retrait_demande = true;
            $salaire->flag = true;
            // Si d'autres champs doivent être réinitialisés, ajoutez-les ici
            $salaire->save();
        }

        // Réinitialisation des déductions
        if ($acouper) {
            $acouper->remboursement = 0;
            $acouper->manquants = 0;
            $acouper->save();
        }

        // Réinitialisation de l'avance sur salaire
        if ($avanceSalaire) {
            $avanceSalaire->sommeAs = 0;
            $avanceSalaire->flag = false;
            $avanceSalaire->retrait_demande = false;
            $avanceSalaire->retrait_valide = false;
            $avanceSalaire->save();
        }

        // Réinitialisation des primes
        Prime::where('id_employe', $id)->delete();

        // Mise à jour de la caisse sociale
        if ($complexe) {
            $complexe->valeur_caisse_sociale += $complexe->caisse_sociale;
            $complexe->save();
        }

        //mise a jour de la note evaluation
        $evaluation = Evaluation::where('user_id', $id)->first();
        if ($evaluation) {
            $evaluation->note = 0;
            $evaluation->save();
        }


        $day = Carbon::now()->format('d/m/Y');

        // Envoyer une notification à l'employé
        $notificationRequest = new Request([
            'recipient_id' => $id,
            'subject' => 'Salaire disponible',
            'message' => 'Vous venez de récupérer votre salaire de ' . $montantSalaire . ' en ce jour ' . $day
        ]);
        $this->notificationController->send($notificationRequest);

        // Historiser l'action
        $currentUser = auth()->user();
        $this->historiser("L'utilisateur {$currentUser->name} a validé le retrait du salaire pour {$user->name} et réinitialisé son compte", 'validation_retrait');

        return redirect()->back()->with('success', 'Retrait validé avec succès et compte réinitialisé');
    });
}

    public function generatePDF($id)
    {
        $employe = User::findOrFail($id);
        $mois = Carbon::now();

        //au debut du mois on replace tous les compteurs de salaires a 0

        $pdf = PDF::loadView('salaires.fiche-paie-pdf', compact('employe', 'mois', 'fichePaie'));
        return $pdf->download('fiche-paie-'.$employe->name.'-'.$mois->format('F-Y').'.pdf');
    }
}
