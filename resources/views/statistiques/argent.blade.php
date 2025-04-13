@extends('layouts.app')

@section('content')

<div class="py-12">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <h1 class="text-3xl font-bold text-gray-900 mb-8">Tableau de bord des statistiques</h1>

        <!-- Résumé général -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Salaires</h3>

                <div class="space-y-3">

                    <p class="text-sm text-gray-600">Total: <span class="font-semibold text-gray-900">{{ number_format($statsSalaires['total_salaires'], 0, ',', ' ') }} FCFA</span></p>

                    <p class="text-sm text-gray-600">En attente: <span class="font-semibold text-gray-900">{{ $statsSalaires['salaires_en_attente'] }}</span></p>

                    <p class="text-sm text-gray-600">Moyenne: <span class="font-semibold text-gray-900">{{ number_format($statsSalaires['moyenne_salaires'], 0, ',', ' ') }} FCFA</span></p>

                </div>

            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Avances</h3>

                <div class="space-y-3">

                    <p class="text-sm text-gray-600">Total: <span class="font-semibold text-gray-900">{{ number_format($statsAvances['total_avances'], 0, ',', ' ') }} FCFA</span></p>

                    <p class="text-sm text-gray-600">En cours: <span class="font-semibold text-gray-900">{{ $statsAvances['avances_en_cours'] }}</span></p>

                    <p class="text-sm text-gray-600">% Employés avec avance: <span class="font-semibold text-gray-900">{{ number_format($statsAvances['pourcentage_employes_avec_avance'], 1) }}%</span></p>

                </div>

            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Primes</h3>

                <div class="space-y-3">

                    <p class="text-sm text-gray-600">Total: <span class="font-semibold text-gray-900">{{ number_format($statsPrimes['total_primes'], 0, ',', ' ') }} FCFA</span></p>

                    <p class="text-sm text-gray-600">Moyenne/employé: <span class="font-semibold text-gray-900">{{ number_format($statsPrimes['moyenne_prime_par_employe'], 0, ',', ' ') }} FCFA</span></p>

                </div>

            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Délis</h3>

                <div class="space-y-3">

                    <p class="text-sm text-gray-600">Nombre total: <span class="font-semibold text-gray-900">{{ $statsDelis['total_delis'] }}</span></p>

                    <p class="text-sm text-gray-600">Montant total: <span class="font-semibold text-gray-900">{{ number_format($statsDelis['montant_total_delis'], 0, ',', ' ') }} FCFA</span></p>

                </div>

            </div>

        </div>

        <!-- Déductions -->

        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 mb-8">

            <h3 class="text-xl font-semibold text-gray-900 mb-6">Déductions</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div class="p-4 bg-red-50 rounded-lg">

                    <p class="text-sm text-red-600">Manquants</p>

                    <p class="text-2xl font-bold text-red-700">{{ number_format($statsDeductions['total_manquants'], 0, ',', ' ') }} FCFA</p>

                </div>

                <div class="p-4 bg-blue-50 rounded-lg">

                    <p class="text-sm text-blue-600">Remboursements</p>

                    <p class="text-2xl font-bold text-blue-700">{{ number_format($statsDeductions['total_remboursements'], 0, ',', ' ') }} FCFA</p>

                </div>

                <div class="p-4 bg-yellow-50 rounded-lg">

                    <p class="text-sm text-yellow-600">Prêts</p>

                    <p class="text-2xl font-bold text-yellow-700">{{ number_format($statsDeductions['total_prets'], 0, ',', ' ') }} FCFA</p>

                </div>

                <div class="p-4 bg-green-50 rounded-lg">

                    <p class="text-sm text-green-600">Caisse sociale</p>

                    <p class="text-2xl font-bold text-green-700">{{ number_format($statsDeductions['total_caisse_sociale'], 0, ',', ' ') }} FCFA</p>

                </div>

            </div>

        </div>

        <!-- Graphiques des tendances -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Tendances des salaires et avances -->

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendances mensuelles - Salaires et Avances</h3>

                <div class="h-80">

                    <canvas id="salairesAvancesChart"></canvas>

                </div>

            </div>

            <!-- Tendances des délis -->

            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendances mensuelles - Délis</h3>

                <div class="h-80">

                    <canvas id="delisChart"></canvas>

                </div>

            </div>

        </div>

    </div>

</div>

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function() {

    // Configuration du graphique Salaires et Avances

    const salairesAvancesCtx = document.getElementById('salairesAvancesChart').getContext('2d');

    new Chart(salairesAvancesCtx, {

        type: 'line',

        data: {

            labels: {!! json_encode($tendances['salaires']->pluck('mois')) !!},

            datasets: [{

                label: 'Salaires',

                data: {!! json_encode($tendances['salaires']->pluck('total')) !!},

                borderColor: 'rgb(59, 130, 246)',

                tension: 0.1

            }, {

                label: 'Avances',

                data: {!! json_encode($tendances['avances']->pluck('total')) !!},

                borderColor: 'rgb(239, 68, 68)',

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

                            return value.toLocaleString('fr-FR') + ' FCFA';

                        }

                    }

                }

            }

        }

    });

    // Configuration du graphique Délis

    const delisCtx = document.getElementById('delisChart').getContext('2d');

    new Chart(delisCtx, {

        type: 'bar',

        data: {

            labels: {!! json_encode($tendances['delis']->pluck('mois')) !!},

            datasets: [{

                label: 'Délis',

                data: {!! json_encode($tendances['delis']->pluck('total')) !!},

                backgroundColor: 'rgba(245, 158, 11, 0.5)',

                borderColor: 'rgb(245, 158, 11)',

                borderWidth: 1

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

                            return value.toLocaleString('fr-FR') + ' FCFA';

                        }

                    }

                }

            }

        }

    });

});

</script>

@endpush

@endsection
