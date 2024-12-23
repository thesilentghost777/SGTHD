<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
      
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'date_naissance' => ['required', 'date', 'before:today'],
            'code_secret' => ['required', 'integer'],
            'secteur' => [
                'required',
                'string',
            ],
            'role' => [
                'required',
                'string'
            ],
            'num_tel' => [
                'required',
                'regex:/^6[0-9]{8}$/',
                'unique:users'
            ],
            'annee_debut_service' => [
                'required', 
                'integer',
                'min:1950',
                'max:' . date('Y')
            ]
        ]);

        //creation and saving
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->date_naissance = $request->date_naissance;
            $user->code_secret = $request->code_secret;
            $user->secteur = $request->secteur;
            $user->role = $request->role;
            $user->num_tel = $request->num_tel;
            $user->avance_salaire = 0;
            $user->annee_debut_service = $request->annee_debut_service;
            $user->created_at = now();
        
        $user->save();
        Auth::login($user);

        return redirect(route('dashboard', absolute: true));
    }
}