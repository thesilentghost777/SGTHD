@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Tableau de bord financier</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Solde total</h3>
                <p class="text-3xl font-bold text-blue-600">{{ number_format($soldeTotalEntreprise, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Dépenses totales</h3>
                <p class="text-3xl font-bold text-red-600">{{ number_format($depensesTotales, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Salaires (mois courant)</h3>
                <p class="text-3xl font-bold text-green-600">{{ number_format($salairesTotauxMois, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Avances (mois courant)</h3>
                <p class="text-3xl font-bold text-yellow-600">{{ number_format($avancesTotalesMois, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Manquants et Délis</h3>
                <p class="text-3xl font-bold text-red-500">{{ number_format($manquantsEtDelis, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Primes</h3>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($primesTotalesMois, 0, ',', ' ') }} FCFA</p>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Montant Caisse Sociale</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ number_format($caisseSociale, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>

        <!-- Prévisions Salariales -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Prévisions Salariales du Mois</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Montant Total Prévisionnel</h4>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($montantPrevisionnel, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Caisse Sociale</h4>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($montantCaisseSociale, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Montant Enveloppes</h4>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($montantEnveloppes, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
        </div>

        <!-- Statistiques des Sacs -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Statistiques des Sacs</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Total Sacs Vendus (Mois)</h4>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($statsSacs['total_vendus']) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-600 mb-2">Revenu des Ventes (Mois)</h4>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($statsSacs['revenu_ventes'], 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
            <div class="mt-6 h-64">
                <canvas id="evolutionSacs"></canvas>
            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Évolution mensuelle</h3>
                <div class="h-[400px]">
                    <canvas id="statsMonthlyChart"></canvas>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Évolution annuelle</h3>
                <div class="h-[400px]">
                    <canvas id="statsYearlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyCtx = document.getElementById('statsMonthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($statsParMois->pluck('mois')) !!},
            datasets: [{
                label: 'Salaires',
                data: {!! json_encode($statsParMois->pluck('salaires')) !!},
                borderColor: 'rgb(34, 197, 94)',
                tension: 0.1
            }, {
                label: 'Avances',
                data: {!! json_encode($statsParMois->pluck('avances')) !!},
                borderColor: 'rgb(234, 179, 8)',
                tension: 0.1
            }, {
                label: 'Délis',
                data: {!! json_encode($statsParMois->pluck('delis')) !!},
                borderColor: 'rgb(239, 68, 68)',
                tension: 0.1
            }, {
                label: 'Primes',
                data: {!! json_encode($statsParMois->pluck('primes')) !!},
                borderColor: 'rgb(147, 51, 234)',
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

    const yearlyCtx = document.getElementById('statsYearlyChart').getContext('2d');
    new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($statsParAnnee->pluck('annee')) !!},
            datasets: [{
                label: 'Salaires',
                data: {!! json_encode($statsParAnnee->pluck('salaires')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.5)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }, {
                label: 'Avances',
                data: {!! json_encode($statsParAnnee->pluck('avances')) !!},
                backgroundColor: 'rgba(234, 179, 8, 0.5)',
                borderColor: 'rgb(234, 179, 8)',
                borderWidth: 1
            }, {
                label: 'Délis',
                data: {!! json_encode($statsParAnnee->pluck('delis')) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.5)',
                borderColor: 'rgb(239, 68, 68)',
                borderWidth: 1
            }, {
                label: 'Primes',
                data: {!! json_encode($statsParAnnee->pluck('primes')) !!},
                backgroundColor: 'rgba(147, 51, 234, 0.5)',
                borderColor: 'rgb(147, 51, 234)',
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
