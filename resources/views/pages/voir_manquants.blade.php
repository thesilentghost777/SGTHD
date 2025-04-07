 <!-- Error Messages -->
 @if (session('error'))
 <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
     {{ session('error') }}
 </div>
@endif

@if ($errors->any())
 <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
     <ul class="list-disc ml-4">
         @foreach ($errors->all() as $error)
             <li>{{ $error }}</li>
         @endforeach
     </ul>
 </div>
@endif
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mes Manquants</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #f6f9fc 0%, #eef2f7 100%);
        }
        .card {
            background: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
            backdrop-filter: blur(10px);
        }
        .amount {
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s infinite;
        }
        .success-message {
            animation: bounce 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        .btn-primary {
            background: linear-gradient(90deg, #4f46e5, #6366f1);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        .info-message {
            background-color: #f3f4f6;
            border-left: 4px solid #4f46e5;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="card max-w-2xl w-full p-8 mx-auto">
        <div class="info-message text-gray-700">
            Si vous considérez que cette somme est injustifiée, vous avez le droit de contester. Chaque contestation sera examinée avec attention.
        </div>

        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-8">Vos Manquants</h1>

        @if($manquants > 0)
            <div class="amount text-5xl md:text-6xl font-bold mb-8">
                {{ $manquants }} F CFA
                @php
                    $emoji = match(true) {
                        $manquants <= 1000 => '😊', // joyeux
                        $manquants <= 5000 => '🤔', // sceptique
                        $manquants <= 20000 => '😔', // triste
                        $manquants <= 50000 => '😢', // pleurs
                        default => '😭'  // pleurs intenses
                    };
                @endphp
                <span class="text-4xl">{{ $emoji }}</span>
            </div>
        @else
            <div class="success-message text-2xl md:text-3xl text-green-500 bg-green-50 p-6 rounded-xl mb-8">
                Bravo ! Vous n'avez aucun manquant 🎉
            </div>
        @endif

        <button id="btn-contestation"
                class="btn-primary text-white px-6 py-3 rounded-lg font-semibold">
            Contestation de manquants
        </button>

        <form id="formulaire-contestation"
              action="{{ route('message-post') }}"
              method="POST"
              class="hidden mt-8 space-y-4">
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <textarea id="message-contestation"
                      name="message"
                      readonly
                      class="w-full p-4 border rounded-lg bg-gray-50 resize-none h-32"></textarea>
            <button type="submit"
                    class="btn-primary text-white px-6 py-3 rounded-lg font-semibold w-full md:w-auto">
                Envoyer
            </button>
        </form>
    </div>

    <script>
        document.getElementById('btn-contestation').addEventListener('click', function() {
            const formulaire = document.getElementById('formulaire-contestation');
            const message = document.getElementById('message-contestation');
            const employe = @json($nom);
            const secteur = @json($secteur);
            const now = new Date();
            const date = now.toLocaleDateString('fr-FR');
            const heure = now.toLocaleTimeString('fr-FR');
            message.value = `Moi l'employe ${employe} du secteur ${secteur}, je conteste cette valeur des manquants et je fais appels à une vérification détaillée du rapport ayant permis de générer cette somme. Message envoyé le ${date} à ${heure}`;
            formulaire.classList.remove('hidden');
        });
    </script>
</body>
</html>
