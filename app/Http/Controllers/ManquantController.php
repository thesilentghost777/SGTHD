<?php

namespace App\Http\Controllers;

use App\Models\ACouper;
use App\Models\ManquantTemporaire;
use App\Models\ProduitRecu1;
use App\Models\Utilisation;
use App\Models\BagAssignment;
use App\Models\BagReception;
use App\Models\BagSale;
use App\Models\Produit_fixes;
use App\Models\TransactionVente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\HistorisableActions;


class ManquantController extends Controller
{
    use HistorisableActions;

    /**
     * Afficher la liste des manquants temporaires pour le DG
     */
    public function index()
    {
        // Récupérer tous les manquants temporaires classés par montant décroissant
        $manquants = ManquantTemporaire::with('employe')
            ->orderBy('montant', 'desc')
            ->get();

        return view('manquants.index', compact('manquants'));
    }

    /**
     * Afficher les manquants de l'employé connecté
     */
    public function mesManquants()
    {
        $employe = Auth::user();
        $manquant = ManquantTemporaire::where('employe_id', $employe->id)->first();

        return view('manquants.mes-manquants', compact('manquant'));
    }

    /**
     * Formulaire d'ajustement pour le DG
     */
    public function ajuster($id)
    {
        $manquant = ManquantTemporaire::with('employe')->findOrFail($id);
        return view('manquants.ajuster', compact('manquant'));
    }

    /**
     * Enregistrer l'ajustement du manquant
     */
    public function sauvegarderAjustement(Request $request, $id)
    {
        $request->validate([
            'montant' => 'required|integer|min:0',
            'commentaire_dg' => 'nullable|string'
        ]);

        $manquant = ManquantTemporaire::findOrFail($id);
        $manquant->montant = $request->montant;
        $manquant->commentaire_dg = $request->commentaire_dg;
        $manquant->statut = 'ajuste';
        $manquant->save();

        return redirect()->route('manquants.index')
            ->with('success', 'Manquant ajusté avec succès');
    }

    /**
     * Valider un manquant et le transférer dans la table ACouper
     */
    public function valider($id)
    {
        $manquant = ManquantTemporaire::findOrFail($id);
        $manquant->statut = 'valide';
        $manquant->valide_par = Auth::id();
        $manquant->save();

        // Vérifier s'il existe déjà une entrée pour cet employé dans ACouper
        $aCouper = ACouper::where('id_employe', $manquant->employe_id)->first();

        if ($aCouper) {
            // Mettre à jour l'entrée existante
            $aCouper->manquants += $manquant->montant;
            $aCouper->date = Carbon::now();
            $aCouper->save();
        } else {
            // Créer une nouvelle entrée
            ACouper::create([
                'id_employe' => $manquant->employe_id,
                'manquants' => $manquant->montant,
                'date' => Carbon::now()
            ]);
        }
        $user = User::find($manquant->employe_id);
        $this->historiser("Le dg vient de confirmer le manquant de  {$user->name} a {$manquant->montant} XAF", 'valider_manquant_temporaire');

        return redirect()->route('manquants.index')
            ->with('success', 'Manquant validé et transféré avec succès');
    }

    /**
     * Calculer les manquants pour tous les employés
     */
    public function calculerTousLesManquants()
    {
        // Récupérer tous les employés par secteur (sauf administration)
        $employes = User::whereNotIn('secteur', ['administration'])
            ->get();

        foreach ($employes as $employe) {
            switch ($employe->role) {
                case 'pointeur':
                    $this->calculerManquantPointeur($employe->id);
                    break;
                case 'producteur':
                    $this->calculerManquantProducteur($employe->id);
                    break;
                case 'serveur':
                    $this->calculerManquantServeur($employe->id);
                    break;
            }
        }

        return redirect()->route('manquants.index')
            ->with('success', 'Calcul des manquants effectué pour tous les employés');
    }

    /**
     * Calculer les manquants pour un pointeur
     * Manquant = quantité produite par producteur - quantité reçue par pointeur
     */
    private function calculerManquantPointeur($pointeurId)
    {
        // Récupérer les quantités totales produites pour chaque produit
        $produitsProduitsTotal = Utilisation::select('produit', DB::raw('SUM(quantite_produit) as total_produit'))
            ->groupBy('produit')
            ->get()
            ->pluck('total_produit', 'produit')
            ->toArray();

        // Récupérer les quantités reçues par le pointeur
        $produitsRecusTotal = ProduitRecu1::where('pointeur_id', $pointeurId)
            ->select('produit_id', DB::raw('SUM(quantite) as total_recu'))
            ->groupBy('produit_id')
            ->get()
            ->pluck('total_recu', 'produit_id')
            ->toArray();

        $montantManquant = 0;
        $explication = [];

        // Parcourir chaque produit et calculer les différences
        foreach ($produitsProduitsTotal as $produitId => $quantiteProduite) {
            $quantiteRecue = $produitsRecusTotal[$produitId] ?? 0;
            $difference = $quantiteProduite - $quantiteRecue;

            if ($difference > 0) {
                // Récupérer le prix du produit
                $produit = Produit_fixes::find($produitId);
                $prixManquant = $difference * $produit->prix;
                $montantManquant += $prixManquant;

                $explication[] = "Produit: {$produit->nom}, Qté produite: {$quantiteProduite}, Qté reçue: {$quantiteRecue}, Différence: {$difference}, Valeur manquante: {$prixManquant}";
            }
        }

        // Mettre à jour ou créer l'entrée dans manquant_temporaire
        $this->mettreAJourManquantTemporaire($pointeurId, $montantManquant, implode("\n", $explication));
    }

    /**
     * Calculer les manquants pour un producteur
     * Manquant = quand coût de production > valeur de la production
     */
    private function calculerManquantProducteur($producteurId)
    {
        // Regrouper les utilisations par lot
        $lots = Utilisation::where('producteur', $producteurId)
            ->select('id_lot')
            ->distinct()
            ->get()
            ->pluck('id_lot')
            ->toArray();

        $montantManquant = 0;
        $explication = [];

        foreach ($lots as $idLot) {
            // Récupérer les détails de la production pour ce lot
            $production = $this->getProductionDetails($idLot);

            // Si le coût est supérieur à la valeur
            if ($production['cout_matieres'] > $production['valeur_production']) {
                $difference = $production['cout_matieres'] - $production['valeur_production'];
                $montantManquant += $difference;

                $explication[] = "Lot: {$idLot}, Produit: {$production['produit']}, Coût production: {$production['cout_matieres']}, Valeur production: {$production['valeur_production']}, Perte: {$difference}";
            }
        }

        // Mettre à jour ou créer l'entrée dans manquant_temporaire
        $this->mettreAJourManquantTemporaire($producteurId, $montantManquant, implode("\n", $explication));
    }

    /**
     * Calculer les manquants pour un serveur
     * Manquant = montant attendu - montant du versement
     * Montant attendu = valeur des produits (reçus - vendus - invendus - avariés)
     */
    private function calculerManquantServeur($serveurId)
    {
        // Récupérer les réceptions de sacs par le serveur
        $receptions = BagReception::whereHas('assignment', function ($query) use ($serveurId) {
            $query->where('user_id', $serveurId);
        })->with(['assignment.bag', 'sales'])->get();

        $montantAttendu = 0;
        $explication = [];

        foreach ($receptions as $reception) {
            $bag = $reception->assignment->bag;
            $quantiteRecue = $reception->quantity_received;

            // Calculer les quantités vendues, invendues et avariées
            $sales = $reception->sales;
            $quantiteVendue = $sales->sum('quantity_sold');
            $quantiteInvendue = $sales->sum('quantity_unsold');
            $quantiteAvariee = 0; // À implémenter si nécessaire

            // Calculer la différence
            $quantiteManquante = $quantiteRecue - $quantiteVendue - $quantiteInvendue - $quantiteAvariee;

            if ($quantiteManquante > 0) {
                // Calculer la valeur des produits manquants
                $valeurManquante = $quantiteManquante * $bag->price_per_unit;
                $montantAttendu += $valeurManquante;

                $explication[] = "Réception #{$reception->id}, Sac: {$bag->name}, Qté reçue: {$quantiteRecue}, " .
                                "Qté vendue: {$quantiteVendue}, Qté invendue: {$quantiteInvendue}, " .
                                "Qté manquante: {$quantiteManquante}, Valeur: {$valeurManquante}";
            }
        }

        // Récupérer le total des versements du serveur
        $totalVersements = DB::table('Versement_csg')
            ->where('verseur', $serveurId)
            ->where('status', 'valide')
            ->sum('somme');

        // Calculer le manquant final
        $montantManquant = max(0, $montantAttendu - $totalVersements);

        if ($montantManquant > 0) {
            $explication[] = "Montant attendu: {$montantAttendu}, Total versements: {$totalVersements}, Manquant: {$montantManquant}";
        }

        // Mettre à jour ou créer l'entrée dans manquant_temporaire
        $this->mettreAJourManquantTemporaire($serveurId, $montantManquant, implode("\n", $explication));
    }

    /**
     * Obtenir les détails d'une production par lot (similaire à produit_par_lot)
     */
    private function getProductionDetails($idLot)
    {
        $utilisations = DB::table('Utilisation')
            ->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')
            ->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')
            ->select(
                'Produit_fixes.nom as nom_produit',
                'Produit_fixes.prix as prix_produit',
                'Utilisation.quantite_produit',
                'Matiere.prix_par_unite_minimale',
                'Utilisation.quantite_matiere'
            )
            ->where('Utilisation.id_lot', $idLot)
            ->get();

        $productionDetails = [
            'produit' => $utilisations->first()->nom_produit,
            'quantite_produit' => $utilisations->first()->quantite_produit,
            'prix_unitaire' => $utilisations->first()->prix_produit,
            'valeur_production' => $utilisations->first()->quantite_produit * $utilisations->first()->prix_produit,
            'cout_matieres' => 0
        ];

        foreach ($utilisations as $utilisation) {
            $productionDetails['cout_matieres'] += $utilisation->quantite_matiere * $utilisation->prix_par_unite_minimale;
        }

        return $productionDetails;
    }

    /**
     * Mettre à jour ou créer une entrée dans manquant_temporaire
     */
    private function mettreAJourManquantTemporaire($employeId, $montant, $explication)
    {
        $manquant = ManquantTemporaire::updateOrCreate(
            ['employe_id' => $employeId],
            [
                'montant' => $montant,
                'explication' => $explication,
                'statut' => 'en_attente',
                'commentaire_dg' => null,
                'valide_par' => null
            ]
        );

        return $manquant;
    }

    /**
     * Méthode pour facturer un manquant à un producteur (action spécifique)
     */
    public function create()
    {
        $producteurs = User::where('role', 'producteur')->get();
        return view('manquants.create', compact('producteurs'));
    }

    /**
     * Enregistrer un manquant facturé
     */
    public function store(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:users,id',
            'montant' => 'required|integer|min:1',
            'explication' => 'required|string'
        ]);

        $this->mettreAJourManquantTemporaire(
            $request->employe_id,
            $request->montant,
            $request->explication
        );

        return redirect()->route('manquants.index')
            ->with('success', 'Manquant facturé avec succès');
    }

    public function details($id)
    {
        $manquant = ManquantTemporaire::with('employe')->findOrFail($id);

        return response()->json([
            'id' => $manquant->id,
            'employe' => [
                'name' => $manquant->employe->name,
                'role' => ucfirst($manquant->employe->role)
            ],
            'montant' => $manquant->montant,
            'explication' => $manquant->explication,
            'statut' => ucfirst(str_replace('_', ' ', $manquant->statut)),
            'commentaire_dg' => $manquant->commentaire_dg,
            'updated_at' => $manquant->updated_at->format('d/m/Y H:i')
        ]);
    }

    public function mesDeductions()
    {
        $employe = Auth::user();
        $deductions = Acouper::where('id_employe', $employe->id)->first();

        return view('manquants.mes-deductions', compact('deductions'));
    }
}
