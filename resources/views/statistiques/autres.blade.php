@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Dépenses Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Statistiques des Dépenses</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Dépenses</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($depenseStats['total'], 2) }} XAF</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Nombre de Dépenses</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $depenseStats['count'] }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Dépenses par Type</h3>
                    <canvas id="depenseTypeChart"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution Mensuelle</h3>
                    <canvas id="depenseMonthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Retenues Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Statistiques des Retenues</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Manquants</h3>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($retenueStats['totalManquants'], 2) }} XAF</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Remboursements</h3>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($retenueStats['totalRemboursements'], 2) }} XAF</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Prêts</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ number_format($retenueStats['totalPrets'], 2) }} XAF</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Caisse Sociale</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($retenueStats['totalCaisseSociale'], 2) }} XAF</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Employés avec Manquants</h3>
                <canvas id="manquantsChart"></canvas>
            </div>
        </div>

        <!-- Primes Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Statistiques des Primes</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Primes</h3>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($primeStats['totalPrimes'], 2) }} XAF</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Prime Moyenne</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($primeStats['avgPrime'], 2) }} XAF</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Distribution des Primes</h3>
                <canvas id="primesDistributionChart"></canvas>
            </div>
        </div>

        <!-- Congés Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Statistiques des Congés</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Jours de Repos</h3>
                    <canvas id="reposChart"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Raisons des Congés</h3>
                    <canvas id="congesRaisonChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Délits Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Statistiques des Délits</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Délits</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $deliStats['totalDelits'] }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Montant Total</h3>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($deliStats['montantTotal'], 2) }} XAF</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Incidents par Mois</h3>
                    <canvas id="incidentsChart"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top 5 Types de Délits</h3>
                    <canvas id="topDelitsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Salaires Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Statistiques des Salaires</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Total Mensuel</h3>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($salaireStats['totalMensuel'], 2) }} XAF</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Salaire Moyen</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($salaireStats['moyenneSalaire'], 2) }} XAF</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Distribution des Salaires</h3>
                <canvas id="salairesChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dépenses par Type
    new Chart(document.getElementById('depenseTypeChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($depenseStats['byType']->pluck('type')) !!},
            datasets: [{
                data: {!! json_encode($depenseStats['byType']->pluck('total')) !!},
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B']
            }]
        }
    });

    // Évolution Mensuelle des Dépenses
    new Chart(document.getElementById('depenseMonthlyChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($depenseStats['monthly']->pluck('month')) !!},
            datasets: [{
                label: 'Dépenses mensuelles',
                data: {!! json_encode($depenseStats['monthly']->pluck('total')) !!},
                borderColor: 'rgb(59, 130, 246)',
                tension: 0.1
            }]
        }
    });

    // Top 5 Employés avec Manquants
    new Chart(document.getElementById('manquantsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($retenueStats['employesManquants']->pluck('name')) !!},
            datasets: [{
                label: 'Manquants',
                data: {!! json_encode($retenueStats['employesManquants']->pluck('total_manquants')) !!},
                backgroundColor: 'rgb(239, 68, 68)'
            }]
        },
        options: {
            indexAxis: 'y'
        }
    });

    // Distribution des Primes
    new Chart(document.getElementById('primesDistributionChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($primeStats['distribution']->pluck('name')) !!},
            datasets: [{
                label: 'Total des primes',
                data: {!! json_encode($primeStats['distribution']->pluck('total_primes')) !!},
                backgroundColor: 'rgb(16, 185, 129)'
            }]
        }
    });

    // Jours de Repos
    new Chart(document.getElementById('reposChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($congeStats['joursRepos']->pluck('jour')) !!},
            datasets: [{
                label: 'Nombre d\'employés',
                data: {!! json_encode($congeStats['joursRepos']->pluck('count')) !!},
                backgroundColor: 'rgb(59, 130, 246)'
            }]
        }
    });

    // Raisons des Congés
    new Chart(document.getElementById('congesRaisonChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($congeStats['raisonConges']->pluck('raison_c')) !!},
            datasets: [{
                data: {!! json_encode($congeStats['raisonConges']->pluck('count')) !!},
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6']
            }]
        }
    });

    // Incidents par Mois
    new Chart(document.getElementById('incidentsChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($deliStats['incidentsByMonth']->pluck('month')) !!},
            datasets: [{
                label: 'Nombre d\'incidents',
                data: {!! json_encode($deliStats['incidentsByMonth']->pluck('count')) !!},
                borderColor: 'rgb(239, 68, 68)',
                tension: 0.1
            }]
        }
    });

    // Top Délits
    new Chart(document.getElementById('topDelitsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($deliStats['topDelits']->pluck('nom')) !!},
            datasets: [{
                label: 'Nombre d\'occurrences',
                data: {!! json_encode($deliStats['topDelits']->pluck('count')) !!},
                backgroundColor: 'rgb(239, 68, 68)'
            }]
        },
        options: {
            indexAxis: 'y'
        }
    });

    // Distribution des Salaires
    new Chart(document.getElementById('salairesChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($salaireStats['distribution']->pluck('name')) !!},
            datasets: [{
                label: 'Salaire de base',
                data: {!! json_encode($salaireStats['distribution']->pluck('somme')) !!},
                backgroundColor: 'rgb(16, 185, 129)'
            }, {
                label: 'Salaire effectif',
                data: {!! json_encode($salaireStats['distribution']->pluck('somme_effective_mois')) !!},
                backgroundColor: 'rgb(59, 130, 246)'
            }]
        }
    });
});
</script>
@endpush
@endsection
