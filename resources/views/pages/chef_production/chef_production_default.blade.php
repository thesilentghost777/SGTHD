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
<div class="flex min-h-screen" x-data="{ sidebarOpen: false, isChefMode: true }">
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

            <!-- Toggle Button -->
            <div class="mt-4 flex items-center justify-center space-x-2">
                <span :class="!isChefMode ? 'opacity-100' : 'opacity-50'">Employé</span>
                <button
                    @click="isChefMode = !isChefMode"
                    class="relative inline-flex h-6 w-11 items-center rounded-full bg-white/20 transition-colors duration-300"
                    :class="{ 'bg-green-500': isChefMode }">
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-300"
                        :class="{ 'translate-x-6': isChefMode, 'translate-x-1': !isChefMode }">
                    </span>
                </button>
                <span :class="isChefMode ? 'opacity-100' : 'opacity-50'">Chef</span>
            </div>
        </div>

        <!-- Menu Sections -->
        <div class="mt-3 space-y-4">
        <!-- Production Section -->
<h3 class="uppercase text-sm font-semibold opacity-70 mb-3">Production</h3>
<div>
    <!-- Liens pour Chef de Production -->
    <template x-if="isChefMode">
        <div class="space-y-2">
            <ul class="list-none">
                <li>
                    <a href="{{ route('chef.dashboard') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                        <i class="mdi mdi-calendar-check-outline mr-2"></i>Assigner matière du jour
                    </a>
                </li>
                <li>
                    <a href="{{ route('stock.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                        <i class="mdi mdi-warehouse mr-2"></i>Gestion Stocks
                    </a>
                </li>
                <li>
                    <a href="{{ route('chef.reservations.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                         <i class="mdi mdi-calendar-account-outline mr-2"></i>Gérer réservation
                    </a>
                </li>
                <li>
                    <a class="flex items-center p-2 rounded hover:bg-white/10">
                        <i class="mdi mdi-account-cog-outline mr-2"></i>Équipes de production
                    </a>
                </li>
                <li>
                    <a href="{{ route('manquant.create') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                        <i class="mdi mdi-file-cog-outline mr-2"></i>Facturer un manquant à un producteur
                    </a>
                </li>
            </ul>
        </div>
    </template>
</div>

<!-- General Section -->
<div>
    <h3 class="uppercase text-sm font-semibold opacity-70 mb-3">Général</h3>
    <ul class="space-y-2">
        <!-- Liens pour Employé -->
        <template x-if="!isChefMode">
            <div class="space-y-2">
                <li><a href="{{ route('manquant') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-alert-circle-outline mr-2"></i>Manquants</a></li>
                <li><a href="{{ route('primes.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-gift-outline mr-2"></i>Primes</a></li>
                <li><a href="{{ route('horaire.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-clock-time-four-outline mr-2"></i>Horaires</a></li>
                <li><a href="{{ route('fiche-paie.show') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-file-document-outline mr-2"></i>Fiche de paie</a></li>
            </div>
        </template>

        <!-- Liens pour Chef de Production -->
        <template x-if="isChefMode">
            <div class="space-y-2">
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-chart-areaspline-variant mr-2"></i>Statistique detailles production</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-delete-alert-outline mr-2"></i>Pertes/Gaspillage</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-checkbox-marked-outline mr-2"></i>Rendement</a></li>
                <li><a href="{{ route('chef.commandes.create') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-cart-outline mr-2"></i>Gestion Commande</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-cash-multiple mr-2"></i>Achat et Dépenses</a></li>
        </template>
    </ul>
</div>

<!-- Communications Section -->
<div>
    <h3 class="uppercase text-sm font-semibold opacity-70 mb-3">Administration</h3>
    <ul class="space-y-2">
        <!-- Liens pour Employé -->
        <template x-if="!isChefMode">
            <div class="space-y-2">
                <li><a href="{{ route('reclamer-as') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-currency-usd-off mr-2"></i>Reclamer Avance Salaire</a></li>
                <li><a href="{{ route('validation-retrait') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-cash-refund mr-2"></i>Retirer Avance Salaire</a></li>
                <li><a href="{{ route('message') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-email-outline mr-2"></i>Messages privés</a></li>
            </div>
        </template>

        <!-- Liens pour Chef de Production -->
        <template x-if="isChefMode">
            <div class="space-y-2">
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-bullhorn-outline mr-2"></i>Annonces</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-check-circle-outline mr-2"></i>Validations</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-account-multiple-outline mr-2"></i>Réunions</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-account-tie-outline mr-2"></i>Gestion employés</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-account-outline mr-2"></i>Gestion stagiaires</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-calendar-check-outline mr-2"></i>Planning & repos</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-chart-box-outline mr-2"></i>Évaluation employés</a></li>
            </div>
        </template>

        <template x-if="isChefMode">
            <div class="space-y-2">
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-file-chart-outline mr-2"></i>Rapports</a></li>
                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-wallet mr-2"></i>Versements</a></li>                <li><a href="." class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-truck-delivery-outline mr-2"></i>Livraisons</a></li>
            </div>
        </template>
    </ul>
</div>

        <!-- Profile Section -->
        <div class="mt-auto border-t border-white/20 pt-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="mdi mdi-account-circle text-xl"></i>
                </div>
                <div>
                    <div class="font-medium">{{ $nom }}</div>
                    <div class="text-sm opacity-70">{{ $role }}</div>
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
