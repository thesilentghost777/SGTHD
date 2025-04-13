@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-r from-blue-50 to-blue-100 py-6">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <div class="bg-gradient-to-r from-blue-700 to-blue-900 text-white p-5 rounded-t-lg shadow-lg">
                <h1 class="text-2xl font-bold">Comparaison des Performances des Vendeurs</h1>
                <h5 class="text-blue-200 mt-1">Statistiques pour {{ $moisActuel }}</h5>
            </div>

            <div class="bg-white p-4 rounded-b-lg shadow-lg">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('ventes.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour aux Ventes
                    </a>

                </div>
            </div>
        </div>

        <!-- Cartes de performance -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach($statsVendeurs as $vendeur)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform hover:-translate-y-1 hover:shadow-xl">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-4">
                    <h5 class="font-bold text-lg truncate">{{ $vendeur->nom_serveur ?? 'Vendeur #' . $vendeur->serveur_id }}</h5>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-700">Quantité vendue:</span>
                        <span class="bg-green-100 text-green-800 px-2.5 py-0.5 rounded-full text-sm font-medium">{{ $vendeur->total_ventes }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-700">Gain rapporté:</span>
                        <span class="bg-blue-100 text-blue-800 px-2.5 py-0.5 rounded-full text-sm font-medium">{{ number_format($vendeur->benefice, 0, ',', ' ') }} XAF</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-700">Produits invendus:</span>
                        <span class="bg-yellow-100 text-yellow-800 px-2.5 py-0.5 rounded-full text-sm font-medium">{{ $vendeur->total_invendus }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Produits avariés:</span>
                        <span class="bg-red-100 text-red-800 px-2.5 py-0.5 rounded-full text-sm font-medium">{{ $vendeur->total_avaries }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4">
                    <h5 class="font-bold">Quantités Vendues</h5>
                </div>
                <div class="p-4">
                    <div class="h-80">
                        <canvas id="ventesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4">
                    <h5 class="font-bold">Bénéfices Rapportés (XAF)</h5>
                </div>
                <div class="p-4">
                    <div class="h-80">
                        <canvas id="beneficesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4">
                    <h5 class="font-bold">Produits Invendus</h5>
                </div>
                <div class="p-4">
                    <div class="h-80">
                        <canvas id="invendusChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4">
                    <h5 class="font-bold">Produits Avariés</h5>
                </div>
                <div class="p-4">
                    <div class="h-80">
                        <canvas id="avariesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = {
            labels: {!! json_encode($chartData['labels']) !!},
            dataVentes: {!! json_encode($chartData['dataVentes']) !!},
            dataBenefices: {!! json_encode($chartData['dataBenefices']) !!},
            dataInvendus: {!! json_encode($chartData['dataInvendus']) !!},
            dataAvaries: {!! json_encode($chartData['dataAvaries']) !!}
        };

        // Configuration commune
        const chartOptions = {
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
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        };

        // Graphique des ventes
        new Chart(document.getElementById('ventesChart'), {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Quantités vendues',
                    data: chartData.dataVentes,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });

        // Graphique des bénéfices
        new Chart(document.getElementById('beneficesChart'), {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Bénéfices (XAF)',
                    data: chartData.dataBenefices,
                    backgroundColor: 'rgba(37, 99, 235, 0.7)',
                    borderColor: 'rgb(37, 99, 235)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });

        // Graphique des invendus
        new Chart(document.getElementById('invendusChart'), {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Produits invendus',
                    data: chartData.dataInvendus,
                    backgroundColor: 'rgba(245, 158, 11, 0.7)',
                    borderColor: 'rgb(245, 158, 11)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });

        // Graphique des avaries
        new Chart(document.getElementById('avariesChart'), {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Produits avariés',
                    data: chartData.dataAvaries,
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1
                }]
            },
            options: chartOptions
        });
    });
</script>
@endpush
@endsection
