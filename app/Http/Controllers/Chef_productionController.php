<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Chef_productionController extends Controller
{
    public function dashboard() {
        return view('pages/chef_production/chef_production_dashboard');
    }
    public function gestion_employe() {
        return view('pages/chef_production/chef_production_gestion_employe');
    }
}
