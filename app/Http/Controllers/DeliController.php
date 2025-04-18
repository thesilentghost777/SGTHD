<?php

namespace App\Http\Controllers;

use App\Models\Deli;
use App\Models\ManquantTemporaire;
use App\Models\User;
use App\Traits\HistorisableActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeliController extends Controller
{
    use HistorisableActions;

    protected $notificationController;

    public function __construct(NotificationController $notificationController)
    {
        $this->notificationController = $notificationController;
    }

    public function index()
    {
        $delis = Deli::with('employes')->get();
        return view('delis.index', compact('delis'));
    }

    public function create()
    {
        $employes = User::all();
        return view('delis.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'montant' => 'required|numeric|min:1', // Montant doit être > 0
            'employes' => 'array',
            'date_incident' => 'required|date'
        ]);

        // Vérification de la date (pas plus vieille qu'hier)
        $dateIncident = Carbon::parse($validated['date_incident']);
        $dateLimit = Carbon::now()->subDay();

        if ($dateIncident->lt($dateLimit)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'La date de l\'incident ne peut pas être antérieure à hier.');
        }

        try {
            DB::beginTransaction();

            // Création du délit
            $deli = Deli::create([
                'nom' => $validated['nom'],
                'description' => $validated['description'],
                'montant' => $validated['montant'],
            ]);

            // Récupérer l'utilisateur courant
            $user = Auth::user();

            // Si des employés sont associés au délit
            if (!empty($validated['employes'])) {
                // Associer les employés au délit avec la date de l'incident
                $deli->employes()->attach($validated['employes'], [
                    'date_incident' => $validated['date_incident']
                ]);

            }

            // Notifier les responsables (PDG, DDG)
            $responsables = User::whereIn('role', ['pdg', 'ddg'])->get();

            foreach ($responsables as $responsable) {
                $notificationRequest = new Request([
                    'recipient_id' => $responsable->id,
                    'subject' => 'Nouvel incident enregistré',
                    'message' => "Bonjour {$responsable->name},\n\nUn nouvel incident a été enregistré : {$validated['nom']}.\n\nDescription : {$validated['description']}.\n\n Montant total retranché: " . number_format($validated['montant'], 0, ',', ' ') . " FCFA.\n\nDate de l'incident : " . $dateIncident->format('d/m/Y') . ".\n\nNombre d'employés impliqués : " . (empty($validated['employes']) ? 0 : count($validated['employes'])) . "."
                ]);

                $this->notificationController->send($notificationRequest);
            }

            // Historiser l'action
            $this->historiser("Enregistrement d'un incident '{$validated['nom']}' d'un montant de {$validated['montant']} FCFA par {$user->name}", 'create');

            DB::commit();

            return redirect()->route('delis.index')
                ->with('success', 'Incident enregistré avec succès et employés facturés.');

        } catch (\Exception $e) {
            Log::info("message_erreur: " . $e->getMessage());
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement de l\'incident : ' . $e->getMessage());
        }
    }

    public function show(Deli $deli)
    {
        $deli->load('employes');  // Chargement des relations
        return view('delis.show', compact('deli'));
    }

    public function edit(Deli $deli)
    {
        $employes = User::all();
        return view('delis.edit', compact('deli', 'employes'));
    }

    public function update(Request $request, Deli $deli)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'montant' => 'required|numeric|min:0',
            'employes' => 'array',
        ]);
        //mettre la date a defaut a aujourdhui
        $validated['date_incident'] = Carbon::now()->format('Y-m-d');

        $deli->update([
            'nom' => $validated['nom'],
            'description' => $validated['description'],
            'montant' => $validated['montant'],
        ]);

        if (!empty($validated['employes'])) {
            $deli->employes()->sync($validated['employes'], [
                'date_incident' => $validated['date_incident']
            ]);
        } else {
            $deli->employes()->detach();
        }

        return redirect()->route('delis.index')
            ->with('success', 'Deli mis à jour avec succès.');
    }

    public function destroy(Deli $deli)
    {
        $deli->employes()->detach();  // Supprime d'abord les relations
        $deli->delete();
        return redirect()->route('delis.index')
            ->with('success', 'Deli supprimé avec succès.');
    }
}
