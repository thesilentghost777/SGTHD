<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Boulangerie Pâtisserie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&family=Caveat:wght@400;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- En-tête avec logo -->
        <header class="pt-4 px-8">
            <div class="max-w-7xl mx-auto flex items-center">
                <img src="{{ asset('assets/logos/TH_LOGO.png') }}" alt="TH Logo" class="h-40 w-auto">
                <div class="border-b-2 border-black py-2">
                    <p class="ml-12 font-['Poppins'] text-2xl leading-loose">
                   <span class="font-bold text-red-600">TH MARKET</span> :
                   <span class="text-blue-900">Boulangerie</span>
                   <span class="text-blue-700">Patisserie</span>
                   <span class="text-blue-500">Alimentation</span>
                   <span class="text-blue-400">Snack</span>
                   <span class="text-blue-300">Restaurant</span>
                   </p>
                   </div>
            </div>
        </header>

        <!-- Navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('dashboard') }}" class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Accueil
                    </a>
                    <a href="{{ route('workspace.redirect') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Espace de travail
                    </a>
                    <a href="{{ route('about') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        À propos
                    </a>
                </nav>
            </div>
        </div>

        <!-- Contenu principal -->
        <main class="flex-1 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section d'accueil -->
                <section id="home" class="space-y-6">
                    <!-- Bannière de bienvenue -->
                    <div class="bg-gradient-to-r from-blue-600 to-green-400 rounded-lg shadow-xl p-6 sm:p-10">
                        <div class="max-w-3xl">
                            <h2 class="text-3xl font-bold text-white mb-2">Bienvenue sur votre espace de travail</h2>
                            <p class="text-blue-50">Gérez efficacement votre activité de boulangerie-pâtisserie</p>
                        </div>
                    </div>

                    <!-- Description de l'application -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <h2 class="text-xl font-semibold text-gray-900 underline">Application de gestion et contrôle complet de votre Boulangerie-Pâtisserie</h2>
                            <p class="mt-2 text-gray-600">
                                Cette application permet de gérer efficacement la production, les ventes, les gains, pertes, les commandes, et le stock des matières premières dans une boulangerie-pâtisserie. Grâce à ses fonctionnalités avancées, elle aide les propriétaires et les employés à optimiser leurs processus et à réduire les erreurs humaines. Elle fournit également des outils de suivi des ventes et des performances en temps réel.
                            </p>
                        </div>
                    </div>

                    <!-- Problèmes que l'application résout -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mt-6">
                        <!-- Problème 1: Suivi de la production -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dt class="text-sm font-semibold text-gray-900 truncate">Suivi de la production</dt>
                                        <dd class="text-base text-gray-500">
                                            L'application permet de suivre en temps réel la production quotidienne, garantissant que les quantités nécessaires sont fabriquées et disponibles à la vente.
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Problème 2: Gestion des commandes en attente -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dt class="text-sm font-semibold text-gray-900 truncate">Gestion des commandes en attente</dt>
                                        <dd class="text-base text-gray-500">
                                            L'application permet de suivre l'état des commandes en attente et d'organiser les priorités, ce qui réduit les risques de retards et améliore la satisfaction des clients.
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Problème 3: Gestion du stock des matières premières -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-blue-400 rounded-md p-3">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dt class="text-sm font-semibold text-gray-900 truncate">Gestion du stock des matières premières</dt>
                                        <dd class="text-base text-gray-500">
                                            L'application permet une gestion efficace des stocks de matières premières, évitant les pénuries et le gaspillage en optimisant les réapprovisionnements.
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        // Gestion des onglets
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('nav a');
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    if (!this.getAttribute('href').startsWith('http')) {
                        e.preventDefault();
                        tabs.forEach(t => t.classList.remove('border-blue-500', 'text-blue-600'));
                        tabs.forEach(t => t.classList.add('border-transparent', 'text-gray-500'));
                        this.classList.remove('border-transparent', 'text-gray-500');
                        this.classList.add('border-blue-500', 'text-blue-600');
                    }
                });
            });
        });
    </script>
</body>
</html>