<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecture des Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .message-type {
            margin: 20px 0;
        }
        .message-type h2 {
            color: #007bff;
        }
        .message {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 5px 0;
            transition: background-color 0.3s;
        }
        .message:hover {
            background-color: #f0f8ff;
        }
        .no-messages {
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Lecture des Messages</h1>

    @if(count($messages_complaint_private) > 1)
        <div class="message-type">
            <h2>Plaintes privees</h2>
            @foreach($messages_complaint_private as $message)
                <div class="message">{{ $message->content }}</div>
            @endforeach
        </div>
    @else
        <div class="message-type">
            <h2>Plainte privee</h2>
            <div class="no-messages">Aucun message à afficher.</div>
        </div>
    @endif

    @if(count($messages_suggestion) > 0)
        <div class="message-type">
            <h2>Suggestions</h2>
            @foreach($messages_suggestion as $message)
                <div class="message">{{ $message->content }}</div>
            @endforeach
        </div>
    @else
        <div class="message-type">
            <h2>Suggestions</h2>
            <div class="no-messages">Aucune suggestion à afficher.</div>
        </div>
    @endif
    
    @if(count($messages_report) > 0)
        <div class="message-type">
            <h2>Signalements</h2>
            @foreach($messages_report as $message)
                <div class="message">{{ $message->content }}</div>
            @endforeach
        </div>
    @else
        <div class="message-type">
            <h2>Signalements</h2>
            <div class="no-messages">Aucun repport à afficher.</div>
        </div>
    @endif
    
    @if(count($messages_error) > 0)
        <div class="message-type">
            <h2>Erreurs</h2>
            @foreach($messages_error as $message)
                <div class="message">{{ $message->content }}</div>
            @endforeach
        </div>
    @else
        <div class="message-type">
            <h2>Erreurs</h2>
            <div class="no-messages">Aucune erreur à afficher.</div>
        </div>
    @endif
</div>

</body>
</html>