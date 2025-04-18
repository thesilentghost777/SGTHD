<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use App\Models\Extra;
use App\Models\Salaire;
use App\Models\ACouper;
use App\Models\Complexe;
use App\Models\Horaire;
use App\Models\EmployeeRation;
use App\Models\Ration;
use App\Models\ManquantTemporaire;
use App\Traits\HistorisableActions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\NotificationController;

class RegisteredUserController extends Controller
{
    use HistorisableActions;
    protected $notificationController;
    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'date_naissance' => ['required', 'date', 'before:today'],
            'code_secret' => ['required', 'integer'],
            'secteur' => [
                'required',
                'string',
            ],
            'role' => [
                'required',
                'string'
            ],
            'num_tel' => [
                'required',
                'regex:/^6[0-9]{8}$/',
                'uniqManquantTemporaireue:users'
            ],
            'annee_debut_service' => [
                'required',
                'integer',
                'min:1950',
                'max:' . date('Y')
            ]
        ]);

    try {
        DB::beginTransaction();

        $user = new User();
        $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->date_naissance = $request->date_naissance;
            $user->code_secret = $request->code_secret;
            $user->secteur = $request->secteur;
            $user->role = $request->role;
            $user->num_tel = $request->num_tel;
            $user->avance_salaire = 0;
            $user->annee_debut_service = $request->annee_debut_service;
            $user->created_at = now();

        $user->save();

        // Utiliser $user comme paramètre au lieu de $user
        if (!$this->validateEmployeeRegulations($user)) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'Vérification des réglementations échouée']);
        }

        $this->createDefaultSalary($user);
        $this->createAcouperEntry($user);
        $this->createDefaultSchedule($user);
        $this->createDefaultRation($user);
        $this->createManquantTemporaireEntry($user);

        $this->historiser("L'utilisateur {$user->name} a été créé avec succès", 'create');

        // Déclencher l'événement Registered
        event(new Registered($user));

        Auth::login($user);
        DB::commit();
        $this->sendAllNotifications($user);

        return redirect(route('dashboard', absolute: true));

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur lors du traitement du nouvel employé: ' . $e->getMessage());
        return redirect()->back()->withErrors(['message' => 'Une erreur est survenue lors de la création de l\'utilisateur']);
    }
    }

    private function validateEmployeeRegulations(User $user)
    {
        $extra = Extra::where('secteur', $user->secteur)->first();

        if (!$extra) {
            Log::error("Pas de règles trouvées pour le secteur: {$user->secteur}");
            return true;
        }

        // Vérification de l'âge
        $age = Carbon::parse($user->date_naissance)->age;
        if ($age < $extra->age_adequat) {
            Log::warning("Employé {$user->name} n'a pas l'âge requis ({$age} ans)");
            $this->sendAgeNotification($user);
            return false;
        }

        return true;
    }

    private function createDefaultSalary(User $user)
    {
        $extra = Extra::where('secteur', $user->secteur)->first();

        Salaire::create([
            'id_employe' => $user->id,
            'somme' => $extra->salaire_adequat,
            'mois_salaire' => now()
        ]);
    }

    private function createAcouperEntry(User $user)
    {
        $complexe = Complexe::first();

        ACouper::create([
            'id_employe' => $user->id,
            'caisse_sociale' => $complexe->caisse_sociale ?? 0,
            'manquants' => 0,
            'remboursement' => 0,
            'pret' => 0,
            'date' => now()
        ]);
    }

    private function createDefaultSchedule(User $user)
    {
        $extra = Extra::where('secteur', $user->secteur)->first();

        Horaire::create([
            'employe' => $user->id,
            'arrive' => Carbon::createFromTimeString('07:00:00'),
            'date' => now()
        ]);
    }

    private function createDefaultRation(User $user)
    {
        $defaultRation = Ration::first();

        EmployeeRation::create([
            'employee_id' => $user->id,
            'montant' => $defaultRation->montant_defaut,
            'personnalise' => false
        ]);
    }

    private function sendAllNotifications(User $user)
    {

        // 2. Notification pour le jour de repos
        $this->sendNotifications($user);

        // 3. Notification pour les chefs de production
        $this->sendCPNotification($user);

        // 4. Notification pour le DG
        $this->sendDGNotification($user);

    }

    private function sendCPNotification(User $user)
    {
        $chefProductions = User::where('role', 'chef_production')->get();

        foreach ($chefProductions as $cp) {
            $request = new Request();
            $request->merge([
                'recipient_id' => $cp->id,
                'subject' => 'Nouvel employé - Actions requises',
                'message' => "Un nouvel employé ({$user->name}) a été ajouté. Veuillez remplir les informations relatives à son avance sur salaire et autres données nécessaires pour sa fiche de paie."
            ]);
            $this->notificationController->send($request);
        }
    }

    private function sendDGNotification(User $user)
    {
        $dg = User::where('role', 'dg')->first();
        if ($dg) {
            $request = new Request();
            $request->merge([
                'recipient_id' => $dg->id,
                'subject' => 'Nouvel employé - Prêt à définir',
                'message' => "Un nouvel employé ({$user->name}) a été ajouté. Veuillez définir s'il dispose d'un prêt à enregistrer dans l'application."
            ]);
            $this->notificationController->send($request);
        }
    }

    private function sendNotifications(User $user)
    {
        $salaire = Salaire::where('id_employe', $user->id)->first();
        $ration = EmployeeRation::where('employee_id', $user->id)->first();

        $message = "Bonjour,\n\n";
        $message .= "Votre salaire a été fixé par défaut à {$salaire->somme} FCFA. Veuillez vous rapprocher de la direction générale pour toute modification éventuelle.\n\n";
        $message .= "Votre jour de repos hebdomadaire n'a pas encore été assigné. Veuillez vous rapprocher du chef de production pour le définir.\n\n";
        $message .= "Votre ration a été fixée par défaut à {$ration->montant} FCFA. Veuillez vous rapprocher de la direction générale pour toute modification éventuelle.\n\n";
        $message .= "Cordialement,\nL'administration.";

        $request = new Request();
        $request->merge([
            'recipient_id' => $user->id,
            'subject' => 'Informations importantes : Salaire, Jour de repos et Ration',
            'message' => $message
    ]);

        $this->notificationController->send($request);

    }

    private function sendAgeNotification(User $user)
    {
        $request = new Request();
        $request->merge([
            'recipient_id' => $user->id,
            'subject' => 'Echec lors de l\'inscription',
            'message' => "Votre âge ne correspond pas aux réglementations de l'entreprise. Veuillez vous rapprocher de la direction générale pour plus d'informations."
        ]);
        $this->notificationController->send($request);
    }

    /*creons une methode qui prends en entrer un user et creer une entrer dans la table manquant temporaire
     Schema::create('manquant_temporaire', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employe_id');
            $table->foreign('employe_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('montant')->default(0);
            $table->text('explication')->nullable();
            $table->enum('statut', ['en_attente', 'ajuste', 'valide'])->default('en_attente');
            $table->text('commentaire_dg')->nullable();
            $table->unsignedBigInteger('valide_par')->nullable();
            $table->foreign('valide_par')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });*/

    private function createManquantTemporaireEntry(User $user)
    {
        ManquantTemporaire::create([
            'employe_id' => $user->id,
            'montant' => 0,
            'explication' => null,
            'statut' => 'en_attente',
            'commentaire_dg' => null,
            'valide_par' => null,
        ]);
    }
}
