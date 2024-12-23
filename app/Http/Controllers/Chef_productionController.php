<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Chef_productionController extends Controller
{
    public function dashboard() {
        return view('pages/chef_production/chef_production_dashboard');
    }
}
