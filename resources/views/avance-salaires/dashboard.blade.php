@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Tableau de bord des avances sur salaire</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Carte statistique - Total des demandes -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-blue-400 border-opacity-30">
                <h3 class="text-white font-medium">Demandes du mois ({{ $currentMonth }})</h3>
            </div>
            <div class="px-6 py-5">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="block text-3xl font-bold text-white">{{ $totalDemandes }}</span>
                        <span class="block text-blue-100 mt-1">Total: {{ number_format($montantTotal, 0, ',', ' ') }} XAF</span>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"></path><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"></path><path d="M12 3v6"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte statistique - Demandes en attente -->
<div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-red-400 border-opacity-30">
        <h3 class="text-red font-medium">En attente de validation</h3>
    </div>
    <div class="px-6 py-5">
        <div class="flex justify-between items-center">
            <div>
                <span class="block text-3xl font-bold text-red">{{ $demandesEnAttente }}</span>
                <span class="block text-red-100 mt-1">Montant: {{ number_format($montantEnAttente, 0, ',', ' ') }} XAF</span>
            </div>
            <div class="bg-red-400 bg-opacity-30 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.5 20H4a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2h3.93a2 2 0 0 1 1.66.9l.82 1.2a2 2 0 0 0 1.66.9H20a2 2 0 0 1 2 2v3"></path><circle cx="18" cy="18" r="3"></circle><path d="M18 15v2l1 1"></path></svg>
            </div>
        </div>
    </div>
</div>

        <!-- Carte statistique - Demandes validées -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-emerald-400 border-opacity-30">
                <h3 class="text-white font-medium">Demandes validées</h3>
            </div>
            <div class="px-6 py-5">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="block text-3xl font-bold text-white">{{ $demandesValidees }}</span>
                        <span class="block text-emerald-100 mt-1">Montant: {{ number_format($montantValide, 0, ',', ' ') }} XAF</span>
                    </div>
                    <div class="bg-emerald-400 bg-opacity-30 p-3 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique des statistiques mensuelles -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="font-medium text-gray-700">Historique des 6 derniers mois</h2>
        </div>
        <div class="p-6">
            <canvas id="statsChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Tableau des avances -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h2 class="font-medium text-gray-700">Liste des avances sur salaire ({{ $currentMonth }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de demande</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($avances as $avance)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $avance->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $avance->employe_nom }}</div>
                            <div class="text-sm text-gray-500">{{ $avance->employe_email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($avance->sommeAs, 0, ',', ' ') }} XAF</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $avance->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($avance->retrait_valide)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Validée
                                </span>
                            @elseif($avance->retrait_demande)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    En attente
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Créée
                                </span>
                            @endif
                        </td>

                    </tr>
                    @endforeach

                    @if(count($avances) == 0)
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400 mb-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            Aucune avance sur salaire pour ce mois
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Préparation des données pour le graphique
        const statsData = @json($statistiquesMensuelles);

        const mois = statsData.map(item => {
            const [year, month] = item.mois.split('-');
            return new Date(year, month - 1).toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
        });

        const nombreDemandes = statsData.map(item => item.nombre);
        const montants = statsData.map(item => item.montant);

        // Création du graphique
        const ctx = document.getElementById('statsChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: mois,
                datasets: [{
                    label: 'Nombre de demandes',
                    data: nombreDemandes,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Montant total (XAF)',
                    data: montants,
                    type: 'line',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Nombre de demandes'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Montant (XAF)'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
