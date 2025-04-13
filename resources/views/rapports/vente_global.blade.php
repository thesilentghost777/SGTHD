@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.js"></script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Rapport des Ventes - {{ $currentMonthStart->locale('fr')->format('F Y') }}</h1>

        <button onclick="printReport()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded flex items-center">
            <i class="fas fa-print mr-2"></i> Imprimer le rapport
        </button>
    </div>

    <div id="report-content" class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- En-tête du rapport -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-6 px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h2 class="text-xl font-bold">Rapport Mensuel des Ventes</h2>
                    <p class="text-blue-100">Période: {{ $currentMonthStart->format('d/m/Y') }} - {{ $currentMonthStart->copy()->endOfMonth()->format('d/m/Y') }}</p>
                </div>
                <div class="md:text-right">
                    <p class="text-xl font-bold">{{ number_format($currentMonthSales, 0, ',', ' ') }} XAF</p>
                    <p class="text-blue-100">Chiffre d'affaires total</p>
                </div>
            </div>
        </div>

        <!-- Statistiques générales -->
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Évolution des ventes -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Évolution des ventes</h3>
                    <div class="flex items-end">
                        <div class="text-3xl font-bold {{ $evolution >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $evolution >= 0 ? '+' : '' }}{{ $evolution }}%
                        </div>
                        <div class="ml-2 text-sm text-gray-500">par rapport au mois précédent</div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        Le chiffre d'affaires a {{ $evolution >= 0 ? 'augmenté' : 'diminué' }} de {{ abs($evolution) }}% par rapport au mois de {{ Carbon\Carbon::now()->subMonth()->locale('fr')->format('F Y') }}.
                    </div>
                </div>

                <!-- Pertes -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Pertes totales</h3>
                    <div class="flex items-end">
                        <div class="text-3xl font-bold text-red-600">
                            {{ number_format($losses, 0, ',', ' ') }} XAF
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        Les pertes dues aux produits avariés représentent {{ $currentMonthSales > 0 ? round(($losses / ($currentMonthSales + $losses)) * 100, 2) : 0 }}% du chiffre d'affaires potentiel.
                    </div>
                </div>

                <!-- Statistiques de transactions -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Transactions</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-xl font-semibold">{{ $totalTransactions }}</span>
                            <span class="text-gray-600 ml-2">transactions totales</span>
                        </div>
                        <div>
                            <span class="text-xl font-semibold">{{ number_format($averageTransactionValue, 0, ',', ' ') }} XAF</span>
                            <span class="text-gray-600 ml-2">valeur moyenne</span>
                        </div>
                    </div>
                </div>
            </div>

           <!-- Graphiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Évolution journalière -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Évolution journalière des ventes</h3>
                    <div class="h-60">
                        <canvas id="dailySalesChart"></canvas>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">
                        Graphique représentant l'évolution journalière du chiffre d'affaires pour le mois de {{ Carbon\Carbon::now()->locale('fr')->format('F Y') }}.
                    </p>
                </div>

                <!-- Répartition des ventes par produit -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Répartition des ventes par produit</h3>
                    <div class="h-60">
                        <canvas id="productRevenueChart"></canvas>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">
                        Graphique représentant la répartition du chiffre d'affaires par produit pour le mois de {{ Carbon\Carbon::now()->locale('fr')->format('F Y') }}.
                    </p>
                </div>
            </div>

            <!-- Top 3 des vendeurs -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Top 3 des vendeurs</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($topSellers as $index => $seller)
                        <div class="bg-white border rounded-lg overflow-hidden shadow-md">
                            <div class="p-6 {{ $index === 0 ? 'bg-gradient-to-r from-blue-500 to-blue-400' : ($index === 1 ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : 'bg-gradient-to-r from-purple-500 to-purple-400') }}">
                                <div class="flex justify-between items-start">
                                    <div class="text-3xl font-bold text-white">#{{ $index + 1 }}</div>
                                    <div class="bg-white rounded-full h-12 w-12 flex items-center justify-center shadow-md">
                                        <i class="fas fa-trophy text-2xl {{ $index === 0 ? 'text-blue-500' : ($index === 1 ? 'text-emerald-500' : 'text-purple-500') }}"></i>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h4 class="text-lg font-bold text-white">{{ optional($seller->vendeur)->name ?? 'Vendeur inconnu' }}</h4>
                                    <p class="text-white text-opacity-90">{{ number_format($seller->revenue, 0, ',', ' ') }} XAF</p>
                                    <p class="mt-1 text-white text-opacity-80 text-sm">{{ $seller->total_sales }} ventes</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Dernières transactions -->
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Transactions récentes</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Produit</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Vendeur</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Quantité</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentTransactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($transaction->date_vente)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">
                                        @php
                                            $produitInfo = $transaction->produit()->first();
                                        @endphp
                                        {{ $produitInfo ? $produitInfo->nom : 'Produit inconnu' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $transaction->vendeur ? $transaction->vendeur->name : 'Vendeur inconnu' }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $transaction->quantite }}</td>
                                    <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ number_format($transaction->quantite * $transaction->prix, 0, ',', ' ') }} XAF</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Conclusion du rapport -->
            <div class="mt-8 p-6 bg-blue-50 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Conclusion</h3>
                <p class="text-blue-700">
                    @if($evolution >= 0)
                        Pour le mois de {{ $currentMonthStart->locale('fr')->format('F Y') }}, l'entreprise a enregistré un chiffre d'affaires de {{ number_format($currentMonthSales, 0, ',', ' ') }} XAF,
                        représentant une augmentation de {{ $evolution }}% par rapport au mois précédent.
                    @else
                        Pour le mois de {{ $currentMonthStart->locale('fr')->format('F Y') }}, l'entreprise a enregistré un chiffre d'affaires de {{ number_format($currentMonthSales, 0, ',', ' ') }} XAF,
                        représentant une baisse de {{ abs($evolution) }}% par rapport au mois précédent.
                    @endif

                    @if($losses > 0)
                        Les pertes dues aux produits avariés s'élèvent à {{ number_format($losses, 0, ',', ' ') }} XAF, ce qui représente
                        {{ $currentMonthSales > 0 ? round(($losses / ($currentMonthSales + $losses)) * 100, 2) : 0 }}% du chiffre d'affaires potentiel.
                    @else
                        Aucune perte due à des produits avariés n'a été enregistrée ce mois-ci.
                    @endif
                </p>
            </div>

            <!-- Note de bas de page -->
            <div class="mt-8 text-center text-sm text-gray-500">
                <p>Rapport généré le {{ \Carbon\Carbon::now()->locale('fr')->format('d/m/Y à H:i') }}</p>
                <p>© {{ date('Y') }} - Tous droits réservés</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des données pour le graphique des ventes journalières
    const dailyData = @json($dailySales);

    if (document.getElementById('dailySalesChart')) {
        new Chart(document.getElementById('dailySalesChart'), {
            type: 'line',
            data: {
                labels: dailyData.map(item => item.date),
                datasets: [{
                    label: 'Ventes journalières',
                    data: dailyData.map(item => item.amount),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('fr-FR') + ' XAF';
                            }
                        }
                    }
                }
            }
        });
    }

    // Configuration des données pour le graphique de répartition des produits
    let productData = @json($productRevenueData);

    // Vérification que productData est un tableau et n'est pas vide
    if (!Array.isArray(productData)) {
        productData = [];
    }

    if (productData.length === 0) {
        productData = [{label: 'Aucune donnée', value: 1}];
    }

    if (document.getElementById('productRevenueChart')) {
        new Chart(document.getElementById('productRevenueChart'), {
            type: 'doughnut',
            data: {
                labels: productData.map(item => item.label),
                datasets: [{
                    data: productData.map(item => item.value),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
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
    }
});

// Fonction d'impression
function printReport() {
    const printContents = document.getElementById('report-content').innerHTML;
    const originalContents = document.body.innerHTML;

    document.body.innerHTML = `
        <style>
            @media print {
                body {
                    font-family: 'Helvetica', 'Arial', sans-serif;
                    color: #333;
                }
                @page {
                    size: A4;
                    margin: 1cm;
                }
                button, .no-print {
                    display: none !important;
                }
            }
        </style>
        <div class="print-container">${printContents}</div>
    `;

    window.print();
    document.body.innerHTML = originalContents;

    // Recharger les graphiques après l'impression
    location.reload();
}
</script>
@endpush
@endsection
