@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Commandes Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Statistiques des Commandes</h2>

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Commandes</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $orderStats['total'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Commandes Validées</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $orderStats['validated'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Commandes en Attente</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ $orderStats['pending'] }}</p>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution Mensuelle</h3>
                    <canvas id="monthlyOrdersChart"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Distribution par Catégorie</h3>
                    <canvas id="categoryPieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Sacs Section -->
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Statistiques des Sacs</h2>

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Sacs</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $bagStats['totalBags'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Valeur Totale Stock</h3>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($bagStats['totalValue']->total_value, 2) }} XAF</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Stock Faible</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $bagStats['lowStock'] }}</p>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Mouvements de Stock</h3>
                    <canvas id="stockMovementChart"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Sacs les Plus Vendus</h3>
                    <canvas id="topBagsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Orders Chart
    new Chart(document.getElementById('monthlyOrdersChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($orderStats['monthlyOrders']->pluck('month')) !!},
            datasets: [{
                label: 'Commandes par mois',
                data: {!! json_encode($orderStats['monthlyOrders']->pluck('count')) !!},
                borderColor: 'rgb(59, 130, 246)',
                tension: 0.1
            }]
        }
    });

    // Category Distribution Pie Chart
    new Chart(document.getElementById('categoryPieChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($orderStats['categoryDistribution']->pluck('categorie')) !!},
            datasets: [{
                data: {!! json_encode($orderStats['categoryDistribution']->pluck('count')) !!},
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
            }]
        }
    });

    // Stock Movement Chart
    new Chart(document.getElementById('stockMovementChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($bagStats['transactions']->pluck('transaction_date')) !!},
            datasets: [{
                label: 'Mouvement net de stock',
                data: {!! json_encode($bagStats['transactions']->pluck('net_quantity')) !!},
                borderColor: 'rgb(16, 185, 129)',
                tension: 0.1
            }]
        }
    });

    // Top Bags Chart
    new Chart(document.getElementById('topBagsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($bagStats['mostPopular']->pluck('name')) !!},
            datasets: [{
                label: 'Quantité vendue',
                data: {!! json_encode($bagStats['mostPopular']->pluck('total_sold')) !!},
                backgroundColor: 'rgb(59, 130, 246)'
            }]
        },
        options: {
            indexAxis: 'y'
        }
    });
});
</script>
@endpush
@endsection
