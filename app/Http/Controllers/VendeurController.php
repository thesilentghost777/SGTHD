<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VendeurController extends Controller
{
    public function dashboard() {
        return view('pages/vendeur/vendeur_dashboard');
    }
}
