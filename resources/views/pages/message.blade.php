@extends('layouts.app')

@section('content')
<br>
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
@endsection
