<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Notifications\UserRegisteredNotification;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Valider le JSON
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'date_naissance' => 'required|date',
            'code_secret' => 'required|string',
            'departement' => 'required|string',
            'role' => 'required|string',
            'num_tel' => 'required|string',
            'annee_debut_service' => 'required|integer'
        ]);

        // 2. Créer l'utilisateur
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'date_naissance' => $data['date_naissance'],
            'code_secret' => $data['code_secret'],
            'secteur' => $data['departement'],
            'role' => $data['role'],
            'num_tel' => $data['num_tel'],
            'annee_debut_service' => $data['annee_debut_service']
        ]);

        // 3. Envoyer l'email (en queue pour éviter les timeout)
        $user->notify(new UserRegisteredNotification());

        // 4. Réponse JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Inscription réussie. Vérifiez votre email.',
            'user' => $user,
            'token' => $user->createToken('auth-token')->plainTextToken
        ], 201);
    }

    // Connexion
    public function login(Request $request)
    {
        
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            \Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'status' => 'erreur',
                'errors' => $validator->errors()
            ], 422);
        }
    
        if (!Auth::attempt($request->only('email', 'password'))) {
            \Log::error('Auth attempt failed', ['email' => $request->email]);
            return response()->json([
                'status' => 'erreur',
                'message' => 'Identifiants invalides'
            ], 401);
        }
    
        $user = Auth::user();
        $token = $user->createToken('mobile-token')->plainTextToken;
    
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Déconnecté avec succès']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}