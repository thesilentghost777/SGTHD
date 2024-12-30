<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
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
        if($request->type != 'complaint-private'){
            Message::create([
                'message'=>$request->message,
                'type'=>$request->category,
                'date_message'=>now(),
                'name'=>$employe->name
              ]);
        }else{
            Message::create([
                'message'=>$request->message,
                'type'=>$request->category,
                'date_message'=>now(),
                'name'=>'null',
              ]);
        }
        return view('pages.message');
       
    }


    public function lecture_message()
{
    $employe = auth()->user();
    if (!$employe) {
        return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }
    
    $messages_complaint_private = Message::where('type', 'complaint-private')->get();
    $messages_suggestion = Message::where('type', 'suggestion')->get();
    $messages_report = Message::where('type', 'report')->get();
    $messages_error = Message::where('type', 'error')->get();
    
    return view('pages.lecture_message', compact(
        'messages_complaint_private',
        'messages_suggestion',
        'messages_report',
        'messages_error'
    ));
}
public function destroy(Message $message)
{
    $message->delete();
    return redirect()->back()->with('success', 'Message supprimé');
}

public function markRead($type)
{
    try {
        // Mettre à jour tous les messages non lus du type spécifié
        Message::where('type', $type)
              ->where('read', false)
              ->update(['read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Messages marqués comme lus'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour des messages'
        ], 500);
    }
}
}