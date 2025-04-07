@extends('layouts.app')

@section('content')
@include('buttons')
<div class="min-h-screen bg-gradient-to-r from-blue-50 to-blue-100">
    <br>
    <div class="flex justify-center w-full">

      </div>
  <br>

    <!-- En-tête -->

    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6">
        <div class="flex justify-between items-center w-full">
          <h1 class="text-3xl font-bold text-white">Statistiques de Production</h1>
          <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6">
            <div class="flex justify-between items-enter w-full">
                <div class="flex gap-4">
                    <a href="{{ route('employee.performance') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition-colors duration-200 font-medium">
                        Voir statistiques par producteur
                    </a>
                    <a href="{{ route('statistiques.details') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition-colors duration-200 font-medium">
                        Voir statistiques ultra détaillées
                    </a>
                </div>
            </div>
        </div>
        </div>
      </div>
    <!-- Grille principale -->

    <div class="container mx-auto px-4 py-8">

        <!-- Statistiques du personnel -->

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-white rounded-lg shadow-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personnel</h3>

                <div class="space-y-4">

                    <div class="flex justify-between items-center">

                        <span class="text-gray-600">Producteurs</span>

                        <span class="text-xl font-semibold">{{ $staffStats['total_producteurs'] }}</span>

                    </div>

                    <div class="flex justify-between items-center">

                        <span class="text-gray-600">Pâtissiers</span>

                        <span class="text-xl font-semibold">{{ $staffStats['patissiers'] }}</span>

                    </div>

                    <div class="flex justify-between items-center">

                        <span class="text-gray-600">Boulangers</span>

                        <span class="text-xl font-semibold">{{ $staffStats['boulangers'] }}</span>

                    </div>

                </div>

            </div>



            <!-- Top 5 produits les plus rentables -->

            <div class="bg-white rounded-lg shadow-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 - Plus rentables</h3>

                <div class="space-y-2">

                    @foreach($topProfitableProducts as $product)

                    <div class="flex justify-between items-center">

                        <span class="text-gray-600">{{ $product->nom }}</span>

                        <span class="text-green-600 font-semibold">{{ number_format($product->revenu_total, 0, ',', ' ') }} XAF</span>

                    </div>

                    @endforeach

                </div>

            </div>

            <!-- Top 5 produits les moins rentables -->

            <div class="bg-white rounded-lg shadow-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 - Moins rentables</h3>

                <div class="space-y-2">

                    @foreach($leastProfitableProducts as $product)

                    <div class="flex justify-between items-center">

                        <span class="text-gray-600">{{ $product->nom }}</span>

                        <span class="text-red-600 font-semibold">{{ number_format($product->revenu_total, 0, ',', ' ') }} XAF</span>

                    </div>

                    @endforeach

                </div>

            </div>

        </div>

        <!-- Graphiques -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Évolution de la production -->

            <div class="bg-white rounded-lg shadow-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution de la production</h3>

                <div class="h-80">

                    <canvas id="productionChart"></canvas>

                </div>

            </div>

            <!-- Évolution du gaspillage -->

            <div class="bg-white rounded-lg shadow-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution du gaspillage</h3>

                <div class="h-80">

                    <canvas id="wasteChart"></canvas>

                </div>

            </div>

        </div>

        <!-- Bénéfices mensuels et fréquence d'utilisation des matières premières -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white rounded-lg shadow-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bénéfices mensuels</h3>

                <div class="h-80">

                    <canvas id="profitsChart"></canvas>

                </div>

            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Fréquence d'utilisation des matières premières</h3>

                <div class="h-80">

                    <canvas id="materialsChart"></canvas>

                </div>

            </div>

        </div>


    </div>
    <br>



</div>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

    // Configuration des graphiques

    const productionData = @json($productionEvolution);

    const wasteData = @json($wasteEvolution);

    const profitsData = @json($monthlyProfits);

    const materialsData = @json($materialUsageFrequency);

    // Graphique d'évolution de la production

    new Chart(document.getElementById('productionChart'), {

        type: 'line',

        data: {

            labels: productionData.map(item => item.date),

            datasets: [{

                label: 'Production totale',

                data: productionData.map(item => item.total_production),

                borderColor: 'rgb(59, 130, 246)',

                tension: 0.4

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false

        }

    });

    // Graphique d'évolution du gaspillage

    new Chart(document.getElementById('wasteChart'), {

        type: 'line',

        data: {

            labels: wasteData.map(item => item.date),

            datasets: [{

                label: 'Gaspillage',

                data: wasteData.map(item => item.waste),

                borderColor: 'rgb(239, 68, 68)',

                tension: 0.4

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false

        }

    });

    // Graphique des bénéfices mensuels

    new Chart(document.getElementById('profitsChart'), {

        type: 'line',

        data: {

            labels: profitsData.map(item => `${item.month}/${item.year}`),

            datasets: [{

                label: 'Bénéfices',

                data: profitsData.map(item => item.profit),

                borderColor: 'rgb(16, 185, 129)',

                tension: 0.4

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false

        }

    });

    // Graphique des matières premières

    new Chart(document.getElementById('materialsChart'), {

        type: 'bar',

        data: {

            labels: materialsData.map(item => item.nom),

            datasets: [{

                label: 'Fréquence d\'utilisation',

                data: materialsData.map(item => item.frequency),

                backgroundColor: 'rgb(59, 130, 246)'

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false

        }

    });

</script>

@endpush

@endsection
