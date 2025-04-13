<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\MatiereComplexe;
use App\Models\FactureComplexeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MatiereComplexeController extends Controller
{
    public function index()
    {
        $matieres = Matiere::with('complexe')->get();
        return view('matieres.complexe.index', compact('matieres'));
    }

    public function toggle($id)
    {
        $matiere = Matiere::findOrFail($id);

        if ($matiere->provientDuComplexe()) {
            // Si la matière existe déjà dans le complexe, la supprimer
            $matiere->complexe()->delete();
            $message = "La matière a été retirée du complexe.";
        } else {
            // Sinon, l'ajouter au complexe
            MatiereComplexe::create([
                'matiere_id' => $matiere->id,
                'prix_complexe' => null
            ]);
            $message = "La matière a été ajoutée au complexe.";
        }

        return redirect()->route('matieres.complexe.index')
            ->with('success', $message);
    }

    public function updatePrix(Request $request, $id)
    {
        $request->validate([
            'prix_complexe' => 'nullable|numeric|min:0'
        ]);

        $matiereComplexe = MatiereComplexe::where('matiere_id', $id)->first();

        if (!$matiereComplexe) {
            // Si la matière n'est pas encore dans le complexe, la créer
            MatiereComplexe::create([
                'matiere_id' => $id,
                'prix_complexe' => $request->prix_complexe
            ]);
        } else {
            $matiereComplexe->update([
                'prix_complexe' => $request->prix_complexe
            ]);
        }

        return redirect()->route('matieres.complexe.index')
            ->with('success', 'Prix mis à jour avec succès.');
    }

    public function statistiques(Request $request)
    {
        // Période par défaut (jour)
        $periode = $request->input('periode', 'jour');

        // Dates de début et fin en fonction de la période
        $dateDebut = null;
        $dateFin = Carbon::now();

        switch ($periode) {
            case 'jour':
                $dateDebut = Carbon::today();
                break;
            case 'semaine':
                $dateDebut = Carbon::now()->startOfWeek();
                break;
            case 'mois':
                $dateDebut = Carbon::now()->startOfMonth();
                break;
            default:
                $dateDebut = Carbon::today();
        }

        // Récupérer les statistiques des matières du complexe
        $statistiques = FactureComplexeDetail::select(
                'facture_complexe_details.matiere_id',
                'Matiere.nom',
                'Matiere.unite_minimale',
                DB::raw('SUM(facture_complexe_details.quantite) as quantite_totale'),
                DB::raw('SUM(facture_complexe_details.montant) as montant_total')
            )
            ->join('factures_complexe', 'facture_complexe_details.facture_id', '=', 'factures_complexe.id')
            ->join('Matiere', 'facture_complexe_details.matiere_id', '=', 'Matiere.id')
            ->join('matiere_complexe', 'Matiere.id', '=', 'matiere_complexe.matiere_id')
            ->where('factures_complexe.statut', '=', 'validee')
            ->whereBetween('factures_complexe.date_creation', [$dateDebut, $dateFin])
            ->groupBy('facture_complexe_details.matiere_id', 'Matiere.nom', 'Matiere.unite_minimale')
            ->orderBy('quantite_totale', 'desc')
            ->get();

        // Calculer les totaux
        $montantTotal = $statistiques->sum('montant_total');

        // Préparer les données pour le graphique circulaire
        $dataGraphique = [];
        foreach ($statistiques as $stat) {
            $dataGraphique[$stat->nom] = $stat->montant_total;
        }

        // Préparer les données pour le graphique d'évolution temporelle
        $evolutionTemporelle = FactureComplexeDetail::select(
                DB::raw('DATE(factures_complexe.date_creation) as date'),
                DB::raw('SUM(facture_complexe_details.montant) as value')
            )
            ->join('factures_complexe', 'facture_complexe_details.facture_id', '=', 'factures_complexe.id')
            ->join('Matiere', 'facture_complexe_details.matiere_id', '=', 'Matiere.id')
            ->join('matiere_complexe', 'Matiere.id', '=', 'matiere_complexe.matiere_id')
            ->where('factures_complexe.statut', '=', 'validee')
            ->whereBetween('factures_complexe.date_creation', [$dateDebut, $dateFin])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        return view('matieres.complexe.statistiques', compact(
            'statistiques',
            'periode',
            'dateDebut',
            'dateFin',
            'montantTotal',
            'dataGraphique',
            'evolutionTemporelle'
        ));
    }
}
