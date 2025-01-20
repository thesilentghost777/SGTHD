<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\VersementCsg;
use Illuminate\Http\Request;
use Carbon\Carbon;
class CaissiereController extends Controller
{
    public function dashboard() {
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $user=User::where('id',$employe->id)->first();
        $versement = \DB::table('versement_csgs')
        ->whereDate('versement_csgs.created_at', Carbon::today())
        ->where('versement_csgs.verseur', $employe->id)
        ->sum('versement_csgs.somme');
        return view('pages/caissiere/caissiere-dashboard',[
            'user'=>$user,
            'versement'=>$versement
        ]);
    }  
    public function stat(){
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $versementsParJour = \DB::table('versement_csgs')
        ->select(
            \DB::raw('DATE(created_at) as date'),
            \DB::raw('SUM(versement_csgs.somme ) as versements'),
        )
        ->where('versement_csgs.verseur', $employe->id)
        ->groupBy(\DB::raw('DATE(created_at)'))
        ->orderBy('date', 'asc')
        ->get();
        return view('pages/caissiere/caissiere-stat',compact('versementsParJour'));
    
    }
    
    


}
