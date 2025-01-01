fichier Events/NewNotification::
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $additionalData;

    public function __construct($notification, $additionalData = [])
    {
        $this->notification = $notification;
        $this->additionalData = $additionalData;
    }

    public function broadcastWith()
    {
        return [
            'notification' => $this->notification,
            'additionalData' => $this->additionalData,
            'formattedDate' => $this->notification->created_at->diffForHumans(),
        ];
    }

    public function broadcastOn()
    {
        return new Channel('notifications.' . $this->notification->user_id);
    }
}

fichier App\Http\Controllers\NotificationController.php::
<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Events\NewNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read' => true]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function store(Request $request)
    {
        $notification = Notification::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'read' => false
        ]);

        event(new NewNotification($notification));

        return response()->json($notification);
    }
}

fichier App\Http\Controllers\MessageController.php ::

<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Notification;
use App\Events\NewNotification;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    public function message()
    {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }
        return view('pages.message');
    }

    public function store_message(Request $request)
    {
        $employe = auth()->user();
        if (!$employe) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter');
        }

        $request->validate([
            'category' => 'required',
            'message' => 'required|string|max:1000',
        ]);

        // Sauvegarder le message
        $message = Message::create([
            'message' => $request->message,
            'type' => $request->category,
            'date_message' => now(),
            'name' => $employe->name
        ]);

        // Personnaliser le message de notification selon la catégorie
        $notificationMessage = $this->getCustomNotificationMessage(
            $request->category,
            $employe->name,
            $request->message
        );

        // Créer la notification
        $notification = Notification::create([
            'user_id' => $this->getDGId(),
            'title' => "Message de {$employe->name}",
            'message' => $notificationMessage,
            'type' => 'new_message',
            'read' => false
        ]);

        // Ajouter des données supplémentaires pour la notification
        $additionalData = [
            'category' => $request->category,
            'senderName' => $employe->name,
            'senderRole' => $employe->role,
            'messagePreview' => substr($request->message, 0, 100),
            'priority' => $this->getPriorityLevel($request->category)
        ];

        \Log::info('Émission de notification', [
            'notification' => $notification,
            'additionalData' => $additionalData
        ]);

        // Déclencher l'événement avec les données personnalisées
        event(new NewNotification($notification, $additionalData));

        return redirect()->back()->with('success', 'Message Envoyer');
    }

    private function getCustomNotificationMessage($category, $senderName, $message)
    {
        $preview = substr($message, 0, 50);

        switch ($category) {

            case 'complaint-private':
                return "🔔 Nouvelle plainte privée";
            case 'suggestion':
                return "💡 Suggestion de {$senderName}: {$preview}...";
            case 'report':
                return "⚠️ Nouveau signalement de {$senderName}: {$preview}...";
            case 'error':
                return "❌ Signalement d'erreur par {$senderName}";
            default:
                return "📩 Nouveau message de {$senderName}: {$preview}...";
        }
    }

    private function getPriorityLevel($category)
    {
        return match ($category) {
            'suggestion' => 'meduim',
            'complaint-private' => 'medium',
            'report' => 'high',
            default => 'normal',
        };
    }

    private function getDGId()
    {
        return User::where('role', 'dg')->first()->id;
    }

    public function lecture_message(){
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

fichier App\Models\Notification.php::
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notification extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'read',
        'type'
    ];

    protected $casts = [
        'read' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

fichier App\Models\Message.php::
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $table='Message';
    protected $fillable = ['message', 'type', 'date_message', 'name', 'read'];
}

fichier App\Notifications\NewMessageNotification.php::
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

fichier Config\broadcasting.php ::
<?php

return [
    'default' => env('BROADCAST_DRIVER', 'pusher'),

    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'encrypted' => true,
            ],
        ],
    ],
];
fichier Database\migrations\2024_12_30_223012_create_notifications_table.php::

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type');
            $table->boolean('read')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};

fichier Resources\js\bootstrap.js ::
import axios from 'axios';
import Echo from "laravel-echo";
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Écouter les notifications pour l'utilisateur connecté
const userId = document.querySelector('meta[name="user-id"]').getAttribute('content');

window.Echo.private(`notifications.${userId}`)
    .listen('NewNotification', (notification) => {
        // Afficher une notification système
        if (Notification.permission === 'granted') {
            new Notification(notification.title, {
                body: notification.message,
                icon: '/icon.png'
            });
        }

        // Mettre à jour le compteur de notifications
        updateNotificationCount();

        // Ajouter la notification à la liste
        addNotificationToList(notification);
    });
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

fichier Resources\js\notification.js::

// Fonction pour afficher la notification
function showNotification(data) {
    const { notification, additionalData } = data;

    // Personnaliser l'icône selon la catégorie
    const icon = getNotificationIcon(additionalData.category);

    // Personnaliser le style selon la priorité
    const style = getNotificationStyle(additionalData.priority);

    // Créer une notification système personnalisée
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(notification.title, {
            body: notification.message,
            icon: icon,
            badge: icon,
            tag: notification.id,
            vibrate: additionalData.priority === 'high' ? [200, 100, 200] : undefined,
            requireInteraction: additionalData.priority === 'high'
        });
    }

    // Ajouter la notification à l'interface
    const template = `
        <div class="notification ${style.className}" data-id="${notification.id}">
            <div class="flex items-center">
                <img src="${icon}" class="w-8 h-8 mr-2" alt="icon">
                <div>
                    <h4 class="font-bold">${notification.title}</h4>
                    <p class="${style.textClass}">${notification.message}</p>
                    <small class="text-gray-500">${data.formattedDate}</small>
                </div>
            </div>
        </div>
    `;

    const container = document.getElementById('notifications-container');
    container.insertAdjacentHTML('afterbegin', template);
}

function getNotificationIcon(category) {
    const icons = {
        'urgent': '/icons/urgent.png',
        'complaint-private': '/icons/complaint.png',
        'suggestion': '/icons/suggestion.png',
        'report': '/icons/report.png',
        'error': '/icons/error.png',
        'default': '/icons/message.png'
    };
    return icons[category] || icons.default;
}

function getNotificationStyle(priority) {
    const styles = {
        'high': {
            className: 'bg-red-50 border-l-4 border-red-500',
            textClass: 'text-red-800'
        },
        'medium': {
            className: 'bg-yellow-50 border-l-4 border-yellow-500',
            textClass: 'text-yellow-800'
        },
        'normal': {
            className: 'bg-blue-50 border-l-4 border-blue-500',
            textClass: 'text-blue-800'
        }
    };
    return styles[priority] || styles.normal;
}

fichier Resources\views\notification-bell.blade.php::

<div class="relative">
    <button id="notification-button" class="relative">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"></span>
    </button>

    <div id="notifications-container" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg hidden">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Notifications</h3>
                <button onclick="markAllAsRead()" class="text-sm text-blue-600 hover:text-blue-800">
                    Tout marquer comme lu
                </button>
            </div>
            <!-- Les notifications seront injectées ici dynamiquement -->
        </div>
    </div>
</div>

fichier Resources\views\layouts\app.blade.php::

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ auth()->id() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @push('scripts')
        <script>
        // Demander la permission pour les notifications du navigateur
        document.addEventListener('DOMContentLoaded', function() {
        if ('Notification' in window) {
            Notification.requestPermission();
        }
        });
        </script>
@endpush
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>

fichier Resources\views\pages\lecture_message.blade.php::
@vite(['resources/css/lecture_message.css','resources/js/lecture_message.js'])
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Lecture des Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="container">
    <h1>Messages</h1>
    <div class="categories-description">
    <div class="category-card">
        <div class="category-icon">🔒</div>
        <h3>Plaintes privées</h3>
        <p>Messages privés et confidentiels. L'identité de l'expéditeur reste anonyme.</p>
    </div>
    <div class="category-card">
        <div class="category-icon">💡</div>
        <h3>Suggestions</h3>
        <p>Idées et propositions d'amélioration de nos services.</p>
    </div>
    <div class="category-card">
        <div class="category-icon">📝</div>
        <h3>Repports</h3>
        <p>Signalements et rapports d'incidents ou de problèmes.</p>
    </div>
    <br><br>
    <div class="category-card">
        <div class="category-icon">📝</div>
        <h3>Conseils</h3>
        <p>Apres la lecture d'un message vous devez le supprimer quand vous finissez de le traiter ou pour l'ignorer</p>
    </div>
    <br>
    <br>
    <br>
    <br>
</div>
    <div class="chat-container">
        <!-- Plaintes privées -->
        <div class="chat-type" onclick="toggleMessages('complaint-private')">
            <div class="chat-header">
                <h2>Plaintes privées</h2>
                @php
                    $unreadCount = $messages_complaint_private->where('read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="unread-count">
                        {{ $unreadCount }}
                    </span>
                @endif
            </div>
            <div class="messages-container" id="complaint-private" style="display: none;">
                @if($messages_complaint_private->count() > 0)
                    @foreach($messages_complaint_private as $message)
                    <div class="message {{ $message->read ? 'read' : 'unread' }}">
                        <div class="message-content">
                        <div class="message-header">
                             <strong>Anonyme</strong> <!-- Toujours afficher Anonyme -->
                        </div>
                            <div class="message-text">{{ $message->message }}</div>
                            <div class="message-date">{{ date('d/m/Y', strtotime($message->date_message)) }}</div>
                        </div>
                        <form action="{{ route('messages.destroy', ['message' => $message->id]) }}" method="POST" onsubmit="return deleteMessage(this)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-btn">x</button>
                        </form>
                    </div>
                    @endforeach
                @else
                    <div class="no-messages">Aucun message</div>
                @endif
            </div>
        </div>

        <!-- Suggestions -->
        <div class="chat-type" onclick="toggleMessages('suggestion')">
            <div class="chat-header">
                <h2>Suggestions</h2>
                @php
                    $unreadCount = $messages_suggestion->where('read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="unread-count">
                        {{ $unreadCount }}
                    </span>
                @endif
            </div>
            <div class="messages-container" id="suggestion" style="display: none;">
                @if($messages_suggestion->count() > 0)
                    @foreach($messages_suggestion as $message)
                    <div class="message {{ $message->read ? 'read' : 'unread' }}">
                        <div class="message-content">
                            <div class="message-header">
                                <strong>{{ $message->name !== 'null' ? $message->name : 'Anonyme' }}</strong>
                            </div>
                            <div class="message-text">{{ $message->message }}</div>
                            <div class="message-date">{{ date('d/m/Y', strtotime($message->date_message)) }}</div>
                        </div>
                        <form action="{{ route('messages.destroy', ['message' => $message->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn" onclick="return confirm('Supprimer ce message ?')">
                                ×
                            </button>
                        </form>
                    </div>
                    @endforeach
                @else
                    <div class="no-messages">Aucun message</div>
                @endif
            </div>
        </div>

        <!-- Reports -->
        <div class="chat-type" onclick="toggleMessages('report')">
            <div class="chat-header">
                <h2>Repports</h2>
                @php
                    $unreadCount = $messages_report->where('read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="unread-count">
                        {{ $unreadCount }}
                    </span>
                @endif
            </div>
            <div class="messages-container" id="report" style="display: none;">
                @if($messages_report->count() > 0)
                    @foreach($messages_report as $message)
                    <div class="message {{ $message->read ? 'read' : 'unread' }}">
                        <div class="message-content">
                            <div class="message-header">
                                <strong>{{ $message->name !== 'null' ? $message->name : 'Anonyme' }}</strong>
                            </div>
                            <div class="message-text">{{ $message->message }}</div>
                            <div class="message-date">{{ date('d/m/Y', strtotime($message->date_message)) }}</div>
                        </div>
                        <form action="{{ route('messages.destroy', ['message' => $message->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn" onclick="return confirm('Supprimer ce message ?')">
                                X
                            </button>
                        </form>
                    </div>
                    @endforeach
                @else
                    <div class="no-messages">Aucun message</div>
                @endif
            </div>
        </div>
    </div>
    <!-- À ajouter juste avant la fermeture de la div.container -->
<div class="back-button-container">
    <a href="{{ route('dashboard') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Retour au Dashboard
    </a>
</div>
</div>

<script>
function toggleMessages(type) {
    const container = document.getElementById(type);
    const isHidden = container.style.display === 'none' || container.style.display === '';

    // Cache tous les autres conteneurs
    document.querySelectorAll('.messages-container').forEach((msgContainer) => {
        if (msgContainer !== container) {
            msgContainer.style.display = 'none';
        }
    });

    // Bascule l'affichage du conteneur sélectionné
    container.style.display = isHidden ? 'block' : 'none';

    if (isHidden) {
        fetch(`/messages/mark-read/${type}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => {
            // Mettre à jour l'interface
            document.querySelectorAll(`#${type} .message.unread`).forEach(msg => {
                msg.classList.remove('unread');
                msg.classList.add('read');
            });
            const countElement = container.previousElementSibling.querySelector('.unread-count');
            if (countElement) {
                countElement.style.display = 'none';
            }
        });
    }
}

// Ajoutez cette fonction pour la suppression
function deleteMessage(formElement) {
    if (confirm('Supprimer ce message ?')) {
        formElement.submit();
    }
    return false;
}
</script>

</body>
</html>

fichier Resources\views\pages\message.blade.php::
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Envoi de Message</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .guidelines {
            background: linear-gradient(135deg, #fff, #f8f9fa);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .guidelines h2 {
            color: #1877F2;
            margin-bottom: 15px;
            font-size: 1.5em;
        }

        .guidelines-list {
            list-style: none;
        }

        .guidelines-list li {
            margin: 10px 0;
            padding-left: 25px;
            position: relative;
        }

        .guidelines-list li:before {
            content: "✓";
            color: #1877F2;
            position: absolute;
            left: 0;
        }

        h1 {
            color: #1877F2;
            text-align: center;
            margin: 20px 0;
            font-size: 2em;
        }

        .message-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        select, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #e4e6eb;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        select:focus, textarea:focus {
            border-color: #1877F2;
            outline: none;
        }

        select {
            background: white;
            cursor: pointer;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        #sendButton {
            background: linear-gradient(to right, #1877F2, #0099FF);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        #sendButton:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(24, 119, 242, 0.3);
        }

        .confirmation-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            text-align: center;
            z-index: 1000;
            min-width: 300px;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }

        .countdown {
            color: #1877F2;
            font-weight: bold;
            font-size: 1.2em;
            display: block;
            margin: 15px 0;
        }

        #cancelButton {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        #cancelButton:hover {
            background: #c82333;
        }

        .success-message, .error-message {
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            display: none;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="guidelines">
            <h2>Conseils d'utilisation</h2>
            <ul class="guidelines-list">
                <li>Vos messages privés restent totalement anonymes</li>
                <li>Évitez tout langage inapproprié ou offensant</li>
                <li>Soyez précis et constructif dans vos messages</li>
                <li>Les suggestions sont examinées régulièrement</li>
                <li>Les signalements sont traités en priorité</li>
            </ul>
        </div>

        <div class="message-form">
            <h1>Envoyez un Message</h1>
            <form action="{{route('message-post')}}" method="POST">
            @csrf
            <select id="messageCategory" name="category" required>
                <option value="" disabled selected>Sélectionnez une catégorie</option>
                <option value="complaint-private">Plainte (Privée)</option>
                <option value="suggestion">Suggestion</option>
                <option value="report">Signalement</option>
            </select>

            <textarea id="messageContent" name="message" placeholder="Écrivez votre message ici..."></textarea>

            <div class="success-message" id="successMessage">Message envoyé avec succès !</div>
            <div class="error-message" id="errorMessage">Erreur lors de l'envoi du message.</div>

            <button id="sendButton">Envoyer</button>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="overlay"></div>
    <div class="confirmation-modal" id="confirmation">
        <p>Êtes-vous sûr de vouloir envoyer ce message ?</p>
        <div class="countdown" id="countdown"></div>
        <button id="cancelButton">Annuler</button>
    </div>


</body>
</html>

fichier routes\web.php::
<?php

use App\Http\Controllers\ProfileController;
 use App\Http\Controllers\ProducteurController;
use App\Http\Controllers\DgController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DdgController;
use App\Http\Controllers\PdgController;
use App\Http\Controllers\Chef_productionController;
use App\Http\Controllers\ServeurController;
use App\Http\Controllers\AlimentationController;
use App\Http\Controllers\GlaceController;
use App\Http\Controllers\PointeurController;
use App\Http\Controllers\MessageController;




Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/producteur/produit', [ProducteurController::class,'producteur'])->name('producteur-produit');
Route::post('/producteur/store', [ProducteurController::class,'store'])->name('enr_produits');

Route::get('/dg/dashboard', [DgController::class,'dashboard'])->name('dg-dashboard');

Route::get('alimentation/dashboard', [AlimentationController::class,'dashboard'])->name('alimentation-dashboard');

Route::get('chef_production/dashboard', [Chef_productionController::class,'dashboard'])->name('chef_production-dashboard');

Route::get('ddg/dashboard', [DdgController::class,'dashboard'])->name('ddg-dashboard');

Route::get('pdg/dashboard', [PdgController::class,'dashboard'])->name('pdg-dashboard');


Route::get('pointeur/dashboard', [PointeurController::class, 'dashboard'])->name('pointeur-dashboard');

Route::get('chef_production/dashboard', [Chef_productionController::class, 'dashboard'])->name('chef_production-dashboard');

Route::get('glace/dashboard', [GlaceController::class, 'dashboard'])->name('glace-dashboard');



require __DIR__.'/auth.php';

Route::get('producteur/dashboard', [ProducteurController::class, 'dashboard'])->name('producteur-dashboard');
Route::get('/producteur/produit', [ProducteurController::class,'produit'])->name('producteur_produit');
Route::get('/producteur/pdefault', [ProducteurController::class,'pdefault'])->name('producteur_default');

Route::get('producteur/dashboard', [ProducteurController::class, 'dashboard'])->name('producteur-dashboard');
Route::get('producteur/fiche_production', [ProducteurController::class, 'fiche_production'])->name('producteur-fiche_production');
Route::get('producteur/commande', [ProducteurController::class, 'commande'])->name('producteur-commande');
Route::get('serveur/ajouterProduit_recu', [ServeurController::class, 'ajouterProduit_recu'])->name('serveur-ajouterProduit_recu');
Route::post('serveur/store', [ServeurController::class, 'store'])->name('addProduit_recu');

Route::get('serveur/dashboard', [ServeurController::class,'dashboard'])->name('serveur-dashboard');
Route::get('serveur/enrProduit_vendu', [ServeurController::class, 'enrProduit_vendu'])->name('serveur-enrProduit_vendu');
Route::post('serveur/store_vendu', [ServeurController::class, 'store_vendu'])->name('saveProduit_vendu');
Route::post('serveur/nbre_sacs_vendu', [ServeurController::class, 'nbre_sacs_vendu'])->name('serveur-nbre_sacs_vendu');

Route::get('serveur/produit_invendu', [ServeurController::class, 'produit_invendu'])->name('serveur-produit_invendu');
Route::post('serveur/store_invendu', [ServeurController::class, 'store_invendu'])->name('saveProduit_invendu');
Route::post('serveur/produit_avarier', [ServeurController::class, 'produit_avarier'])->name('serveur-produit_avarier');

Route::get('serveur/versement', [ServeurController::class, 'versement'])->name('serveur-versement');
Route::post('serveur/store_versement', [ServeurController::class, 'store_versement'])->name('save_versement');
Route::post('serveur/monnaie_recu', [ServeurController::class, 'monnaie_recu'])->name('serveur-monnaie_recu');

Route::get('serveur/fiche_versement', [ServeurController::class, 'fiche_versement'])->name('serveur-fiche_versement');
Route::get('message', [MessageController::class, 'message'])->name('message');
Route::post('message/store_message', [MessageController::class, 'store_message'])->name('message-post');

Route::get('lecture_message', [MessageController::class, 'lecture_message'])->name('lecture_message');
Route::post('/messages/mark-read/{type}', [MessageController::class, 'markRead'])->name('messages.markRead');
Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
