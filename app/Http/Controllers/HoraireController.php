<?php

namespace App\Http\Controllers;

use App\Models\Horaire;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\HistorisableActions;
use App\Http\Controllers\NotificationController;

class HoraireController extends Controller
{
    use HistorisableActions;

    protected $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    public function index()
    {
        $horaires = Horaire::where('employe', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $serverTime = now()->format('Y-m-d H:i:s');
        return view('pages.horaire.index', compact('horaires', 'serverTime'));
    }

    public function marquerArrivee()
    {
        $user = auth()->user();

        // Vérifier s'il n'y a pas déjà une entrée non terminée
        $horaireExistant = Horaire::where('employe', $user->id)
            ->whereNull('depart')
            ->first();

        if ($horaireExistant) {
            return redirect()->back()->with('error', 'Vous avez déjà marqué votre arrivée');
        }

        // Créer une nouvelle entrée avec seulement l'heure d'arrivée
        $horaire = Horaire::create([
            'employe' => $user->id,
            'arrive' => now(),
            'depart' => null
        ]);

        // Historisation de l'action
        $this->historiser("L'utilisateur {$user->name} a marqué son arrivée à {$horaire->arrive->format('H:i')}", 'arrive');

        return redirect()->back()->with('success', 'Heure d\'arrivée enregistrée');
    }

    public function marquerDepart()
    {
        $user = auth()->user();

        $horaire = Horaire::where('employe', $user->id)
            ->whereNull('depart')
            ->latest()
            ->first();

        if (!$horaire) {
            return redirect()->back()->with('error', 'Aucune entrée d\'arrivée trouvée. Impossible de marquer le départ sans avoir marqué l\'arrivée.');
        }

        $horaire->update(['depart' => now()]);

        // Historisation de l'action
        $this->historiser("L'utilisateur {$user->name} a marqué son départ à {$horaire->depart->format('H:i')}", 'depart');

        return redirect()->back()->with('success', 'Heure de départ enregistrée');
    }

    public function enregistrerHoraire(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'arrive' => 'required|date_format:H:i',
            'depart' => 'required|date_format:H:i|after:arrive'
        ]);

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

        return redirect()->back()->with('success', 'Horaires enregistrés avec succès');
    }

    /**
     * Vérifier les horaires manquants et envoyer des notifications
     * Cette méthode doit être appelée par un job planifié
     */
    public function verifierHoraireManquant()
    {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        // Trouver tous les employés actifs
        $employes = User::where('active', true)->get();

        foreach ($employes as $employe) {
            // Vérifier si c'est un jour de repos pour l'employé
            // À adapter selon votre logique de jours de repos
            $estJourDeRepos = false; // Remplacer par votre logique

            if (!$estJourDeRepos) {
                // Vérifier si l'employé a déjà marqué son arrivée aujourd'hui
                $arriveeAujourdhui = Horaire::where('employe', $employe->id)
                    ->whereDate('arrive', $today)
                    ->exists();

                if (!$arriveeAujourdhui) {
                    // Si c'est le début de journée (avant midi), envoyer un rappel à l'employé
                    if ($now->hour < 12) {
                        $this->envoyerNotificationRappelArrivee($employe->id);
                    }
                    // Si c'est la fin de journée (après 17h), notifier le chef de projet et l'employé
                    elseif ($now->hour >= 17) {
                        $this->envoyerNotificationAbsenceNonMarquee($employe->id);
                    }
                }
            }
        }
    }

    /**
     * Envoyer une notification de rappel à l'employé
     */
    private function envoyerNotificationRappelArrivee($id_employe)
    {
        $request = new Request();
        $request->merge([
            'recipient_id' => $id_employe,
            'subject' => 'Rappel: Marquage d\'arrivée',
            'message' => 'Bonjour, nous vous rappelons de bien vouloir marquer votre arrivée dans le système d\'horaires. Merci de votre attention.'
        ]);

        $this->notificationController->send($request);
    }

    /**
     * Envoyer une notification au chef de projet et à l'employé pour absence non marquée
     */
    private function envoyerNotificationAbsenceNonMarquee($id_employe)
    {
        // Obtenir l'employé
        $employe = User::findOrFail($id_employe);

        // Envoyer à l'employé
        $requestEmploye = new Request();
        $requestEmploye->merge([
            'recipient_id' => $id_employe,
            'subject' => 'Absence de marquage d\'arrivée',
            'message' => 'Nous avons constaté que vous n\'avez pas marqué votre arrivée aujourd\'hui. Veuillez régulariser votre situation dès que possible ou contacter votre responsable si nécessaire.'
        ]);
        $this->notificationController->send($requestEmploye);

        // Trouver le chef de projet de l'employé et lui envoyer une notification
        // Adaptation nécessaire selon votre modèle de données
        $chefProjetId = $this->obtenirChefProjetId($employe);

        if ($chefProjetId) {
            $requestCP = new Request();
            $requestCP->merge([
                'recipient_id' => $chefProjetId,
                'subject' => 'Absence de marquage d\'arrivée d\'un employé',
                'message' => "L'employé {$employe->name} n'a pas marqué son arrivée aujourd'hui. Aucune entrée n'a été enregistrée dans le système d'horaires."
            ]);
            $this->notificationController->send($requestCP);
        }

        // Historiser l'événement
        $this->historiser("Notification envoyée pour absence de marquage d'arrivée de l'employé {$employe->name}", 'notification_absence');
    }

    /**
     * Obtenir l'ID du chef de projet d'un employé
     * À adapter selon votre modèle de données
     */
    /**
 * Obtenir l'ID du chef de projet d'un employé
 * Le chef de production est le user avec role='chef_production'
 */
private function obtenirChefProjetId($employe)
{
    // Rechercher l'utilisateur avec le rôle 'chef_production'
    $chefProduction = User::where('role', 'chef_production')->first();

    // Retourner l'ID du chef de production s'il existe, sinon null
    return $chefProduction ? $chefProduction->id : null;
}
    }