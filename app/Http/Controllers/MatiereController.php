<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matiere;
use Illuminate\Support\Facades\Log;
use Exception;

class MatiereController extends Controller
{

    /**
     * Obtenir les prix des matières premières en fonction des quantités
     */
    public function getPrix(Request $request)
    {
        try {
            // Récupérer les quantités depuis la requête
            $quantiteFarine = $request->input('farine', 0);
            $quantiteEau = $request->input('eau', 0);
            $quantiteHuile = $request->input('huile', 0);

            // Valider les données
            if (!is_numeric($quantiteFarine) || !is_numeric($quantiteEau) || !is_numeric($quantiteHuile)) {
                Log::warning('Requête getPrix avec données non numériques', [
                    'farine' => $quantiteFarine,
                    'eau' => $quantiteEau,
                    'huile' => $quantiteHuile
                ]);
                return response()->json([
                    'error' => 'Les quantités doivent être numériques',
                    'details' => [
                        'farine' => $quantiteFarine,
                        'eau' => $quantiteEau,
                        'huile' => $quantiteHuile
                    ]
                ], 400);
            }

            // Récupérer les prix unitaires des matières depuis la base de données
            $farine = Matiere::where('nom', 'like', '%farine%')->first();
            $eau = Matiere::where('nom', 'like', '%eau%')->first();
            $huile = Matiere::where('nom', 'like', '%huile%')->first();

            // Calculer les prix totaux
            $prixFarine = $farine ? $farine->prix_par_unite_minimale * $quantiteFarine : $quantiteFarine * 500;
            $prixEau = $eau ? $eau->prix_par_unite_minimale * $quantiteEau : $quantiteEau * 50;
            $prixHuile = $huile ? $huile->prix_par_unite_minimale * $quantiteHuile : $quantiteHuile * 1200;

            // Retourner les résultats
            return response()->json([
                'prix_farine' => $prixFarine,
                'prix_eau' => $prixEau,
                'prix_huile' => $prixHuile,
                'prix_total' => $prixFarine + $prixEau + $prixHuile
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors du calcul des prix des matières', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors du calcul des prix',
                'message' => $e->getMessage(),
                'details' => env('APP_DEBUG') ? $e->getTraceAsString() : 'Pour plus de détails, consultez les logs du serveur'
            ], 500);
        }
    }
}
