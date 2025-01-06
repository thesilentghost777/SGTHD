<?php

namespace App\Http\Controllers;

use App\Models\Horaire;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HoraireController extends Controller
{
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
        // Vérifier s'il n'y a pas déjà une entrée non terminée
        $horaireExistant = Horaire::where('employe', auth()->id())
            ->whereNull('depart')
            ->first();

        if ($horaireExistant) {
            return redirect()->back()->with('error', 'Vous avez déjà marqué votre arrivée');
        }

        // Créer une nouvelle entrée avec seulement l'heure d'arrivée
        Horaire::create([
            'employe' => auth()->id(),
            'arrive' => now(),
            'depart' => null
        ]);

        return redirect()->back()->with('success', 'Heure d\'arrivée enregistrée');
    }

    public function marquerDepart()
    {
        $horaire = Horaire::where('employe', auth()->id())
            ->whereNull('depart')
            ->latest()
            ->first();

        if (!$horaire) {
            return redirect()->back()->with('error', 'Aucune entrée d\'arrivée trouvée');
        }

        $horaire->update(['depart' => now()]);
        return redirect()->back()->with('success', 'Heure de départ enregistrée');
    }

    public function enregistrerHoraire(Request $request)
    {
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

        Horaire::create([
            'employe' => auth()->id(),
            'arrive' => $arrive,
            'depart' => $depart
        ]);

        return redirect()->back()->with('success', 'Horaires enregistrés avec succès');
    }
}
