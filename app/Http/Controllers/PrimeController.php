<?php

namespace App\Http\Controllers;

use App\Models\Prime;
use App\Models\User;
use Illuminate\Http\Request;

class PrimeController extends Controller
{
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

        return view('pages.dg.attribution-prime', compact('employes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_employe' => 'required|exists:users,id',
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0'
        ]);

        Prime::create($validated);

        return redirect()->back()->with('success', 'Prime attribuée avec succès');
    }
}
