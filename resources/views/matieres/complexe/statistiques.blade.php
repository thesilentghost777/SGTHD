@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Statistiques des Matières du Complexe</h1>
        <div class="flex">
            <a href="{{ route('matieres.complexe.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Retour à la liste
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filtres de période -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Sélectionner la période</h2>
        <form action="{{ route('matieres.complexe.statistiques') }}" method="GET" class="flex space-x-4">
            <div class="flex items-center space-x-2">
                <input type="radio" id="jour" name="periode" value="jour" {{ $periode == 'jour' ? 'checked' : '' }} class="h-4 w-4 text-blue-600">
                <label for="jour" class="text-sm font-medium text-gray-700">Aujourd'hui</label>
            </div>
            <div class="flex items-center space-x-2">
                <input type="radio" id="semaine" name="periode" value="semaine" {{ $periode == 'semaine' ? 'checked' : '' }} class="h-4 w-4 text-blue-600">
                <label for="semaine" class="text-sm font-medium text-gray-700">Cette semaine</label>
            </div>
            <div class="flex items-center space-x-2">
                <input type="radio" id="mois" name="periode" value="mois" {{ $periode == 'mois' ? 'checked' : '' }} class="h-4 w-4 text-blue-600">
                <label for="mois" class="text-sm font-medium text-gray-700">Ce mois</label>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-4 rounded">
                Filtrer
            </button>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">
                Récapitulatif des matières du complexe
                ({{ $periode == 'jour' ? 'Aujourd\'hui' : ($periode == 'semaine' ? 'Cette semaine' : 'Ce mois') }})
            </h2>
            <p class="text-sm text-gray-600 mb-4">
                Période: du {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
            </p>

            @if(count($statistiques) > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité Totale</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unité</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($statistiques as $stat)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $stat->nom }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900">{{ number_format($stat->quantite_totale, 3, ',', ' ') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $stat->unite_minimale }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($stat->montant_total, 0, ',', ' ') }} FCFA</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-right text-lg font-bold text-gray-900">Total</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-lg font-bold text-gray-900">{{ number_format($montantTotal, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div class="bg-blue-50 p-4 rounded">
                    <p class="text-blue-700">Aucune donnée disponible pour cette période.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Graphique circulaire -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Répartition par matière</h2>
                @if(count($dataGraphique) > 0)
                    <div id="pie-chart-container" style="height: 300px;">
                        <canvas id="pieChart"></canvas>
                    </div>
                @else
                    <div class="bg-gray-100 h-80 flex items-center justify-center">
                        <p class="text-gray-500">Aucune donnée disponible pour cette période.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Graphique d'évolution temporelle -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Évolution sur la période</h2>
                @if(count($evolutionTemporelle) > 0)
                    <div id="time-series-container" style="height: 300px;">
                        <canvas id="timeSeriesChart"></canvas>
                    </div>
                @else
                    <div class="bg-gray-100 h-80 flex items-center justify-center">
                        <p class="text-gray-500">Aucune donnée disponible pour cette période.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(count($dataGraphique) > 0 || count($evolutionTemporelle) > 0)
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(count($dataGraphique) > 0)
        // Configuration pour le graphique circulaire
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieData = {
            labels: Object.keys(@json($dataGraphique)),
            values: Object.values(@json($dataGraphique))
        };

        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: pieData.labels,
                datasets: [{
                    data: pieData.values,
                    backgroundColor: [
                        '#4299e1', '#38b2ac', '#ed8936', '#9f7aea',
                        '#f56565', '#48bb78', '#667eea', '#ed64a6',
                        '#d69e2e', '#4a5568', '#c53030', '#2b6cb0'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const formattedValue = new Intl.NumberFormat('fr-FR').format(value);
                                return `${label}: ${formattedValue} FCFA`;
                            }
                        }
                    }
                }
            }
        });
        @endif

        @if(count($evolutionTemporelle) > 0)
        // Configuration pour le graphique d'évolution temporelle
        const timeCtx = document.getElementById('timeSeriesChart').getContext('2d');
        const timeSeriesData = @json($evolutionTemporelle);

        const timeSeriesChart = new Chart(timeCtx, {
            type: 'line',
            data: {
                labels: timeSeriesData.map(item => item.date),
                datasets: [{
                    label: 'Montant total (FCFA)',
                    data: timeSeriesData.map(item => item.value),
                    borderColor: '#4299e1',
                    backgroundColor: 'rgba(66, 153, 225, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw || 0;
                                return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
                            }
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endif
@endsection
