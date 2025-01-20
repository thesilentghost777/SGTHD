<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class EmployeStandardController extends Controller
{
   
    public function dashboard() {
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $user = User::where('id', $employe->id)->first();
        return view('pages\employes standard\employe-dashboard',[
        'user'=>$user, 
        'nom'=>$user->name,]);
    }
}
