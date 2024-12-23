<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlaceController extends Controller
{
    public function dashboard() {
        return view('pages/glace/glace_dashboard');
    }
}
