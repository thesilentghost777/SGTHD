<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlimentationController extends Controller
{
    public function dashboard() {
        return view('pages/alimentation/alimentation_dashboard');
    }
}
