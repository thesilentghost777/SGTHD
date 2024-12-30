<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
class MessageController extends Controller
{
    public function message() {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        return view('pages.message');
    }
    public function store_message(Request $request) {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        $request->validate([
         'category'=>'required',
         'message'=>'required|string|max:1000',
        ]);
        Message::create([
          'message'=>$request->message,
          'type'=>$request->category,
          'date_message'=>now(),
          'name'=>$employe->name
        ]);
        return redirect()->route('dashboard')->with('Succes','Message envoye avec succes');
    }
}
