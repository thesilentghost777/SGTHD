@extends('employee.default2')

@section('page-content')

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Employé - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête du profil -->
        <div class="bg-white rounded-2xl p-8 mb-6 shadow-xl border border-gray-200">
            <div class="flex items-center space-x-6">
                <div class="h-24 w-24 rounded-full bg-blue-600 flex items-center justify-center text-white text-3xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $user->name }}</h1>
                    <p class="text-blue-600">{{ ucfirst($user->role) }} - {{ ucfirst($user->secteur) }}</p>
                </div>
            </div>
        </div>

        <!-- Informations détaillées -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations personnelles -->
            <div class="bg-white rounded-xl p-6 shadow-xl border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informations Personnelles
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-600">Email</span>
                        <span class="text-gray-900">{{ $user->email ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-600">Téléphone</span>
                        <span class="text-gray-900">{{ $user->num_tel ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-600">Date de naissance</span>
                        <span class="text-gray-900">{{ $user->date_naissance ? date('d/m/Y', strtotime($user->date_naissance)) : 'Non renseignée' }}</span>
                    </div>
                </div>
            </div>

            <!-- Informations professionnelles -->
            <div class="bg-white rounded-xl p-6 shadow-xl border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Informations Professionnelles
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-600">Secteur</span>
                        <span class="text-gray-900">{{ ucfirst($user->secteur) ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-600">Rôle</span>
                        <span class="text-gray-900">{{ ucfirst($user->role) ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-600">Année de début</span>
                        <span class="text-gray-900">{{ $user->annee_debut_service ?? 'Non renseignée' }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-600">Ancienneté</span>
                        <span class="text-gray-900">{{ $user->annee_debut_service ? (date('Y') - $user->annee_debut_service) . ' ans' : 'Non calculable' }}</span>
                    </div>
                </div>
            </div>

            <!-- Informations financières -->
            <div class="md:col-span-2 bg-white rounded-xl p-6 shadow-xl border border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informations Financières
                </h2>
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Avance sur salaire récupérée ce mois</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($user->avance_salaire, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

@endsection
