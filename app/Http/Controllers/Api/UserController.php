<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(): JsonResponse
{
    $users = User::whereNotIn('role', ['dg', 'pdg', 'ddg'])
        ->orderBy('name')
        ->get()
        ->map(function ($user) {
            return [
                'id' => (int)$user->id,
                'name' => $user->name,
                'email' => $user->email,
                'date_naissance' => $user->date_naissance?->format('Y-m-d'), // Formatage de la date
                'code_secret' => $user->code_secret !== null ? (int)$user->code_secret : null,
                'secteur' => $user->secteur,
                'role' => $user->role,
                'num_tel' => $user->num_tel,
                'avance_salaire' => $user->avance_salaire !== null ? (int)$user->avance_salaire : null,
                'annee_debut_service' => $user->annee_debut_service !== null ? (int)$user->annee_debut_service : null,
                'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
                'created_at' => $user->created_at?->toDateTimeString(),
                'updated_at' => $user->updated_at?->toDateTimeString(),
                // Ne pas inclure le mot de passe pour des raisons de sécurité
            ];
        });

    return response()->json($users);
}
}