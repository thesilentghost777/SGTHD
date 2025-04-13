<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse Production/Ventes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-blue: #1a56db;
            --secondary-blue: #3b82f6;
            --light-blue: #eff6ff;
            --accent-green: #10b981;
            --light-green: #d1fae5;
        }
        body {
            background-color: #f8fafc;
        }
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            background-color: white;
            transition: transform 0.3s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            background-color: var(--primary-blue);
            color: white;
            padding: 1rem;
        }
        .badge-success {
            background-color: var(--accent-green);
        }
        .badge-warning {
            background-color: #f59e0b;
        }
        .badge-danger {
            background-color: #ef4444;
        }
        .badge-info {
            background-color: var(--secondary-blue);
        }
        .badge-neutral {
            background-color: #6b7280;
        }
        .alert {
            border-left: 4px solid var(--primary-blue);
            background-color: var(--light-blue);
        }
        .progress-bar {
            height: 10px;
            border-radius: 5px;
            background-color: #e5e7eb;
            overflow: hidden;
        }
        .progress-value {
            height: 100%;
            border-radius: 5px;
        }
        .status-augmenter {
            background-color: var(--accent-green);
        }
        .status-maintenir {
            background-color: var(--secondary-blue);
        }
        .status-reduire {
            background-color: #f59e0b;
        }
        .status-optimiser {
            background-color: #8b5cf6;
        }
        .status-annuler {
            background-color: #ef4444;
        }
        .tab-active {
            background-color: var(--primary-blue);
            color: white;
        }
    </style>
</head>
<body class="min-h-screen">
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold">Analyse Production/Ventes</h1>
            <p class="mt-2 text-blue-100">Tableau de bord des statistiques et incohérences</p>
        </div>
    </header>

    <main class="container mx-auto py-8 px-4">
        <!-- Onglets de navigation -->
        <div class="flex flex-wrap mb-8 border-b border-gray-200">
            <button class="tab-active px-6 py-3 font-medium text-sm rounded-t-lg" onclick="openTab(event, 'resume')">
                Résumé
            </button>
            <button class="px-6 py-3 font-medium text-sm text-gray-600 hover:bg-gray-100 rounded-t-lg" onclick="openTab(event, 'evolution')">
                Évolution
            </button>
            <button class="px-6 py-3 font-medium text-sm text-gray-600 hover:bg-gray-100 rounded-t-lg" onclick="openTab(event, 'top-produits')">
                Top Produits
            </button>
            <button class="px-6 py-3 font-medium text-sm text-gray-600 hover:bg-gray-100 rounded-t-lg" onclick="openTab(event, 'alertes')">
                Alertes
            </button>
            <button class="px-6 py-3 font-medium text-sm text-gray-600 hover:bg-gray-100 rounded-t-lg" onclick="openTab(event, 'recommandations')">
                Recommandations
            </button>
        </div>

        <!-- Onglet Résumé -->
        <div id="resume" class="tab-content block">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Total Produits</h2>
                            <p class="text-2xl font-semibold text-gray-800">{{ count($ratioProduitsVendus) }}</p>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Profit Total</h2>
                            <p class="text-2xl font-semibold text-gray-800">
                                {{ number_format(array_sum(array_column($ratioProduitsVendus, 'profit')), 0, ',', ' ') }} XAF
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Ratio Moyen</h2>
                            <p class="text-2xl font-semibold text-gray-800">
                                {{ number_format(array_sum(array_column($ratioProduitsVendus, 'ratio')) / count($ratioProduitsVendus), 1, ',', ' ') }}%
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-gray-600 text-sm">Alertes</h2>
                            <p class="text-2xl font-semibold text-gray-800">{{ count($alertesProduits) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold">Top 5 - Meilleurs Ratios</h3>
                    </div>
                    <div class="p-4">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-600">Produit</th>
                                    <th class="px-4 py-2 text-right text-gray-600">Ratio</th>
                                    <th class="px-4 py-2 text-right text-gray-600">Vendu/Produit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topMeilleursRatios as $produit)
                                <tr class="border-b">
                                    <td class="px-4 py-3 text-gray-800">{{ $produit['nom'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="px-2 py-1 text-xs rounded-full text-white
                                            {{ $produit['ratio'] >= 90 ? 'badge-success' : 'badge-info' }}">
                                            {{ number_format($produit['ratio'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-800">
                                        {{ number_format($produit['quantite_vendue'], 0) }}/{{ number_format($produit['quantite_produite'], 0) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold">Top 5 - Faibles Ratios</h3>
                    </div>
                    <div class="p-4">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-600">Produit</th>
                                    <th class="px-4 py-2 text-right text-gray-600">Ratio</th>
                                    <th class="px-4 py-2 text-right text-gray-600">Vendu/Produit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPiresRatios as $produit)
                                <tr class="border-b">
                                    <td class="px-4 py-3 text-gray-800">{{ $produit['nom'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="px-2 py-1 text-xs rounded-full text-white
                                            {{ $produit['ratio'] < 50 ? 'badge-danger' : ($produit['ratio'] < 75 ? 'badge-warning' : 'badge-neutral') }}">
                                            {{ number_format($produit['ratio'], 1) }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-800">
                                        {{ number_format($produit['quantite_vendue'], 0) }}/{{ number_format($produit['quantite_produite'], 0) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold">Aperçu des Recommandations</h3>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($recommandationsProduits as $codeProduit => $recommandation)
                            <div class="border rounded p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-medium text-gray-800">{{ $recommandation['nom'] }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full text-white
                                        {{ $recommandation['statut'] == 'augmenter' ? 'badge-success' :
                                           ($recommandation['statut'] == 'maintenir' ? 'badge-info' :
                                           ($recommandation['statut'] == 'reduire' ? 'badge-warning' :
                                           ($recommandation['statut'] == 'optimiser' ? 'bg-purple-500' : 'badge-danger'))) }}">
                                        {{ ucfirst($recommandation['statut']) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $recommandation['message'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Évolution -->
        <div id="evolution" class="tab-content hidden">
            <div class="card mb-8">
                <div class="card-header">
                    <h3 class="text-lg font-semibold">Évolution du Ratio Produit/Vendu</h3>
                </div>
                <div class="p-4">
                    <div style="height: 400px;">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Top Produits -->
        <div id="top-produits" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold">Meilleurs Ratios Produit/Vendu</h3>
                    </div>
                    <div class="p-4" style="height: 400px;">
                        <canvas id="topRatiosChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold">Faibles Ratios Produit/Vendu</h3>
                    </div>
                    <div class="p-4" style="height: 400px;">
                        <canvas id="lowRatiosChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold">Tous les Produits</h3>
                </div>
                <div class="p-4 overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600">Produit</th>
                                <th class="px-4 py-2 text-right text-gray-600">Quantité Produite</th>
                                <th class="px-4 py-2 text-right text-gray-600">Quantité Vendue</th>
                                <th class="px-4 py-2 text-right text-gray-600">Ratio</th>
                                <th class="px-4 py-2 text-right text-gray-600">Coût Production</th>
                                <th class="px-4 py-2 text-right text-gray-600">Valeur Ventes</th>
                                <th class="px-4 py-2 text-right text-gray-600">Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ratioProduitsVendus as $produit)
                            <tr class="border-b">
                                <td class="px-4 py-3 text-gray-800">{{ $produit['nom'] }}</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ number_format($produit['quantite_produite'], 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ number_format($produit['quantite_vendue'], 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="px-2 py-1 text-xs rounded-full text-white
                                        {{ $produit['ratio'] >= 90 ? 'badge-success' :
                                           ($produit['ratio'] >= 75 ? 'badge-info' :
                                           ($produit['ratio'] >= 50 ? 'badge-warning' : 'badge-danger')) }}">
                                        {{ number_format($produit['ratio'], 1, ',', ' ') }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ number_format($produit['cout_production'], 0, ',', ' ') }} XAF</td>
                                <td class="px-4 py-3 text-right text-gray-800">{{ number_format($produit['valeur_ventes'], 0, ',', ' ') }} XAF</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="px-2 py-1 text-xs rounded-full text-white
                                        {{ $produit['profit'] > 0 ? 'badge-success' : 'badge-danger' }}">
                                        {{ number_format($produit['profit'], 0, ',', ' ') }} XAF
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Onglet Alertes -->
        <div id="alertes" class="tab-content hidden">
            <div class="card mb-8">
                <div class="card-header">
                    <h3 class="text-lg font-semibold">Alertes (Pertes > 5000 XAF)</h3>
                </div>
                <div class="p-4">
                    @if(count($alertesProduits) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($alertesProduits as $alerte)
                            <div class="alert p-4 rounded">
                                <div class="flex items-center">
                                    <div class="text-blue-600 mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-blue-800">{{ $alerte['produit'] }} - {{ $alerte['date'] }}</h4>
                                        <p class="text-sm text-blue-600">
                                            Invendus: {{ $alerte['invendus'] }} unités -
                                            Perte estimée: {{ number_format($alerte['perte'], 0, ',', ' ') }} XAF
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h4 class="text-lg font-medium">Aucune alerte</h4>
                            <p>Aucune perte importante n'a été détectée.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Onglet Recommandations -->
        <div id="recommandations" class="tab-content hidden">
            <div class="card mb-8">
                <div class="card-header">
                    <h3 class="text-lg font-semibold">Recommandations de Production</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($recommandationsProduits as $codeProduit => $recommandation)
                        <div class="border rounded p-4">
                            <h4 class="font-medium text-gray-800 mb-2">{{ $recommandation['nom'] }}</h4>
                            <div class="progress-bar mb-3">
                                <div class="progress-value status-{{ $recommandation['statut'] }}" style="width:
                                    {{ $recommandation['statut'] == 'augmenter' ? '100%' :
                                       ($recommandation['statut'] == 'maintenir' ? '80%' :
                                       ($recommandation['statut'] == 'reduire' ? '60%' :
                                       ($recommandation['statut'] == 'optimiser' ? '40%' : '20%'))) }}"></div>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Statut:</span>
                                <span class="text-sm font-medium
                                    {{ $recommandation['statut'] == 'augmenter' ? 'text-green-600' :
                                       ($recommandation['statut'] == 'maintenir' ? 'text-blue-600' :
                                       ($recommandation['statut'] == 'reduire' ? 'text-yellow-600' :
                                       ($recommandation['statut'] == 'optimiser' ? 'text-purple-600' : 'text-red-600'))) }}">
                                    {{ ucfirst($recommandation['statut']) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">{{ $recommandation['message'] }}</p>

                            @php
                                $produitIndex = false;
                                foreach ($ratioProduitsVendus as $index => $prod) {
                                    if ($prod['nom'] === $recommandation['nom']) {
                                        $produitIndex = $index;
                                        break;
                                    }
                                }
                                $produit = $produitIndex !== false ? $ratioProduitsVendus[$produitIndex] : null;
                            @endphp

                            @if($produit)
                            <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                                <div class="bg-gray-50 p-2 rounded">
                                    <span class="block text-gray-500">Ratio</span>
                                    <span class="font-medium">{{ number_format($produit['ratio'], 1) }}%</span>
                                </div>
                                <div class="bg-gray-50 p-2 rounded">
                                    <span class="block text-gray-500">Profit</span>
                                    <span class="font-medium
                                        {{ $produit['profit'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($produit['profit'], 0, ',', ' ') }} XAF
                                    </span>
                                </div>
                            </div>
                            @else
                            <div class="mt-4 bg-yellow-50 p-2 rounded text-sm text-yellow-700">
                                Données détaillées non disponibles
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-blue-900 text-white py-6">
        <div class="container mx-auto px-4 text-center">
            <p>© {{ date('Y') }} Easy Gest</p>
        </div>
    </footer>

    <script>
        // Fonction pour changer d'onglet
        function openTab(evt, tabName) {
            var i, tabContent, tabLinks;

            // Cacher tous les contenus d'onglets
            tabContent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }

            // Désactiver tous les boutons d'onglets
            tabLinks = document.getElementsByTagName("button");
            for (i = 0; i < tabLinks.length; i++) {
                tabLinks[i].className = tabLinks[i].className.replace("tab-active", "text-gray-600 hover:bg-gray-100");
            }

            // Afficher le contenu de l'onglet actif et activer le bouton
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className = evt.currentTarget.className.replace("text-gray-600 hover:bg-gray-100", "tab-active");
        }

        // Initialiser les graphiques une fois que la page est chargée
        document.addEventListener('DOMContentLoaded', function() {
            // Données pour le graphique d'évolution
            const evolutionData = {!! $dataEvolutionRatio !!};

            // Regrouper les données par date
            const dateGrouped = {};
            evolutionData.forEach(item => {
                if (!dateGrouped[item.date]) {
                    dateGrouped[item.date] = {};
                }
                dateGrouped[item.date][item.produit] = item.ratio;
            });

            // Extraire les dates et produits uniques
            const dates = Object.keys(dateGrouped).sort();
            const uniqueProducts = [...new Set(evolutionData.map(item => item.produit))];

            // Créer les datasets pour chaque produit
            const datasets = uniqueProducts.map((product, index) => {
                const colorIndex = index % 12;
                const colors = [
                    'rgb(26, 86, 219)', 'rgb(16, 185, 129)', 'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)', 'rgb(139, 92, 246)', 'rgb(59, 130, 246)',
                    'rgb(236, 72, 153)', 'rgb(16, 158, 207)', 'rgb(112, 163, 17)',
                    'rgb(255, 159, 64)', 'rgb(153, 102, 255)', 'rgb(201, 203, 207)'
                ];

                return {
                    label: product,
                    data: dates.map(date => dateGrouped[date][product] || 0),
                    borderColor: colors[colorIndex],
                    backgroundColor: colors[colorIndex] + '33',
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                };
            });

            // Créer le graphique d'évolution
            new Chart(document.getElementById('evolutionChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Ratio (%)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Évolution du Ratio Produit/Vendu'
                        }
                    }
                }
            });

            // Données pour les graphiques des top ratios
            const topRatios = {!! json_encode($topMeilleursRatios) !!};
            const lowRatios = {!! json_encode($topPiresRatios) !!};

            // Créer le graphique des meilleurs ratios
            new Chart(document.getElementById('topRatiosChart'), {
                type: 'bar',
                data: {
                    labels: topRatios.map(item => item.nom),
                    datasets: [{
                        label: 'Ratio (%)',
                        data: topRatios.map(item => item.ratio),
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Top 5 - Meilleurs Ratios Produit/Vendu'
                        }
                    }
                }
            });

            // Créer le graphique des faibles ratios
            new Chart(document.getElementById('lowRatiosChart'), {
                type: 'bar',
                data: {
                    labels: lowRatios.map(item => item.nom),
                    datasets: [{
                        label: 'Ratio (%)',
                        data: lowRatios.map(item => item.ratio),
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Top 5 - Faibles Ratios Produit/Vendu'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
