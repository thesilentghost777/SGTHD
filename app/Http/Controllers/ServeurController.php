<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServeurController extends Controller
{
    public function dashboard() {
        return view('pages/serveur/serveur_dashboard');
    }
}
