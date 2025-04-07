<?php

namespace App\Http\Controllers;

use App\Models\Prime;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HistorisableActions;
use Carbon\Carbon;
use App\Http\Controllers\NotificationController;


class PrimeController extends Controller
{

    use HistorisableActions;

    public function __construct(NotificationController $notificationController)
	{
    		$this->notificationController = $notificationController;
	}
    public function index()
    {
        $employe = auth()->user();
        $primes = Prime::where('id_employe', $employe->id)
                      ->orderBy('created_at', 'desc')
                      ->get();

        $totalPrimes = $primes->sum('montant');
        $hasPrimes = $primes->count() > 0;
        return view('pages.mes_primes', compact('primes', 'totalPrimes', 'hasPrimes'));
    }

    public function create()
    {
        $employes = User::whereNotIn('role', ['dg', 'pdg', 'ddg'])
            ->orderBy('name')
            ->get();

        // Récupérer toutes les primes avec les informations des employés
        $primes = Prime::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.dg.attribution-prime', compact('employes', 'primes'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_employe' => 'required|exists:users,id',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0'
        ]);

        $user = auth()->user();
        $cible = User::find($validated['id_employe']);
        $date = Carbon::now();

        // On cherche si l'employé a déjà une prime
        $existingPrime = Prime::where('id_employe', $validated['id_employe'])->first();

        if ($existingPrime) {
            // Si une prime existe déjà, on la met à jour
            $existingPrime->update([
                'libelle' => $validated['libelle'],
                'montant' => $validated['montant']
            ]);

            // On envoie une notification à l'utilisateur concerné
            $request->merge([
                'recipient_id' => $cible->id,
                'subject' => 'Prime mise à jour',
                'message' => 'Felicitations ! Vous avez reçu une prime de ' . $validated['montant'] . ' pour ' . $validated['libelle'],
            ]);

            // Appel de la méthode send
            $this->notificationController->send($request);
            $this->historiser("L'utilisateur {$user->name} a créé une prime pour {$cible->name} le {$date}", 'create_prime');

            return redirect()->back()->with('success', 'Prime mise à jour avec succès');
        } else {
            // Si pas de prime existante, on en crée une nouvelle
            Prime::create($validated);

            $this->historiser("L'utilisateur {$user->name} a créé une prime pour {$cible->name} le {$date}", 'create_prime');

            // On envoie une notification à l'utilisateur concerné
            $request->merge([
                'recipient_id' => $cible->id,
                'subject' => 'Prime attribuée',
                'message' => 'Felicitations ! Vous avez reçu une prime de '. $validated['libelle'],
            ]);

            // Appel de la méthode send
            $this->notificationController->send($request);

            return redirect()->back()->with('success', 'Prime attribuée avec succès');
        }
    }
}
