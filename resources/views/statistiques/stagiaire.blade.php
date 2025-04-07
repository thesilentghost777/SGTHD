@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gray-100 py-6">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- En-tête -->

        <div class="mb-8">

            <h1 class="text-3xl font-bold text-gray-900">Statistiques des Stagiaires</h1>

            <p class="mt-2 text-gray-600">Vue d'ensemble et statistiques détaillées</p>

        </div>

        <!-- Statistiques générales -->

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

            <div class="bg-white rounded-lg shadow p-6">

                <h3 class="text-lg font-medium text-gray-900">Total Stagiaires</h3>

                <p class="text-3xl font-bold text-blue-600">{{ $generalStats['total'] }}</p>

            </div>

            <div class="bg-white rounded-lg shadow p-6">

                <h3 class="text-lg font-medium text-gray-900">Stagiaires Actifs</h3>

                <p class="text-3xl font-bold text-green-600">{{ $generalStats['actifs'] }}</p>

            </div>

            <div class="bg-white rounded-lg shadow p-6">

                <h3 class="text-lg font-medium text-gray-900">Rémunération Totale</h3>

                <p class="text-3xl font-bold text-purple-600">{{ number_format($generalStats['totalRemuneration'], 2) }} XAF</p>

            </div>

            <div class="bg-white rounded-lg shadow p-6">

                <h3 class="text-lg font-medium text-gray-900">Rémunération Moyenne</h3>

                <p class="text-3xl font-bold text-yellow-600">{{ number_format($generalStats['moyenneRemuneration'], 2) }} XAF</p>

            </div>

        </div>

        <!-- Graphiques -->

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Types de stage -->

            <div class="bg-white rounded-lg shadow p-6">

                <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition par Type de Stage</h3>

                <canvas id="typeStageChart"></canvas>

            </div>

            <!-- Évolution mensuelle -->

            <div class="bg-white rounded-lg shadow p-6">

                <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution Mensuelle des Arrivées</h3>

                <canvas id="evolutionChart"></canvas>

            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Durée moyenne -->

            <div class="bg-white rounded-lg shadow p-6">

                <h3 class="text-lg font-medium text-gray-900 mb-4">Durée Moyenne par Type de Stage</h3>

                <canvas id="dureeChart"></canvas>

            </div>

            <!-- Départements -->

            <div class="bg-white rounded-lg shadow p-6">

                <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition par Département</h3>

                <canvas id="departementChart"></canvas>

            </div>

        </div>

        <!-- Top Appréciations -->

        <div class="bg-white rounded-lg shadow p-6 mb-8">

            <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 des Appréciations</h3>

            <canvas id="appreciationChart"></canvas>

        </div>

    </div>

</div>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function() {

    // Graphique Types de stage

    new Chart(document.getElementById('typeStageChart'), {

        type: 'pie',

        data: {

            labels: {!! json_encode($typeStats->pluck('type_stage')) !!},

            datasets: [{

                data: {!! json_encode($typeStats->pluck('total')) !!},

                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444']

            }]

        }

    });

    // Graphique Évolution mensuelle

    new Chart(document.getElementById('evolutionChart'), {

        type: 'line',

        data: {

            labels: {!! json_encode($evolutionMensuelle->pluck('mois')) !!},

            datasets: [{

                label: 'Nouveaux stagiaires',

                data: {!! json_encode($evolutionMensuelle->pluck('total')) !!},

                borderColor: 'rgb(59, 130, 246)',

                tension: 0.1

            }]

        }

    });

    // Graphique Durée moyenne

    new Chart(document.getElementById('dureeChart'), {

        type: 'bar',

        data: {

            labels: {!! json_encode($dureeMoyenne->pluck('type_stage')) !!},

            datasets: [{

                label: 'Jours',

                data: {!! json_encode($dureeMoyenne->pluck('duree_moyenne')) !!},

                backgroundColor: 'rgb(59, 130, 246)'

            }]

        }

    });

    // Graphique Départements

    new Chart(document.getElementById('departementChart'), {

        type: 'doughnut',

        data: {

            labels: {!! json_encode($departementStats->pluck('departement')) !!},

            datasets: [{

                data: {!! json_encode($departementStats->pluck('total')) !!},

                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']

            }]

        }

    });

    // Graphique Appréciations

    new Chart(document.getElementById('appreciationChart'), {

        type: 'bar',

        data: {

            labels: {!! json_encode($topAppreciations->pluck('appreciation')) !!},

            datasets: [{

                label: 'Nombre de stagiaires',

                data: {!! json_encode($topAppreciations->pluck('total')) !!},

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
