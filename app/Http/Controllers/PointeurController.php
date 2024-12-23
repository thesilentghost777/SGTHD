<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PointeurController extends Controller
{
    public function dashboard() {
        return view('pages/pointeur/pointeur_dashboard');
    }
}
