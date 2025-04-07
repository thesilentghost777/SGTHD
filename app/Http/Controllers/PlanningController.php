<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PlanningController extends Controller
{
    public function index()
    {
        $employes = User::whereIn('role', ['boulanger', 'patissier', 'serveur'])->get();
        $plannings = Planning::with('user')->get();

        // Grouper les jours de repos par employé
        $joursRepos = Planning::where('type', 'repos')
            ->whereDate('date', '>=', now())
            ->with('user')
            ->get()
            ->groupBy('employe');

        return view('plannings.index', compact('employes', 'plannings', 'joursRepos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => 'required|string',
            'employe' => 'required|exists:users,id',
            'type' => 'required|in:tache,repos',
            'date' => 'required|date',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut'
        ]);

        // Vérifier s'il existe déjà un planning pour cet employé à cette date
        $existingPlanning = Planning::where('employe', $validated['employe'])
            ->whereDate('date', $validated['date'])
            ->first();

        if ($existingPlanning) {
            return response()->json([
                'status' => 'error',
                'message' => 'Un planning existe déjà pour cet employé à cette date'
            ], 422);
        }

        $planning = Planning::create($validated);

        return response()->json([
            'status' => 'success',
            'planning' => $planning
        ]);
    }

    public function update(Request $request, Planning $planning)
    {
        $validated = $request->validate([
            'libelle' => 'required|string',
            'type' => 'required|in:tache,repos',
            'date' => 'required|date',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i|after:heure_debut'
        ]);

        $planning->update($validated);

        return response()->json([
            'status' => 'success',
            'planning' => $planning
        ]);
    }

    public function destroy(Planning $planning)
    {
        $planning->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function monPlanning()
    {
        $employe_id = auth()->id();
        $plannings = Planning::where('employe', $employe_id)
            ->whereDate('date', '>=', now())
            ->orderBy('date')
            ->get();

        return view('plannings.mon-planning', compact('plannings'));
    }

    public function getEvents()
    {
        $plannings = Planning::with('user')->get();

        return response()->json($plannings->map(function($planning) {
            return [
                'id' => $planning->id,
                'title' => $planning->libelle,
                'start' => $planning->date->format('Y-m-d') .
                    ($planning->heure_debut ? 'T' . $planning->heure_debut->format('H:i:s') : ''),
                'end' => $planning->date->format('Y-m-d') .
                    ($planning->heure_fin ? 'T' . $planning->heure_fin->format('H:i:s') : ''),
                'backgroundColor' => $planning->type === 'repos' ? '#EF4444' : '#3B82F6',
                'borderColor' => $planning->type === 'repos' ? '#DC2626' : '#2563EB',
                'extendedProps' => [
                    'type' => $planning->type,
                    'employe' => $planning->user->name
                ]
            ];
        }));
    }
}
