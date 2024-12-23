<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DdgController extends Controller
{
    public function dashboard() {
        return view('pages/ddg/ddg_dashboard');
    }
}
