<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques Financières</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center tracking-wide">
            📊 Tableau de Bord Financier
        </h1>

        <!-- Cartes des statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Statistiques journalières -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Aujourd'hui</h3>
                <div class="space-y-2">
                    <p class="text-sm text-gray-600">Revenus: <span class="font-semibold text-green-600">{{ number_format($statsJour->revenus, 2) }} XAF</span></p>
                    <p class="text-sm text-gray-600">Dépenses: <span class="font-semibold text-red-600">{{ number_format($statsJour->depenses, 2) }} XAF</span></p>
                    <p class="text-sm text-gray-600">Solde: <span class="font-semibold {{ $statsJour->solde >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($statsJour->solde, 2) }} XAF</span></p>
                </div>
            </div>

            <!-- Statistiques hebdomadaires -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Cette semaine</h3>
                <div class="space-y-2">
                    <p class="text-sm text-gray-600">Revenus: <span class="font-semibold text-green-600">{{ number_format($statsHebdo->revenus, 2) }} XAF</span></p>
                    <p class="text-sm text-gray-600">Dépenses: <span class="font-semibold text-red-600">{{ number_format($statsHebdo->depenses, 2) }} XAF</span></p>
                    <p class="text-sm text-gray-600">Solde: <span class="font-semibold {{ $statsHebdo->solde >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($statsHebdo->solde, 2) }} XAF</span></p>
                </div>
            </div>

            <!-- Statistiques mensuelles -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Ce mois</h3>
                <div class="space-y-2">
                    <p class="text-sm text-gray-600">Revenus: <span class="font-semibold text-green-600">{{ number_format($statsMois->revenus, 2) }} XAF</span></p>
                    <p class="text-sm text-gray-600">Dépenses: <span class="font-semibold text-red-600">{{ number_format($statsMois->depenses, 2) }} XAF</span></p>
                    <p class="text-sm text-gray-600">Solde: <span class="font-semibold {{ $statsMois->solde >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($statsMois->solde, 2) }} XAF</span></p>
                </div>
            </div>

            <!-- Statistiques annuelles -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Cette année</h3>
                <div class="space-y-2">
                    <p class="text-sm text-gray-600">Revenus: <span class="font-semibold text-green-600">{{ number_format($statsAnnee->revenus, 2) }} XAF</span></p>
                    <p class="text-sm text-gray-600">Dépenses: <span class="font-semibold text-red-600">{{ number_format($statsAnnee->depenses, 2) }} XAF</span></p>
                    <p class="text-sm text-gray-600">Solde: <span class="font-semibold {{ $statsAnnee->solde >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($statsAnnee->solde, 2) }} XAF</span></p>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Evolution mensuelle -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Évolution mensuelle</h3>
                <div class="h-[400px]">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>

            <!-- Répartition des dépenses par catégorie -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Répartition des dépenses par catégorie</h3>
                <div class="h-[400px]">
                    <canvas id="depensesChart"></canvas>
                </div>
            </div>

            <!-- Évolution journalière -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Évolution journalière du mois en cours</h3>
                <div class="h-[400px]">
                    <canvas id="evolutionJournaliereChart"></canvas>
                </div>
            </div>

            <!-- Ratio dépenses/revenus -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Ratio dépenses/revenus du mois</h3>
                <div class="flex items-center justify-center h-64">
                    <div class="text-center">
                        <div class="text-4xl font-bold {{ $ratio->ratio <= 100 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $ratio->ratio }}%
                        </div>
                        <p class="text-sm text-gray-600 mt-2">des revenus sont dépensés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 des dépenses -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Top 5 des dépenses</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topDepenses as $depense)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($depense->date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $depense->category->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $depense->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                {{ number_format($depense->amount, 2) }} XAF
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Configuration du graphique d'évolution mensuelle
        const ctx = document.getElementById('evolutionChart').getContext('2d');
        const evolutionData = @json($evolutionMensuelle);

        const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                       'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: evolutionData.map(d => months[d.mois - 1]),
                datasets: [
                    {
                        label: 'Revenus',
                        data: evolutionData.map(d => d.revenus),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Dépenses',
                        data: evolutionData.map(d => d.depenses),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Solde',
                        data: evolutionData.map(d => d.solde),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Configuration du graphique des dépenses par catégorie
        const ctxDepenses = document.getElementById('depensesChart').getContext('2d');
        const depensesData = @json($depensesParCategorie);

        new Chart(ctxDepenses, {
            type: 'pie',
            data: {
                labels: Object.keys(depensesData),
                datasets: [{
                    data: Object.values(depensesData),
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',
                        'rgb(99, 255, 132)',
                        'rgb(162, 235, 54)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });

        // Configuration du graphique d'évolution journalière
        const ctxJournalier = document.getElementById('evolutionJournaliereChart').getContext('2d');
        const evolutionJournaliereData = @json($evolutionJournaliere);

        new Chart(ctxJournalier, {
            type: 'line',
            data: {
                labels: evolutionJournaliereData.map(d => new Date(d.jour).toLocaleDateString()),
                datasets: [
                    {
                        label: 'Revenus',
                        data: evolutionJournaliereData.map(d => d.revenus),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Dépenses',
                        data: evolutionJournaliereData.map(d => d.depenses),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
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
