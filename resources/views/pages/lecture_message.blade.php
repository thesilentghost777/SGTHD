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