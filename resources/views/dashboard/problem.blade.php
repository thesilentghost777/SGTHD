@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur d'Accès</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .warning-pulse {
            animation: pulse 2s infinite;
        }
        .gradient-background {
            background: linear-gradient(135deg, #f0f9ff 0%, #e6f7ff 100%);
        }
        .error-shadow {
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body class="gradient-background min-h-screen flex items-center justify-center p-4">
    @if(session('success'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 3000)"
                 class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 3000)"
                 class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
                {{ session('error') }}
            </div>
        @endif
    <div class="max-w-4xl mx-auto text-center">
        <!-- Illustration principale -->
        <div class="mb-8 warning-pulse">
            <svg class="w-40 h-40 mx-auto mb-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4Z" stroke="#EF4444" stroke-width="2"/>
                <path d="M12 8V12" stroke="#EF4444" stroke-width="2" stroke-linecap="round"/>
                <circle cx="12" cy="15" r="1" fill="#EF4444"/>
            </svg>
        </div>

        <!-- Message principal -->
        <div class="bg-white p-8 rounded-xl shadow-2xl error-shadow mb-8">
            <h1 class="text-3xl font-bold text-red-600 mb-6">
                Erreur d'Authentification
            </h1>

            <div class="space-y-6">
                <p class="text-xl text-gray-700 mb-4">
                    Votre code secret est erroné.
                </p>

                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <p class="text-lg text-red-700">
                        Êtes-vous sûr d'avoir :
                    </p>
                    <ul class="text-left text-red-600 mt-2 space-y-2">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Sélectionné le bon poste ?
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Entré le bon code secret ?
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Message d'action -->
        <div class="bg-blue-50 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
            <h2 class="text-xl font-semibold text-blue-800 mb-4">
                Action Requise
            </h2>
            <p class="text-blue-700 mb-4">
                Veuillez contacter l'administration au plus tôt pour régler ce problème.
            </p>
            <div class="inline-flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Contacter l'Administration
            </div>
        </div>
    </div>
</body>


</html>
@endsection
