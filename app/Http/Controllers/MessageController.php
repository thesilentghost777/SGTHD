<?php

namespace App\Http\Controllers;

use App\Models\Message;
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
<<<<<<< HEAD
    public function store_message() {
=======
    public function store_message(Request $request) {
>>>>>>> 6636e43ed6c533fa2c38982f6598d0107b426a6d
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


    public function lecture_message()
        {
            $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
    $allMessages = Message::orderBy('date_message', 'desc')->get();
    
    // Créer des tableaux vides pour chaque type
    $messages_complaint_private = [];
    $messages_suggestion = [];
    $messages_report = [];
    $messages_error = [];

    $messages_complaint_private[] = "RAS";
    
    // Parcourir les messages et les classer selon leur type
    foreach($allMessages as $message) {
        switch($message->type) {
            case 'complaint_private':
                $messages_complaint_private[] = $message;
                break;
            case 'suggestion':
                $messages_suggestion[] = $message;
                break;
            case 'report':
                $messages_report[] = $message;
                break;
            case 'error':
                $messages_error[] = $message;
                 break;
        }
    }
    
    // Retourner les tableaux avec compact()
    return view('pages.lecture_message', compact(
        'messages_complaint_private',
        'messages_suggestion',
        'messages_report',
        'messages_error'
    ));
}
    }
