<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\ReservationMp;
use App\Services\UniteConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationMpController extends Controller
{
    protected $uniteConversionService;

    public function __construct(UniteConversionService $uniteConversionService)
    {
        $this->uniteConversionService = $uniteConversionService;
    }

    public function index()
    {
        $reservations = ReservationMp::with(['producteur', 'matiere'])
            ->where('statut', 'en_attente')
            ->get();
        $matieres = Matiere::all();

        return view('pages.chef_production.gestion_reservation', compact('reservations','matieres'));
    }

    public function create()
    {
        $matieres = Matiere::all();
        return view('pages.producteur.reserver-mp', compact('matieres'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matiere_id' => 'required|exists:Matiere,id',
            'quantite_demandee' => 'required|numeric|min:0.001',
            'unite_demandee' => 'required|string'
        ]);

        $reservation = new ReservationMp($validated);
        $reservation->producteur_id = Auth::id();
        $reservation->save();

        return redirect()->back()->with('success', 'Demande de réservation envoyée avec succès');
    }

    public function validerReservation(Request $request, ReservationMp $reservation)
    {
        $matiere = $reservation->matiere;

       $quantiteMinimale = $this->uniteConversionService->convertir(
            $reservation->quantite_demandee,
            $reservation->unite_demandee,
            $matiere->unite_minimale
        );

        // Calculer la quantité à déduire en unités classiques
        /*$quantiteADeduire = $quantiteMinimale / $matiere->quantite_par_unite;*/
        $quantiteADeduire = $quantiteMinimale/($matiere->quantite_par_unite*1000);


        // Vérifier si la quantité est disponible
        if ($matiere->quantite < $quantiteADeduire) {
            return redirect()->back()->with('error', 'Stock insuffisant');
        }

        // Mettre à jour le stock
        $matiere->quantite -= $quantiteADeduire;
        $matiere->save();

        // Approuver la réservation
        $reservation->statut = 'approuvee';
        $reservation->save();

        return redirect()->back()->with('success', 'Réservation approuvée avec succès');
    }

    public function refuserReservation(Request $request, ReservationMp $reservation)
    {
        $validated = $request->validate([
            'commentaire' => 'required|string|max:255'
        ]);

        $reservation->statut = 'refusee';
        $reservation->commentaire = $validated['commentaire'];
        $reservation->save();

        return redirect()->back()->with('success', 'Réservation refusée');
    }
}
