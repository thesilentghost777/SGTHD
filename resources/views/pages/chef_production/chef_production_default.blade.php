@extends('layouts.app')

@section('content')
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
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300"
                    :class="isChefMode ? 'bg-green-500' : 'bg-white/20'">
                    <span
                        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-300"
                        :class="isChefMode ? 'translate-x-6' : 'translate-x-1'">
                    </span>
                </button>
                <span :class="isChefMode ? 'opacity-100' : 'opacity-50'">Chef</span>
            </div>
        </div>

        <!-- Menu Sections -->
        <div class="mt-3 space-y-4 overflow-y-auto">
            <!-- Production Section -->
            <div>
                <h3 class="uppercase text-sm font-semibold opacity-70 mb-3">Production</h3>

                <!-- Liens pour Chef de Production -->
                <template x-if="isChefMode">
                    <div>
                        <!-- Section des Statistiques -->
                        <div class="space-y-4">
                            <h3 class="text-base font-semibold uppercase tracking-wider text-white/70">Statistiques</h3>
                            <ul class="space-y-3">
                                @foreach([
                                    ['icon' => 'factory', 'route' => 'statistiques.production', 'label' => 'Statistiques Production'],
                                    ['icon' => 'truck-delivery', 'route' => 'statistiques.commande', 'label' => 'Statistiques Commandes et Sac'],
                                    ['icon' => 'account-group', 'route' => 'statistiques.stagiere', 'label' => 'Statistiques Stagiere'],
                                    ['icon' => 'chart-bar', 'route' => 'matieres.complexe.statistiques', 'label' => 'Statistiques des matieres pris dans le complexe'],

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

                        <div class="space-y-2 mt-4">
                            <ul class="list-none">
                                <li>
                                    <a href="{{ route('production.chief.workspace') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                        <i class="mdi mdi-calendar-check-outline mr-2"></i>Assigner Production du jour
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('chef.assignations') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                        <i class="mdi mdi-calendar-check-outline mr-2"></i>Assigner Matiere du jour
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('assignations.resume-quantites') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                       <i class="mdi mdi-file-document-outline mr-2"></i>Voir resumer total des assignations
                                    </a>
                                </li>
                                <li class="flex items-center p-2 rounded hover:bg-white/10 cursor-pointer">
                                    <a href="{{ route('recettes.index') }}" class="flex items-center">
                                        <i class="mdi mdi-clipboard-list-outline mr-2 text-lg"></i>
                                        <span>Recettes des produits</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('employees2') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                       <i class="mdi mdi-account-group mr-2"></i>Analyser vos Producteurs et leur performances
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('incoherence.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                       <i class="mdi mdi-chart-box-outline mr-2"></i>Analyser vos Produits et leur performances
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('stock.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                        <i class="mdi mdi-warehouse mr-2"></i>Gestion Stocks
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('matieres.complexe.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                       <i class="mdi mdi-package-variant mr-2"></i>Définir les matières premières prises dans le complexe
                                    </a>
                                   </li>
                                   <li>
                                    <a href="{{ route('factures-complexe.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                       <i class="mdi mdi-file-document-outline mr-2"></i>Factures pour les matières prises dans le complexe
                                    </a>
                                   </li>
                                <li>
                                    <a href="{{ route('chef.reservations.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                         <i class="mdi mdi-calendar-account-outline mr-2"></i>Gérer réservation
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('manquant.create') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                        <i class="mdi mdi-file-cog-outline mr-2"></i>Facturer un manquant à un employé
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('solde-cp.index') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                        <i class="mdi mdi-wallet-outline mr-2"></i>Gerer le solde Jounalier
                                    </a>
                                </li>
                            </ul>
                        </div>
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
                            <li>
                                <a href="{{ route('extras.index2') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                    <i class="mdi mdi-gavel mr-2"></i> Réglementation
                                </a>
                            </li>
                            <li><a href="{{ route('manquant.create') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-alert-circle-outline mr-2"></i>Manquants</a></li>
                            <li><a href="{{ route('primes.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-gift-outline mr-2"></i>Primes</a></li>
                            <li><a href="{{ route('horaire.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-clock-time-four-outline mr-2"></i>Horaires</a></li>
                            <li><a href="{{ route('consulterfp') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-file-document-outline mr-2"></i>Fiche de paie</a></li>
                            <li>
                                <a href="{{ route('loans.my-loans') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                    <i class="mdi mdi-cash-multiple mr-2"></i> Effectuer un prêt
                                </a>
                            </li>

                        </div>
                    </template>

                    <!-- Liens pour Chef de Production -->
                    <template x-if="isChefMode">
                        <div class="space-y-2">
                            <li><a href="{{ route('producteur.lots') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-chart-areaspline-variant mr-2"></i>Statistique detailles production</a></li>
                            <li><a href="{{ route('ventes.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-chart-areaspline-variant mr-2"></i>Statistique detailles des ventes</a></li>
                            <li><a href="{{ route('chef.commandes.create') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-cart-outline mr-2"></i>Gestion Commande</a></li>
                            <li><a href="{{ route('depenses.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-cash-multiple mr-2"></i>Achat et Dépenses</a></li>
                            <li><a href="{{ route('bag.assignments.create') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-bag-checked mr-2"></i>Assigner les sacs au vendeuse</a></li>
                            <li><a href="{{ route('bag.recovery.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-bag-personal mr-2"></i>Recuperer les sacs invendu</a></li>
                            <li><a href="{{ route('taules.types.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-bag-personal mr-2"></i>Gestion des taules de production</a></li>


                        </div>
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
                            <a href="{{ route('employee.claim') }}" class="flex items-center p-2 rounded hover:bg-white/10">
                                <i class="mdi mdi-currency-usd mr-2 text-lg"></i> <!-- Remplacez mdi-coins par une icône valide -->
                                <span>Ration Journalière</span>
                            </a>
                        </div>
                    </template>

                    <!-- Liens pour Chef de Production -->
                    <template x-if="isChefMode">
                        <div class="space-y-2">
                            <li>
                                <a href="{{ route('announcements.index') }}" class="flex items-center px-3 py-3 text-base rounded-lg hover:bg-white/10 transition-colors">
                                    <i class="mdi mdi-bullhorn-outline mr-3 text-xl"></i>
                                    Annonce
                                </a>
                            </li>
                            <li><a href="{{ route('account-access.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-account-group mr-2"></i>Acceder aux comptes du personnel</a></li>
                            <li><a href="{{ route('repos-conges.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-calendar-check-outline mr-2"></i>Planning & repos</a></li>
                            <li><a href="{{ route('employees.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-chart-box-outline mr-2"></i>Évaluation employés</a></li>
                            <li><a href="{{ route('choix_classement') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-chart-box-outline mr-2"></i>Classement Employes</a></li>
                            <li><a href="{{ route('versements.index') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-wallet mr-2"></i>Versements</a></li>
                            <li><a href="{{ route('depenses.index2') }}" class="flex items-center p-2 rounded hover:bg-white/10"><i class="mdi mdi-truck-delivery-outline mr-2"></i>Livraisons</a></li>
                        </div>
                    </template>
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
                    <div class="font-medium">{{ $nom ?? 'Utilisateur' }}</div>
                    <div class="text-sm opacity-70">{{ $role ?? 'Rôle non défini' }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Content Area -->
    <main class="flex-1 p-3 lg:ml-7">
        @yield('page-content')
    </main>
</div>
@endsection
