<?php

namespace App\Http\Controllers;

use App\Models\VersementChef;
use App\Models\SoldeCP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Traits\HistorisableActions;

class VersementChefController extends Controller
{
    use HistorisableActions;

    public function __construct(NotificationController $notificationController, MessageController $messageController)
    {
        $this->notificationController = $notificationController;
        $this->messageController = $messageController;
    }

    public function index()
    {
        $nom = auth()->user()->name;
        $role = auth()->user()->role;
        $versements = VersementChef::with('chefProduction')
            ->where('chef_production', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $total_non_valide = $versements->where('status', 0)->sum('montant');
        $total_valide = $versements->where('status', 1)->sum('montant');

        return view('versements.index', compact('versements', 'total_non_valide', 'total_valide', 'nom','role'));
    }

    public function create()
    {
        $role = auth()->user()->role;

        $nom = auth()->user()->name;
    // Get versements for the authenticated chef de production
    $versements = VersementChef::with('chefProduction')
        ->where('chef_production', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();

    // Calculate totals
    $total_non_valide = $versements->where('status', 0)->sum('montant');
    $total_valide = $versements->where('status', 1)->sum('montant');

    // Get today's VersementCSG records
    $versements_csg_today = DB::table('Versement_csg')
        ->join('users', 'Versement_csg.verseur', '=', 'users.id')
        ->whereDate('Versement_csg.date', now()->toDateString())
        ->select('users.name', 'users.role', 'Versement_csg.somme')
        ->get();

    // Calculate total amount received today
    $total_today = $versements_csg_today->sum('somme');

    return view('versements.create2', compact(
        'versements',
        'total_non_valide',
        'total_valide',
        'versements_csg_today',
        'total_today',
        'nom',
        'role'
    ));
}

public function sendDailyTotal()
{
    $today_total = DB::table('Versement_csg')
        ->whereDate('date', now()->toDateString())
        ->sum('somme');

    // Create a new versement record
    VersementChef::create([
        'chef_production' => Auth::id(),
        'montant' => $today_total,
        'date' => now(),
        'libelle' => 'Versement total journalier - ' . now()->format('d/m/Y'),
        'status' => 0
    ]);

    return redirect()->route('versements.index')
        ->with('success', 'Le montant total a été envoyé avec succès.');
}


public function store(Request $request)
{
    $validated = $request->validate([
        'libelle' => 'required|string|max:255',
        'montant' => 'required|numeric|min:0.01',
        'date' => 'required|date',
    ]);

    // Get the current CP balance
    $soldeCp = SoldeCp::first();

    /*if (!$soldeCp) {
        return redirect()->route('versements.index')
            ->with('error', 'Impossible de trouver le solde CP');
    }*/

    // Ensure amount is positive
    $montant = abs($validated['montant']);

    // Create new versement
    $versement = new VersementChef();
    $versement->chef_production = Auth::id();
    $versement->libelle = $validated['libelle'];
    $versement->montant = $montant;
    $versement->status = 0; // Pending by default
    $versement->date = $validated['date'];
    $versement->save();

    // Compare amount with CP balance
    $user = Auth::user();
    if($user->role = 'chef_production'){
        $this->comparerEtNotifierDifference($soldeCp, $montant, $versement->id);
        #mettre le solde cp a 0
        $soldeCp->montant = 0;
    }
    // Log the action
    $this->historiser("Un versement de {$montant} a été créé par {$user->name}", 'create_versement');

    return redirect()->route('versements.index')
        ->with('success', 'Versement enregistré avec succès');
}


    public function edit(VersementChef $versement)
    {
        if ($versement->status == 1) {
            return redirect()->route('versements.index')
                ->with('error', 'Impossible de modifier un versement validé');
        }

        if ($versement->chef_production !== Auth::id()) {
            return redirect()->route('versements.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce versement');
        }

        return view('versements.edit', compact('versement'));
    }

    public function update(Request $request, VersementChef $versement)
    {
        if ($versement->status == 1) {
            return redirect()->route('versements.index')
                ->with('error', 'Impossible de modifier un versement validé');
        }

        if ($versement->chef_production !== Auth::id()) {
            return redirect()->route('versements.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce versement');
        }

        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0.01'
        ]);

        // Ensure amount is positive
        $montant = abs($validated['montant']);
        $validated['montant'] = $montant;

        // Store old value for history
        $oldMontant = $versement->montant;

        // Update versement
        $versement->update($validated);

        // Get current CP balance
        $soldeCp = SoldeCp::first();


        // Log the action
        $user = Auth::user();
        $this->historiser("Le versement #{$versement->id} a été modifié par {$user->name} (Ancien montant: {$oldMontant}, Nouveau montant: {$montant})", 'update_versement');

        return redirect()->route('versements.index')
            ->with('success', 'Versement mis à jour avec succès');
    }

    public function destroy(VersementChef $versement)
    {
        if ($versement->status == 1) {
            return redirect()->route('versements.index')
                ->with('error', 'Impossible de supprimer un versement validé');
        }

        if ($versement->chef_production !== Auth::id()) {
            return redirect()->route('versements.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer ce versement');
        }

        $versement->delete();

        return redirect()->route('versements.index')
            ->with('success', 'Versement supprimé avec succès');
    }

    // Pour le DG
    public function validation()
    {
        $versements = VersementChef::with('chefProduction')
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $total_en_attente = $versements->sum('montant');

        return view('versements.validation', compact('versements', 'total_en_attente'));
    }

    public function valider(VersementChef $versement)
    {
        if ($versement->status == 1) {
            return redirect()->route('versements.validation')
                ->with('error', 'Ce versement a déjà été validé');
        }

        // Compare amount with CP balance one last time before validation
        $montant = $versement->montant;
        $soldeCp = SoldeCp::first();
        // Mark versement as validated
        $versement->status = 1;
        $versement->save();

        // Reset CP balance to 0
        $soldeCp->montant = 0;
        $soldeCp->derniere_mise_a_jour = now();
        $soldeCp->description = "Solde remis à zéro après validation du versement #{$versement->id}";
        $soldeCp->save();

        // Notify CP to define objectives for next day
        $this->notifierDefinitionObjectifs($versement->chef_production);

        // Log the action
        $user = Auth::user();
        $this->historiser("Le versement #{$versement->id} de {$montant} a été validé par {$user->name} et le solde CP a été remis à zéro", 'validate');

        return redirect()->back()
            ->with('success', 'Versement validé avec succès et solde CP remis à zéro');
    }
    private function comparerEtNotifierDifference($soldeCp, $montant, $versementId)
    {
        // Compare the versement amount with the CP balance
        $difference = abs($soldeCp->montant - $montant);
        $tolerance = 0.01; // Small tolerance for floating point comparison

        if ($difference > $tolerance) {
            // There's a discrepancy
            if ($montant > $soldeCp->montant) {
                $message = "Le montant du versement #{$versementId} ({$montant}) est supérieur au solde CP ({$soldeCp->montant}). Possible erreur de saisie.";
            } else {
                $message = "Le montant du versement #{$versementId} ({$montant}) est inférieur au solde CP ({$soldeCp->montant}). Possible détournement de fonds.";
            }

            // Send alert to DG
            $signalementRequest = new Request([
                'message' => $message,
                'category' => 'report'
            ]);
            $this->messageController->store_message($signalementRequest);

            // Send notification to CP
            $cpId = Auth::id();
            $notificationRequest = new Request([
                'recipient_id' => $cpId,
                'subject' => 'Anomalie détectée dans votre versement',
                'message' => "Nous avons détecté une différence entre le montant de votre versement #{$versementId} ({$montant}) et le solde CP actuel ({$soldeCp->montant}). Veuillez vérifier et contacter la direction si nécessaire."
            ]);
            $this->notificationController->send($notificationRequest);

            // Log the anomaly
            $this->historiser($message, 'anomaly');
        }
    }

    /**
     * Notify CP to define objectives for the next day
     */
    private function notifierDefinitionObjectifs($cpId)
    {
        $notificationRequest = new Request([
            'recipient_id' => $cpId,
            'subject' => 'Définition des objectifs pour la prochaine journée',
            'message' => "Votre versement a été validé et le solde CP a été remis à zéro. Veuillez définir les objectifs et les attentes pour la prochaine journée de production."
        ]);
        $this->notificationController->send($notificationRequest);
    }
}
