@extends('layouts.app')

@section('content')
@include('buttons')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 py-8">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


        <!-- Employee Header -->

        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">

            <div class="flex items-center space-x-6">

                <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center">

                    <span class="text-3xl text-blue-600">

                        {{ strtoupper(substr($employee->name, 0, 2)) }}

                    </span>

                </div>

                <div>

                    <h1 class="text-3xl font-bold text-gray-900">{{ $employee->name }}</h1>

                    <div class="mt-2 grid grid-cols-2 gap-4 text-sm text-gray-600">

                        <div>

                            <span class="font-medium">Age:</span> {{ $age }} ans

                        </div>

                        <div>

                            <span class="font-medium">Téléphone:</span> {{ $employee->num_tel }}

                        </div>

                        <div>

                            <span class="font-medium">Secteur:</span> {{ $employee->secteur }}

                        </div>

                        <div>

                            <span class="font-medium">En service depuis:</span> {{ $employee->annee_debut_service }}

                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800">Salaire Mensuel</h3>
                            <div class="mt-1">
                                @if($salary)
                                    <p class="text-2xl font-bold text-green-600">{{ number_format($salary->somme, 0, ',', ' ') }} FCFA</p>
                                    <p class="text-sm text-green-700 mt-1">
                                        @if($salary->retrait_valide)
                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                                Payé
                                            </span>
                                        @elseif($salary->retrait_demande)
                                            <span class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                                En attente de validation
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                                                Non réclamé
                                            </span>
                                        @endif
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">Aucun salaire défini</p>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>
                    <!-- Period Display -->
            <div class="bg-blue-100 rounded-lg p-4 mb-8">
                <h2 class="text-xl font-semibold text-blue-800">Période d'analyse</h2>
                <p class="text-blue-600 mt-1">{{ $periodDisplay }}</p>
            </div>


                    <!-- Performance Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                        <!-- Production Value -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Valeur de Production</h3>
                            <div class="text-3xl font-bold text-blue-600">
                                {{ number_format($productionStats['total_revenue']) }} FCFA
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                Bénéfice: {{ number_format($productionStats['profit']) }} FCFA
                            </div>
                        </div>

                        <!-- Assignment Completion -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Taux de Respect des Assignations</h3>
                            <div class="text-3xl font-bold @if($assignmentRate >= 80) text-green-600 @else text-yellow-600 @endif">
                                {{ number_format($assignmentRate, 1) }}%
                            </div>
                        </div>

                        <!-- Most Produced Product -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Produit le Plus Produit</h3>
                            @if($mostProducedProduct)
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ $mostProducedProduct['nom_produit'] }}
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    Quantité: {{ number_format($mostProducedProduct['total_quantity']) }} unités
                                </div>
                            @else
                                <div class="text-sm text-gray-500">Aucune production pour cette période</div>
                            @endif
                        </div>
                    </div>

                    <!-- Production Details -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Production Evolution -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Evolution de la Production</h3>
                            <div class="h-80">
                                <canvas id="productionChart"></canvas>
                            </div>
                        </div>

                        <!-- Products List -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Produits Réalisés</h3>
                            <div class="space-y-4">
                                @foreach($productionStats['products'] as $productId => $stats)
                                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $stats['name'] }}</h4>
                                            <p class="text-sm text-gray-600">Quantité: {{ number_format($stats['quantity']) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-blue-600">{{ number_format($stats['revenue']) }} FCFA</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Waste Metrics -->
                    <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Gaspillage par Produit</h3>
                        <div class="space-y-4">
                            @foreach($wasteStats as $productId => $waste)
                                <div>
                                    <div class="flex justify-between text-sm">
                                        <span>{{ $waste['name'] }}</span>
                                        <span class="font-medium @if($waste['waste_percentage'] <= 5) text-green-600 @else text-red-600 @endif">
                                            {{ number_format($waste['waste_percentage'], 1) }}%
                                        </span>
                                    </div>
                                    <div class="mt-1 h-2 bg-gray-200 rounded-full">
                                        <div class="h-full rounded-full @if($waste['waste_percentage'] <= 5) bg-green-500 @else bg-red-500 @endif"
                                             style="width: {{ min($waste['waste_percentage'], 100) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>


    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    // Setup production evolution chart

    const ctx = document.getElementById('productionChart').getContext('2d');

    const evolutionData = @json($evolutionData);



    new Chart(ctx, {

        type: 'line',

        data: {

            labels: evolutionData.map(item => item.date),

            datasets: [{

                label: 'Production Quotidienne',

                data: evolutionData.map(item => item.total),

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

                    beginAtZero: true

                }

            }

        }

    });

</script>

@endsection
