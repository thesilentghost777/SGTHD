<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DgController extends Controller
{
    public function dashboard() {
        return view('pages/dg/dg-dashboard');
    }
}
