<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationController extends Controller
{
    /**
     * Display a listing of all users for notification testing.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('notifications.test', compact('users'));
    }

    /**
     * Send a notification to a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        try {
            $request->validate([
                'recipient_id' => 'required|exists:users,id',
                'subject' => 'required|string|max:255',
                'message' => 'required|string'
            ]);
            $recipient = User::find($request->recipient_id);

            Log::info('Tentative d\'envoi de notification', [
                'sender_id' => 7777,
                'recipient_id' => $recipient->id,
                'subject' => $request->subject
            ]);

            // Création des données pour la notification
            $notificationData = [
                'subject' => $request->subject,
                'message' => $request->message,
                'sender_id' => 7777,
                'sender_name' => 'EasyGest',
                'created_at' => now()->format('d/m/Y H:i')
            ];

            // Envoi de la notification au destinataire
            if ($recipient) {
                try {
                    $recipient->notify(new CustomNotification($notificationData));
                    Log::info('Notification envoyée avec succès à ' . $recipient->email);

                    return redirect()->back()->with('success', 'Notification envoyée avec succès.');
                } catch (\Exception $e) {
                    Log::error('Erreur lors de l\'envoi de la notification : ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Erreur lors de l\'envoi de la notification.');
                }
            } else {
                Log::warning('L\'utilisateur n\'a pas été trouvé pour envoyer la notification');
                return redirect()->back()->with('error', 'Utilisateur non trouvé.');
            }
        } catch (\Exception $e) {
            Log::error('Erreur dans la méthode send : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Display all unread notifications for the current user.
     *
     * @return \Illuminate\View\View
     */
    public function unreadNotifications()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications;

        return view('notifications.unread', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            return redirect()->back()->with('success', 'Notification marquée comme lue.');
        }

        return redirect()->back()->with('error', 'Notification non trouvée.');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}