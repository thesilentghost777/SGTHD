@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TH Market Dashboard</title>

    <!-- External Resources -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .sidebar-scroll {
            height: calc(100vh - 180px); /* Ajustez cette valeur selon la hauteur de votre header et footer */
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }

        .main-content {
            height: 100vh;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans h-screen overflow-hidden">
    <div class="flex h-full" x-data="{ sidebarOpen: false }">
        <!-- Mobile Menu Button -->
        <button
            class="lg:hidden fixed z-50 top-4 left-4 p-4 text-white bg-blue-600 rounded-md shadow-md"
            @click="sidebarOpen = !sidebarOpen"
            aria-label="Toggle menu">
            <i class="mdi mdi-menu text-2xl"></i>
        </button>

        <!-- Sidebar -->
        <aside
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
            class="fixed inset-y-0 z-40 w-64 lg:w-72 transform transition-transform duration-300 ease-in-out lg:translate-x-0 bg-gradient-to-br from-blue-800 to-blue-600 text-white flex flex-col lg:static">

            <!-- Header fixe -->
            <div class="p-6">
                <div class="text-center border-b border-white/20 pb-6">
                    <h1 class="text-3xl font-bold">TH MARKET</h1>
                    <span class="text-base">Powered by SGc</span>
                </div>
            </div>

            <!-- Navigation avec défilement -->
            <nav class="sidebar-scroll flex-1 p-6">
                <!-- Supervision Section -->
                <div class="space-y-4 mb-8">
                    <h3 class="text-base font-semibold uppercase tracking-wider text-white/70">Supervision Générale</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('dg.workspace') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-monitor-dashboard mr-3 text-xl"></i>
                                Tableau de bord global
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Section des Statistiques -->
                <div class="space-y-4">
                    <h3 class="text-base font-semibold uppercase tracking-wider text-white/70">Statistiques</h3>
                    <ul class="space-y-3">
                        @foreach([
                            ['icon' => 'chart-bar', 'route' => 'statistiques.horaires', 'label' => 'Statistiques liées aux Employés/RH'],
                            ['icon' => 'factory', 'route' => 'statistiques.production', 'label' => 'Statistiques Production'],
                            ['icon' => 'cash', 'route' => 'statistiques.finance', 'label' => 'Statistiques Ventes/Finance'],
                            ['icon' => 'chart-pie', 'route' => 'statistiques.autres', 'label' => 'Autres Statistiques'],
                            ['icon' => 'truck-delivery', 'route' => 'statistiques.commande', 'label' => 'Statistiques Commandes et Sac'],
                            ['icon' => 'account-group', 'route' => 'statistiques.stagiere', 'label' => 'Statistiques Stagiere'],
                            ['icon' => 'currency-usd', 'route' => 'statistiques.argent', 'label' => 'Statistiques Salaire']
                        ] as $item)
                            <li>
                                <a href="{{ route($item['route']) }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                    <i class="mdi mdi-{{ $item['icon'] }} mr-3 text-xl"></i>
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Finance Section -->
                <div class="space-y-4">
                    <h3 class="text-base font-semibold uppercase tracking-wider text-white/70">Finances</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('solde') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-bank mr-3 text-xl"></i>
                                Solde entreprise
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('transactions.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-swap-horizontal mr-3 text-xl"></i>
                                Transactions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('bags.index2') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-bag-personal-outline mr-3 text-xl"></i>
                                Gestion des sacs
                            </a>
                        </li>

                    </ul>
                </div>

                <!-- Employee Management Section -->
                <div class="space-y-4">
                    <h3 class="text-base font-semibold uppercase tracking-wider text-white/70">Gestion employé</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('extras.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-file-document-outline mr-3 text-xl"></i>
                                Réglementation
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('delis.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-alert-circle-outline mr-3 text-xl"></i>
                                Infractions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('manquants.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-alert-circle-outline mr-3 text-xl"></i>
                                Gestion des Manquants
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('stagiaires.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-account-group mr-3 text-xl"></i>
                                Gestion stagiere
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('primes.create') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-cash-multiple mr-3 text-xl"></i>
                                Primes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('avance-salaires.dashboard') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-cash-fast mr-3 text-xl"></i>
                                Mini Stat Avance Salaire
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('valider-as') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-cash-fast mr-3 text-xl"></i>
                                Avance Salaire
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('valider-retraitcp') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-cash-remove mr-3 text-xl"></i>
                                Validation Retrait AS
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('salaires.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-currency-usd mr-3 text-xl"></i>
                                Salaire
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('loans.pending') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-currency-usd mr-3 text-xl"></i>
                                Gestion des emprunts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('versements.validation') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-currency-usd mr-3 text-xl"></i>
                                Validation des versements
                            </a>
                        </li>
                        <li><a href="{{ route('cash.distributions.index') }}" class="flex items-center p-2 rounded-lg hover:bg-white/10 transition-colors group">
                            <i class="mdi mdi-package-variant-closed-check mr-3 text-blue-200 group-hover:text-white"></i>
                            <span>Fermer une session de vente</span>
                        </a>
                        </li>
                        <li>
                            <a href="{{ route('employees.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-account-search mr-3 text-xl"></i>
                                Consultation et notation Employé
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('employees2') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                               <i class="mdi mdi-account-group mr-2"></i>Analyser vos Producteurs et leur performances
                            </a>
                        </li>
                        <a href="{{ route('incoherence.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                           <i class="mdi mdi-chart-box-outline mr-2"></i>Analyser vos Produits et leur performances
                        </a>
                       </li>
                        <li>
                            <a href="{{ route('employee.code_list') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-magnify mr-3 text-xl"></i>
                                    Consultation des codes d'enregistrement
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rations.admin.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                <i class="mdi mdi-account-group-outline mr-2 text-lg"></i>
                                <span>Gestion de la ration des employés</span>
                            </a>
                        </li>

                        <li><a href="{{ route('account-access.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-account-group mr-2"></i>Acceder aux comptes Employes</a></li>

                    </ul>
                </div>

                <!-- Communication Section -->
                <div class="space-y-4">
                    <h3 class="text-base font-semibold uppercase tracking-wider text-white/70">Communication</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('announcements.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-bullhorn-outline mr-3 text-xl"></i>
                                Annonce
                            </a>
                        </li>
                        <li class="flex items-center p-2 rounded hover:bg-white/10 cursor-pointer">
                            <a href="{{ route('recettes.index') }}" class="flex items-center">
                                <i class="mdi mdi-clipboard-list-outline mr-2 text-lg"></i>
                                <span>Recettes des produits</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('lecture_message') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-email-outline mr-3 text-xl"></i>
                                Voir Messages, plaintes et Suggestions
                            </a>
                        </li>

                    </ul>
                </div>

                <!-- Details section -->
                <div class="space-y-4">
                    <h3 class="text-base font-semibold uppercase tracking-wider text-white/70">Details</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('query.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-information-outline mr-3 text-xl"></i>
                                Details informatifs
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rapports.select') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-file-document-outline mr-3 text-xl"></i>
                                Rapports detailles
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('setup.edit') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-file-document-outline mr-3 text-xl"></i>
                                Modifier les infos relatives au complexe
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Sherlock Section -->
                <div class="space-y-4">
                    <h3 class="text-base font-semibold uppercase tracking-wider text-white/70">Sherlock</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('sherlock.copilot') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-magnify mr-3 text-xl"></i>
                                Sherlock Copilot
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('sherlock.conseiller') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-account-voice mr-3 text-xl"></i>
                                Conseiller Sherlock
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Incoherence Section -->
                <div class="space-y-4">
                    <h3 class="text-base font-semibold uppercase tracking-wider text-white/70">Incoherence</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ route('bag.discrepancies.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                <i class="mdi mdi-alert-circle-outline mr-3 text-xl"></i>
                                Incohérences au niveau des sacs
                            </a>
                        </li>


                    </ul>
                </div>

            </nav>

            <!-- Profile Section fixe -->
            <div class="p-6 border-t border-white/20">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center">
                        <i class="mdi mdi-account text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-lg font-medium">Mr {{ $staff['nom'] }}</div>
                        <div class="text-base text-white/70">Directeur Général</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 main-content lg:ml-22">
            <div class="p-6">
                @yield('page-content')
            </div>
        </main>
    </div>
</body>
</html>
@endsection