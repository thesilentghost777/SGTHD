<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PdgController extends Controller
{
    public function dashboard() {
        return view('pages/pdg/pdg_dashboard');
    }

    public function depense() {
        return view('pages/pdg/pdg_depense');
    }
}
