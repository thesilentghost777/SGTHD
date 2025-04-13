@extends('layouts.app')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TH Market Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <!-- Mobile menu button -->
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden fixed z-50 top-4 left-4 p-2 rounded-md bg-blue-600 text-white shadow-lg">
            <i class="mdi mdi-menu text-2xl"></i>
        </button>

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-40 w-72 bg-gradient-to-br from-blue-800 to-blue-600 text-white transform transition-transform duration-300 ease-in-out overflow-y-auto">

            <!-- Logo -->
            <div class="px-6 py-8 border-b border-white/20">
                <h1 class="text-2xl font-bold text-center">TH MARKET</h1>
                <p class="text-xs text-center text-blue-100">Powered by SGc</p>
            </div>

            <!-- Menu Items -->
            <nav class="p-6 space-y-8">
                <!-- Ventes Section -->
                <div>
                    <h3 class="text-xs font-semibold tracking-wider uppercase text-blue-100 mb-3">Ventes</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('seller.workspace') }}" class="flex items-center p-2 rounded-lg hover:bg-white/10 transition-colors group">
                               <i class="mdi mdi-view-dashboard mr-3 text-blue-200 group-hover:text-white"></i>
                               <span>Dashboard</span>
                            </a>
                           </li>
                           <li>
                            <a href="{{ route('cash.distributions.index') }}" class="flex items-center p-2 rounded-lg hover:bg-white/10 transition-colors group">
                                <i class="mdi mdi-package-variant-closed-check mr-3 text-blue-200 group-hover:text-white"></i>
                                <span>Demarrer une session de vente</span>
                              </a>
                           </li>
                           <li>
                            <a href="{{ route('serveur-ajouterProduit_recu') }}" class="flex items-center p-2 rounded-lg hover:bg-white/10 transition-colors group">
                               <i class="mdi mdi-package-variant-closed-check mr-3 text-blue-200 group-hover:text-white"></i>
                               <span>Produits reçus</span>
                            </a>
                           </li>
                        <li>
                            <a href="{{ route('serveur-produit_invendu') }}" class="flex items-center p-2 rounded-lg hover:bg-white/10 transition-colors group">
                               <i class="mdi mdi-chart-line mr-3 text-blue-200 group-hover:text-white"></i>
                               <span>Ventes du jour</span>
                            </a>
                           </li>
                           <li>
                            <a href="{{ route('serveur-nbre_sacs_vente') }}" class="flex items-center p-2 rounded-lg hover:bg-white/10 transition-colors group">
                               <i class="mdi mdi-package-variant mr-3 text-blue-200 group-hover:text-white"></i>
                               <span>Sac et contenant</span>
                            </a>
                           </li>
                           <li>
                            <a href="{{ route('versements.index') }}" class="flex items-center p-2 rounded-lg hover:bg-white/10 transition-colors group">
                               <i class="mdi mdi-bank-transfer mr-3 text-blue-200 group-hover:text-white"></i>
                               <span>Versement</span>
                            </a>
                           </li>
                    </ul>
                </div>

                <!-- Général Section -->
                <div>
                    <h3 class="text-xs font-semibold tracking-wider uppercase text-blue-100 mb-3">Général</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('serveur-stats') }}" class="flex items-center p-2 rounded-lg hover:bg-white/10 transition-colors group">
                                <i class="mdi mdi-chart-bar mr-3 text-blue-200 group-hover:text-white"></i>
                                <span>Statistiques</span>
                            </a>
                        </li>
                        <li><a href="{{ route('producteur.lots') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-eye mr-2"></i>Details des Ventes</a></li>
                        <li><a href="{{ route('manquant.view') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-alert-circle mr-2"></i>Manquants</a></li>
                        <li><a href="{{ route('manquant.mes-deductions') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-alert-circle mr-2"></i>Montant a deduire au salaire</a></li>
                        <li><a href="{{ route('primes.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-gift mr-2"></i>Primes</a></li>
                        <li>
                            <a href="{{ route('loans.my-loans') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                <i class="mdi mdi-cash-multiple mr-2"></i> Effectuer un prêt
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('employee.claim') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                <i class="mdi mdi-currency-usd mr-2 text-lg"></i> <!-- Remplacez mdi-coins par une icône valide -->
                                <span>Ration Journalière</span>
                            </a>
                        </li>

                        <li><a href="{{ route('horaire.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-clock-check mr-2"></i>Horaires</a></li>
                        <li><a href="{{ route('consulterfp') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-file-document-multiple mr-2"></i>Fiche de paie</a></li>
                        <li><a href="{{ route('producteur.comparaison') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-podium mr-2"></i>Classement</a></li>

                    </ul>
                </div>

                <!-- Communications Section -->
                <div>
                    <h3 class="text-xs font-semibold tracking-wider uppercase text-blue-100 mb-3">Communications</h3>
                    <ul class="space-y-2">
                        <li>
                            <li>
                                <a href="{{ route('extras.index2') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                    <i class="mdi mdi-gavel mr-2"></i> Réglementation
                                </a>
                            </li>
                            <li><a href="{{ route('reclamer-as') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-cash mr-2"></i>Reclamer Avance Salaire</a></li>
                            <li><a href="{{ route('validation-retrait') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-currency-usd mr-2"></i>Retirer Avance Salaire</a></li>
                            <li><a href="{{ route('message') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-message-text mr-2"></i>Messages privés et suggestions</a></li>
                            <li><a href="{{ route('message') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-alert mr-2"></i>Signalements</a></li>

                        </li>
                        <!-- Autres items du menu communications... -->
                    </ul>
                </div>
            </nav>

           <!-- Profile Section -->
        <div class="mt-auto border-t border-white/20 pt-6">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="mdi mdi-account-circle text-xl"></i>
                </div>
                <div>
                    <div class="font-medium">{{ $nom }}</div>
                    <div class="text-sm opacity-70">Vendeur(se)</div>
                </div>
            </div>
        </div>
        </aside>

        <!-- Main Content -->
        <main class="p-15" style="margin-left: 300px;">
            <div class="container mx-auto">
                @yield('page-content')
            </div>
        </main>
    </div>
</body>
</html>
@endsection
