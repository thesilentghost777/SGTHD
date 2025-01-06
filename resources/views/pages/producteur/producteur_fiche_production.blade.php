{{-- resources/views/production/fiche.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Production</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
        }

        .stat-card {
            @apply bg-white rounded-lg shadow p-6 mb-4;
        }

        .stat-title {
            @apply text-xl font-bold text-gray-800 mb-2;
        }

        .stat-value {
            @apply text-2xl font-bold text-blue-600;
        }

        .chart-container {
            @apply bg-white rounded-lg shadow p-4 mb-6;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Fiche de Production</h1>
             <!-- Header -->
        <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Statistiques de Production</h1>
        <p class="text-gray-600">{{ $nom }} - {{ $secteur }}</p>
    </div>
            <button onclick="window.print()" class="no-print bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Imprimer le rapport
            </button>
        </div>

        <!-- Statistiques Maximales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <div class="stat-card">
                <div class="stat-title">Production Maximale</div>
                <div class="stat-value">{{ number_format($globalStats['max_production']['valeur'], 0) }}</div>
                <div class="text-gray-600">{{ $globalStats['max_production']['produit']['produit']['nom'] }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-title">Meilleur Bénéfice</div>
                <div class="stat-value">{{ number_format($globalStats['max_benefice']['valeur'], 0) }} F</div>
                <div class="text-gray-600">{{ $globalStats['max_benefice']['produit']['produit']['nom'] }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-title">Marge Moyenne</div>
                <div class="stat-value">{{ number_format($globalStats['moyenne_marge'], 1) }}%</div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Graphique Production Journalière -->
            <div class="chart-container">
                <h3 class="text-lg font-semibold mb-4">Production des 7 derniers jours</h3>
                <canvas id="dailyChart"></canvas>
            </div>

            <!-- Graphique Production Mensuelle -->
            <div class="chart-container">
                <h3 class="text-lg font-semibold mb-4">Production mensuelle</h3>
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Appréciations -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-xl font-bold mb-4">Appréciations</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="font-semibold text-gray-700">Rentabilité</p>
                    <p class="text-gray-600">{{ $appreciations['rentabilite'] }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Tendance</p>
                    <p class="text-gray-600">{{ $appreciations['tendance'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Configuration des graphiques
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');

        // Graphique journalier
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json($stats['daily']['labels']).reverse(),
                datasets: [{
                    label: 'Production journalière',
                    data: @json($stats['daily']['quantities']).reverse(),
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Graphique mensuel
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json($stats['monthly']['labels']).reverse(),
                datasets: [{
                    label: 'Production mensuelle',
                    data: @json($stats['monthly']['quantities']).reverse(),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
