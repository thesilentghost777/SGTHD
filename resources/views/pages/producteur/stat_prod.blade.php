@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Statistiques de Production</h1>
        <p class="text-gray-600">{{ $nom }} - {{ $secteur }}</p>
    </div>

    <!-- Production Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Daily Production -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Production Journalière</h2>
            <canvas id="dailyChart"></canvas>
        </div>

        <!-- Monthly Production -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Production Mensuelle</h2>
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Yearly Production -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Production Annuelle</h2>
        <canvas id="yearlyChart"></canvas>
    </div>

    <!-- Product Details -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-xl font-bold mb-4">Détails des Produits</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité Totale</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenu Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coût MP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bénéfice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marge (%)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stats['products'] as $product)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $product['produit']['nom'] }}</div>
                            <div class="mt-1">
                                <button onclick="toggleMaterials('{{ $loop->index }}')" class="text-sm text-blue-600 hover:text-blue-800">
                                    Voir matières premières
                                </button>
                            </div>
                            <!-- Materials Details (hidden by default) -->
                            <div id="materials-{{ $loop->index }}" class="hidden mt-2 pl-4 border-l-2 border-gray-200">
                                @foreach($product['matieres_premieres'] as $mp)
                                <div class="text-sm text-gray-600 mb-1">
                                    <span class="font-medium">{{ $mp['nom'] }}:</span>
                                    {{ $mp['quantite_totale'] }} {{ $mp['unite'] }}
                                    ({{ number_format($mp['cout_total']) }} FCFA)
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ $product['produit']['quantite_totale'] }}</td>
                        <td class="px-6 py-4">{{ number_format($product['produit']['revenu_total']) }} FCFA</td>
                        <td class="px-6 py-4">{{ number_format($product['cout_total_mp']) }} FCFA</td>
                        <td class="px-6 py-4 {{ $product['benefice'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($product['benefice']) }} FCFA
                        </td>
                        <td class="px-6 py-4">{{ number_format($product['marge'], 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleMaterials(index) {
    const element = document.getElementById(`materials-${index}`);
    element.classList.toggle('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const stats = @json($stats);

    // Helper function to create charts
    function createChart(elementId, labels, data, title, type = 'line') {
        // Inverser les tableaux de labels et de données
        const reversedLabels = labels.reverse();
        const reversedData = data.reverse();

        const ctx = document.getElementById(elementId).getContext('2d');
        return new Chart(ctx, {
            type: type,
            data: {
                labels: reversedLabels,
                datasets: [{
                    label: 'Quantité produite',
                    data: reversedData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: title
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Create charts
    createChart('dailyChart', stats.daily.labels, stats.daily.quantities, 'Production des 7 derniers jours');
    createChart('monthlyChart', stats.monthly.labels, stats.monthly.quantities, 'Production des 12 derniers mois');
    createChart('yearlyChart', stats.yearly.labels, stats.yearly.quantities, 'Production des 5 dernières années', 'bar');
});
</script>
@endsection

