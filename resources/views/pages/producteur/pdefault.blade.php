@extends('layouts.app')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TH Market Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
<div class="flex min-h-screen" x-data="{ sidebarOpen: false }">
    <!-- Mobile Menu Button -->
    <button
        class="lg:hidden p-4 text-white bg-blue-600 fixed z-50 top-4 left-4 rounded-md shadow-md"
        @click="sidebarOpen = !sidebarOpen"
        aria-label="Open menu">
        <i class="mdi mdi-menu text-2xl"></i>
    </button>

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="lg:translate-x-0 transform lg:w-72 w-64 bg-gradient-to-br from-blue-800 to-blue-600 text-white p-6 flex flex-col fixed lg:static inset-y-0 z-40 transition-transform duration-300 ease-in-out">
        <div class="text-center border-b border-white/20 pb-6">
            <h1 class="text-2xl font-bold">TH MARKET</h1>
            <span class="text-xs">Powered by SGc</span>
        </div>

        <!-- Menu Sections -->
        <div class="mt-6 space-y-8">
            <!-- Production Section -->
            <div>
                <h3 class="uppercase text-sm font-semibold opacity-70 mb-3">Production</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('producteur_produit') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-clipboard-text mr-2"></i>Produits du jour</a></li>
                    <li><a href="{{ route('producteur-commande') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-cart mr-2"></i>Commandes</a></li>
                    <li class="flex items-center p-2 rounded hover:bg-white/10 cursor-pointer"><i class="mdi mdi-archive mr-2"></i>Réservation MP</li>
                </ul>
            </div>
		 <!-- General Section -->
            <div>
                <h3 class="uppercase text-sm font-semibold opacity-70 mb-3">Général</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('producteur.sp') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-chart-bar mr-2"></i>Statistiques</a></li>
                    <li><a href="{{ route('producteur.lots') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-eye mr-2"></i>Details des Productions</a></li>
                    <li><a href="{{ route('manquant') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-alert-circle mr-2"></i>Manquants</a></li>
                    <li><a href="{{ route('primes.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-gift mr-2"></i>Primes</a></li>
                    <li><a href="{{ route('horaire.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-clock-check mr-2"></i>Horaires</a></li>
                    <li><a href="{{ route('fiche-paie.show') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-file-document-multiple mr-2"></i>Fiche de paie</a></li>
                    <li><a href="{{ route('producteur.comparaison') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-podium mr-2"></i>Classement</a></li>
                </ul>
            </div>

            <!-- Communications Section -->
            <div>
                <h3 class="uppercase text-sm font-semibold opacity-70 mb-3">Communications</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('reclamer-as') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-cash mr-2"></i>Reclamer Avance Salaire</a></li>
                    <li><a href="{{ route('validation-retrait') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-currency-usd mr-2"></i>Retirer Avance Salaire</a></li>
                    <li><a href="{{ route('message') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-message-text mr-2"></i>Messages privés et suggestions</a></li>
                    <li><a href="{{ route('message') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-alert mr-2"></i>Signalements</a></li>
                </ul>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="mt-auto border-t border-white/20 pt-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="mdi mdi-account-circle text-xl"></i>
                </div>
                <div>
                    <div class="font-medium">{{ $nom }}</div>
                    <div class="text-sm opacity-70">{{ $secteur }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Content Area -->
    <main class="flex-1 p-6 lg:ml-72">
        @yield('page-content')
    </main>
</div>
</body>
@endsection
