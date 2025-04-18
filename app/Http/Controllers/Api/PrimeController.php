<?php

// app/Http/Controllers/API/PrimeController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Prime;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HistorisableActions;
use Carbon\Carbon;

class PrimeController extends Controller
{
    use HistorisableActions;

    public function index()
    {
        $employe = auth()->user();
        $primes = Prime::where('id_employe', $employe->id)
                      ->orderBy('created_at', 'desc')
                      ->get();

        return response()->json($primes);
    }

    public function all()
    {
        $primes = Prime::with('user')
                 ->orderBy('created_at', 'desc')
                 ->get();

        return response()->json($primes);
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

            $this->historiser("L'utilisateur {$user->name} a mis à jour une prime pour {$cible->name} le {$date}", 'update_prime');

            return response()->json($existingPrime, 200);
        } else {
            // Si pas de prime existante, on en crée une nouvelle
            $prime = Prime::create($validated);

            $this->historiser("L'utilisateur {$user->name} a créé une prime pour {$cible->name} le {$date}", 'create_prime');

            return response()->json($prime, 201);
        }
    }
}
