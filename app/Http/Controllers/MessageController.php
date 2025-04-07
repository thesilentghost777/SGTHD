<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\ACouper;
use App\Models\User;
use App\Notifications\SignalementNotification;
use Illuminate\Support\Facades\Log;
use App\Traits\HistorisableActions;




class MessageController extends Controller
{
    use HistorisableActions;

    public function message() {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        return view('pages.message');
    }
    public function store_message(Request $request) {
        if (!auth()->user()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        $validatedData = $request->validate([
            'message' => 'required|string|max:1000',
            'category' => 'nullable|string'
        ]);

        $messageData = [
            'message' => $validatedData['message'],
            'type' => $validatedData['category'] ?? 'report',
            'date_message' => now(),
            'name' => $request->type != 'complaint-private' ? auth()->user()->name : 'null'
        ];

        // Créer le message
        $message = Message::create($messageData);
        $user = auth()->user();
        $this->historiser("L'utilisateur {$user->name} a créé un message de type {$messageData['type']}", 'create_message');
        // Si c'est un signalement, envoyer une notification au DG
        // Dans store_message()
if ($messageData['type'] === 'report') {
    // Récupérer l'utilisateur DG
    $dg = User::getDG();
    Log::info('Utilisateur DG trouvé : ' . ($dg ? 'Oui (ID: '.$dg->id.', Email: '.$dg->email.')' : 'Non'));

    // Envoyer la notification si le DG existe
    if ($dg) {
        try {
            $dg->notify(new SignalementNotification($message));
            Log::info('Notification envoyée avec succès à ' . $dg->email);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification : ' . $e->getMessage());
        }
    } else {
        Log::warning('Aucun utilisateur DG trouvé pour envoyer la notification');
    }
}

        return redirect()->back()->with('success','message transmis avec succès');
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
public function showManquants() {
    $employe = auth()->user();
    if (!$employe) {
        return redirect()->route('login')->with('error', 'Veuillez vous connecter');
    }
    $info = User::where('id', $employe->id)->first();
    $nom = $info->name;
    $secteur = $info->secteur;
    $manquants = ACouper::where('id_employe', $employe->id)
    ->whereMonth('date', now()->month)
    ->whereYear('date', now()->year)
    ->first()
    ->manquants ?? 0;
    return view('pages.voir_manquants', compact('manquants','nom','secteur'))->with('success','Requete Envoyer avec success');
}

}
