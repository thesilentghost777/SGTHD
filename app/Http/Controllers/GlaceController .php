<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VersementCsg;
use App\Models\Transaction_vente;
use Carbon\Carbon;
class GlaceController extends Controller
{
    public function dashboard() {
        $employe = auth()->user();

        if (!$employe) {
          return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $user=User::where('id',$employe->id)->first();
        $ventes=\DB::table('transaction_ventes')
        ->whereDate('transaction_ventes.created_at',Carbon::today())
        ->where('transaction_ventes.serveur',$employe->id)
        ->where('type','Vente_glace')
        ->sum('transaction_ventes.prix');
  
        $versement = \DB::table('versement_csgs')
        ->whereDate('versement_csgs.created_at', Carbon::today())
        ->where('versement_csgs.verseur', $employe->id)
        ->sum('versement_csgs.somme');
        return view('pages/glace/glace_dashboard',[
              'user'=>$user,
              'ventes'=>$ventes,
              'versement'=>$versement
        ]);
    }
public function store(Request $request){
    $employe = auth()->user();

    if (!$employe) {
      return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }
     $request->validate([
       'parfum'=>'required',
       'prix'=>'required',
       'type'=>'required',
     ]);
     Transaction_vente::create([
      'parfum'=>$request->parfum,
      'prix'=>$request->prix,
      'type'=>$request->type,
      'serveur'=>$employe->id,
      'date_vente'=>Carbon::today()
     ]);
return redirect()->route('glace-dashboard');
}
public function vente(){

    return view('pages/glace/glace-vente');
}
public function stat(){
    $ventesParJour = \DB::table('transaction_ventes')
    ->select(
        \DB::raw('DATE(created_at) as date'),
        \DB::raw('SUM(CASE WHEN type = "Vente_glace" THEN prix ELSE 0 END) as ventes'),
    )
    ->groupBy(\DB::raw('DATE(created_at)'))
    ->orderBy('date', 'asc')
    ->get();
    return view('pages/glace/glace-stat',compact('ventesParJour'));

}



}
