@extends('layouts.app')
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord RH - Statistiques Employés</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2pdf.js"></script>
</head>
@section('content')
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen p-6">
    <br /><br />
    <div class="container mx-auto">
        <header class="bg-white shadow-lg rounded-lg p-6 mb-8 flex justify-between items-center">
            <div class="space-x-4">
                <button id="exportCSV" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition">
                    <a href="{{ route('statistiques.absences') }}">voir statistique sur les Abscence</a>
                </button>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-blue-800">Statistiques Horaires pour le mois courant</h1>
                <p class="text-gray-600">Analyse détaillée des performances et présences</p>
            </div>
            <div class="space-x-4">
                <button id="exportCSV" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition">
                    Exporter CSV
                </button>
            </div>
        </header>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-blue-800 mb-4">Ponctualité Globale</h3>
                <canvas id="punctualityChart"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-blue-800 mb-4">Performance Globale</h3>
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
        <br><br>
        <div class="bg-white shadow-2xl rounded-2xl p-8 col-span-full">
            <h2 class="text-3xl font-extrabold text-center mb-8 text-blue-800 border-b-4 border-blue-500 pb-4">
                🏆 Tableaux des Records Employés
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Records Positifs -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <span class="text-3xl mr-3">🌟</span>
                        <h3 class="text-2xl font-bold text-green-800">Records Positifs</h3>
                    </div>
                    <ul class="space-y-3">
                        <li class="bg-white p-3 rounded-lg shadow">
                            <span class="font-semibold text-green-700">Plus Ponctuel</span>
                            <div class="flex justify-between">
                                <span>{{ $plusPonctuel->name }}</span>
                                <span class="text-green-600 font-bold">{{ $plusPonctuel->taux_ponctualite }}%</span>
                            </div>
                        </li>

                        <li class="bg-white p-3 rounded-lg shadow">
                            <span class="font-semibold text-green-700">Plus d'Heures Travaillées</span>
                            @php
                                $topWorker = $statistiquesHoraires->sortByDesc('heures_travaillees')->first();
                            @endphp
                            <div class="flex justify-between">
                                <span>{{ $topWorker->name }}</span>
                                <span class="text-green-600 font-bold">{{ round($topWorker->heures_travaillees, 2) }} h/j</span>
                            </div>
                        </li>

                        @php
                            $topOvertime = $heuresSupp->sortByDesc('heures_supp_total')->first();
                        @endphp
                    </ul>
                </div>

                <!-- Points d'Amélioration -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <span class="text-3xl mr-3">⚠️</span>
                        <h3 class="text-2xl font-bold text-red-800">Points d'Amélioration</h3>
                    </div>
                    <ul class="space-y-3">
                        @foreach($plusAbsentParSecteur as $secteur => $employe)
                        <li class="bg-white p-3 rounded-lg shadow">
                            <span class="font-semibold text-red-700">Secteur {{ $secteur }}</span>
                            <div class="flex justify-between">
                                <span>{{ $employe->name }}</span>
                                <span class="text-red-600 font-bold">{{ $employe->nombre_absences }} abs.</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Records Variabilité -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center mb-4">
                        <span class="text-3xl mr-3">📊</span>
                        <h3 class="text-2xl font-bold text-purple-800">Variabilité & Consistance</h3>
                    </div>
                    <ul class="space-y-3">
                        @php
                            $mostConsistentArrival = $variationHoraires->sortBy('variation_arrivee')->first();
                            $mostConsistentDeparture = $variationHoraires->sortBy('variation_depart')->first();
                        @endphp
                        <li class="bg-white p-3 rounded-lg shadow">
                            <span class="font-semibold text-purple-700">Plus Consistant (Arrivée)</span>
                            <div class="flex justify-between">
                                <span>{{ $mostConsistentArrival->name }}</span>
                                <span class="text-purple-600 font-bold">±{{ round($mostConsistentArrival->variation_arrivee, 2) }}h</span>
                            </div>
                        </li>
                        <li class="bg-white p-3 rounded-lg shadow">
                            <span class="font-semibold text-purple-700">Plus Consistant (Départ)</span>
                            <div class="flex justify-between">
                                <span>{{ $mostConsistentDeparture->name }}</span>
                                <span class="text-purple-600 font-bold">±{{ round($mostConsistentDeparture->variation_depart, 2) }}h</span>
                            </div>
                        </li>
                        @php
                            $mostInconsistentArrival = $variationHoraires->sortByDesc('variation_arrivee')->first();
                        @endphp
                        <li class="bg-white p-3 rounded-lg shadow">
                            <span class="font-semibold text-purple-700">Plus Variable (Horaires)</span>
                            <div class="flex justify-between">
                                <span>{{ $mostInconsistentArrival->name }}</span>
                                <span class="text-purple-600 font-bold">±{{ round($mostInconsistentArrival->variation_arrivee, 2) }}h</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
        <br><br>

        <div id="statisticsContainer">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($statistiquesGlobales as $stat)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 p-6 space-y-4 border-l-4 border-blue-500">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-semibold text-blue-800">{{ $stat->name }}</h3>
                            <span class="text-sm text-gray-500">{{ $stat->secteur ?? 'Non assigné' }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-600 mb-1">Heures/Jour</p>
                                <p class="font-bold text-blue-700">{{ number_format($stat->moyenne_heures_jour, 1) }} h</p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-600 mb-1">Minutes Retard</p>
                                <p class="font-bold text-green-700">{{ $stat->total_minutes_retard }} min</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 mb-2">Respect des Horaires</p>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div
                                    class="bg-blue-600 h-2.5 rounded-full"
                                    style="width: {{ number_format($stat->taux_respect_horaires, 1) }}%"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-500 text-right mt-1">
                                {{ number_format($stat->taux_respect_horaires, 1) }}%
                            </p>
                        </div>

                        @php
                            $employeeVariation = $variationHoraires->where('name', $stat->name)->first();
                        @endphp
                        @if($employeeVariation)
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-xs text-gray-600 mb-2">Variation Horaires</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-xs text-gray-500">Arrivée</p>
                                        <p class="font-medium text-blue-700">
                                            ±{{ number_format($employeeVariation->variation_arrivee, 1) }}h
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Départ</p>
                                        <p class="font-medium text-blue-700">
                                            ±{{ number_format($employeeVariation->variation_depart, 1) }}h
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Ponctualité Chart
            new Chart(document.getElementById('punctualityChart'), {
                type: 'bar',
                data: {
                    labels: @json($tauxPonctualite->pluck('name')),
                    datasets: [{
                        label: 'Taux de Ponctualité (%)',
                        data: @json($tauxPonctualite->pluck('taux_ponctualite')),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });

            // Performance Chart (utilisant les données de workTimePerDay)
            new Chart(document.getElementById('performanceChart'), {
                type: 'line',
                data: {
                    labels: @json($workTimePerDay->pluck('work_date')),
                    datasets: [{
                        label: 'Heures Travaillées',
                        data: @json($workTimePerDay->pluck('hours_worked')),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Exportation PDF
            document.getElementById('exportPDF').addEventListener('click', () => {
                const element = document.getElementById('statisticsContainer');
                html2pdf().from(element).save('statistiques_employes.pdf');
            });

            // Exportation CSV
            document.getElementById('exportCSV').addEventListener('click', () => {
                const data = @json($statistiquesGlobales);
                let csvContent = "data:text/csv;charset=utf-8,";

                // En-têtes
                csvContent += "Nom,Secteur,Moyenne Heures/Jour,Minutes Retard,Taux Respect Horaires\n";

                // Données
                data.forEach(stat => {
                    csvContent += `${stat.name},${stat.secteur || ''},${stat.moyenne_heures_jour},${stat.total_minutes_retard},${stat.taux_respect_horaires}\n`;
                });

                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "statistiques_employes.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    </script>
</body>
@endsection
</html>
