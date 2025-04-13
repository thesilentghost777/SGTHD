@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gradient-to-r from-blue-50 to-blue-100">

    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center">

            <div>

                <h1 class="text-3xl font-bold text-white">{{ $employee->name }}</h1>

                <p class="text-blue-100 mt-2 capitalize">{{ $employee->role }}</p>

            </div>

            <div class="mt-4 md:mt-0">

                <a href="{{ route('employees2') }}" class="inline-flex items-center px-4 py-2 bg-white rounded-md shadow-sm text-sm font-medium text-blue-700 hover:bg-blue-50">

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />

                    </svg>

                    Retour à la liste

                </a>

            </div>

        </div>

    </div>
    <!-- Information sur le ratio -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500">
            <h4 class="font-semibold text-gray-800 mb-2">Information sur le ratio dépense/gain</h4>
            <p class="text-gray-700">
                <span class="text-green-600 font-medium">Ratio supérieur à 1 :</span> Gain positif (le produit génère plus de valeur que son coût de production)
            </p>
            <p class="text-gray-700 mt-1">
                <span class="text-red-600 font-medium">Ratio inférieur à 1 :</span> Perte (le coût des matières premières dépasse la valeur des produits finis)
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">

        <!-- Filtres de période -->

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtrer par période</h3>



            <div class="flex flex-wrap gap-4">

                <a href="{{ route('employee.details2', ['id' => $employee->id, 'period' => 'day']) }}"

                   class="px-4 py-2 rounded-md {{ $period == 'day' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">

                    Aujourd'hui

                </a>

                <a href="{{ route('employee.details2', ['id' => $employee->id, 'period' => 'week']) }}"

                   class="px-4 py-2 rounded-md {{ $period == 'week' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">

                    Cette semaine

                </a>

                <a href="{{ route('employee.details2', ['id' => $employee->id, 'period' => 'month']) }}"

                   class="px-4 py-2 rounded-md {{ $period == 'month' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">

                    Ce mois

                </a>

                <a href="{{ route('employee.details2', ['id' => $employee->id, 'period' => 'year']) }}"

                   class="px-4 py-2 rounded-md {{ $period == 'year' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">

                    Cette année

                </a>

                <a href="{{ route('employee.details2', ['id' => $employee->id, 'period' => 'all']) }}"

                   class="px-4 py-2 rounded-md {{ $period == 'all' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">

                    Tout

                </a>

            </div>

        </div>

        <!-- Résumé et courbe d'évolution -->

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

            <!-- Résumé -->

            <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-1">

                <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé</h3>



                <div class="space-y-4">

                    <div class="flex justify-between border-b border-gray-200 pb-2">

                        <span class="text-gray-600">Coût total matières:</span>

                        <span class="font-semibold text-red-600">{{ number_format($totalMaterialsCost, 1) }} XAF</span>

                    </div>

                    <div class="flex justify-between border-b border-gray-200 pb-2">

                        <span class="text-gray-600">Valeur totale produits:</span>

                        <span class="font-semibold text-green-600">{{ number_format($totalProductsValue, 1) }} XAF</span>

                    </div>

                    <div class="flex justify-between pt-2">

                        <span class="text-gray-800 font-medium">Ratio gain/dépense:</span>

                        <span class="font-bold text-blue-600">{{ number_format($ratio, 1) }}</span>

                    </div>

                </div>

            </div>



            <!-- Courbe d'évolution -->

            <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">

                <h3 class="text-xl font-semibold text-gray-800 mb-4">Évolution du ratio gain/dépense</h3>

                <div class="h-64">

                    <canvas id="ratioChart"></canvas>

                </div>

            </div>

        </div>

        <!-- Factures -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Facture de matières premières -->

            <div class="bg-white rounded-lg shadow-md overflow-hidden">

                <div class="bg-blue-50 border-b border-blue-100 p-6">

                    <h3 class="text-xl font-semibold text-gray-800">Facture de matières premières</h3>

                    <p class="text-sm text-gray-600 mt-2">Liste des matières premières utilisées</p>

                </div>



                <div class="p-6">

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead>

                                <tr>

                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>

                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>

                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unité</th>

                                    <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Coût unitaire</th>

                                    <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Coût total</th>

                                </tr>

                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @forelse($materialsInvoice as $material)

                                <tr>

                                    <td class="px-4 py-3 whitespace-nowrap">{{ $material['nom'] }}</td>

                                    <td class="px-4 py-3 whitespace-nowrap">{{ number_format($material['quantite'], 3) }}</td>

                                    <td class="px-4 py-3 whitespace-nowrap">{{ $material['unite'] }}</td>

                                    <td class="px-4 py-3 whitespace-nowrap text-right">{{ number_format($material['cout_unitaire'], 2) }} XAF</td>

                                    <td class="px-4 py-3 whitespace-nowrap text-right font-medium">{{ number_format($material['cout_total'], 2) }} XAF</td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">Aucune matière première assignée pour cette période</td>

                                </tr>

                                @endforelse

                            </tbody>

                            <tfoot>

                                <tr>

                                    <td colspan="4" class="px-4 py-3 text-right font-semibold">Total:</td>

                                    <td class="px-4 py-3 text-right font-bold text-red-600">{{ number_format($totalMaterialsCost, 2) }} XAF</td>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                </div>

            </div>



            <!-- Pseudo facture de produits -->

            <div class="bg-white rounded-lg shadow-md overflow-hidden">

                <div class="bg-green-50 border-b border-green-100 p-6">

                    <h3 class="text-xl font-semibold text-gray-800">Pseudo facture de produits</h3>

                    <p class="text-sm text-gray-600 mt-2">Liste des produits fabriqués</p>

                </div>



                <div class="p-6">

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead>

                                <tr>

                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>

                                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>

                                    <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>

                                    <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur totale</th>

                                </tr>

                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @forelse($productsInvoice as $product)

                                <tr>

                                    <td class="px-4 py-3 whitespace-nowrap">{{ $product['nom'] }}</td>

                                    <td class="px-4 py-3 whitespace-nowrap">{{ number_format($product['quantite'], 2) }}</td>

                                    <td class="px-4 py-3 whitespace-nowrap text-right">{{ number_format($product['prix_unitaire'], 2) }} XAF</td>

                                    <td class="px-4 py-3 whitespace-nowrap text-right font-medium">{{ number_format($product['valeur_totale'], 2) }} XAF</td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="4" class="px-4 py-3 text-center text-gray-500">Aucun produit fabriqué pour cette période</td>

                                </tr>

                                @endforelse

                            </tbody>

                            <tfoot>

                                <tr>

                                    <td colspan="3" class="px-4 py-3 text-right font-semibold">Total:</td>

                                    <td class="px-4 py-3 text-right font-bold text-green-600">{{ number_format($totalProductsValue, 2) }} XAF</td>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    // Données pour le graphique

    const historicalData = @json($historicalData);



    // Créer le graphique

    const ctx = document.getElementById('ratioChart').getContext('2d');

    const ratioChart = new Chart(ctx, {

        type: 'line',

        data: {

            labels: historicalData.map(data => data.month),

            datasets: [

                {

                    label: 'Ratio gain/dépense',

                    data: historicalData.map(data => data.ratio),

                    borderColor: '#2563EB',

                    backgroundColor: 'rgba(37, 99, 235, 0.1)',

                    tension: 0.4,

                    fill: true,

                },

                {

                    label: 'Dépenses (XAF)',

                    data: historicalData.map(data => data.depense),

                    borderColor: '#DC2626',

                    tension: 0.4,

                    borderDash: [5, 5],

                    fill: false,

                    hidden: true

                },

                {

                    label: 'Gains (XAF)',

                    data: historicalData.map(data => data.gain),

                    borderColor: '#10B981',

                    tension: 0.4,

                    borderDash: [5, 5],

                    fill: false,

                    hidden: true

                }

            ]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                tooltip: {

                    callbacks: {

                        label: function(context) {

                            let label = context.dataset.label || '';

                            if (label) {

                                label += ': ';

                            }

                            if (context.parsed.y !== null) {

                                if (context.datasetIndex === 0) {

                                    label += parseFloat(context.parsed.y).toFixed(2);

                                } else {

                                    label += parseFloat(context.parsed.y).toFixed(2) + ' XAF';

                                }

                            }

                            return label;

                        }

                    }

                }

            },

            scales: {

                y: {

                    beginAtZero: true,

                    ticks: {

                        callback: function(value) {

                            return value.toFixed(2);

                        }

                    }

                }

            }

        }

    });

</script>

@endpush

@endsection
