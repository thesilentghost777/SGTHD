@extends('pages/serveur/serveur_default')

@section('page-content')
<div class="py-8 bg-white">
    <!-- En-tête avec boutons -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                Tableau de bord
            </h1>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{route('serveur-produit_invendu')}}"
                   class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Enregistrer produits
                </a>
                <a href="{{route('versements.index')}}"
                   class="inline-flex items-center justify-center px-5 py-3 bg-white text-blue-600 text-sm font-medium rounded-md shadow-sm border border-blue-300 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Effectuer un versement
                </a>
            </div>
        </div>
    </div>

    <!-- Conteneur principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Graphique principal (occupe 2 colonnes) -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Évolution des Ventes</h2>
                    <div class="h-[400px]">
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Graphique circulaire -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Distribution des Ventes</h2>
                    <div class="h-[400px] flex items-center justify-center">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique en barres (pleine largeur) -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden mb-8">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">Comparaison Ventes/Invendus</h2>
                <div class="h-[400px]">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js n\'est pas chargé correctement');
        return;
    }

    // Configuration globale de Chart.js
    Chart.defaults.color = '#4b5563';
    Chart.defaults.font.family = '"Inter", system-ui, -apple-system, sans-serif';
    Chart.defaults.font.size = 13;
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(17, 24, 39, 0.95)';
    Chart.defaults.plugins.tooltip.titleColor = '#fff';
    Chart.defaults.plugins.tooltip.bodyColor = '#fff';
    Chart.defaults.plugins.tooltip.borderColor = 'rgba(255, 255, 255, 0.1)';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.cornerRadius = 6;

    // Palette de couleurs professionnelle (40% bleu, 5% vert, 5% autres)
    const blueColors = [
        'rgba(37, 99, 235, 1)', // bleu principal
        'rgba(59, 130, 246, 1)',
        'rgba(96, 165, 250, 1)',
        'rgba(147, 197, 253, 1)',
    ];

    const greenColors = ['rgba(16, 185, 129, 1)']; // 5% vert

    const otherColors = ['rgba(249, 115, 22, 1)']; // 5% autres (orange)

    // Fonction pour créer le graphique en courbe
    function createLineChart() {
        const data = @json($ventesParJour);
        const ctx = document.getElementById('lineChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => formatDate(item.date)),
                datasets: [{
                    label: 'Ventes',
                    data: data.map(item => item.ventes),
                    borderColor: blueColors[0],
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.2,
                    borderWidth: 3,
                    pointRadius: 3,
                    pointBackgroundColor: blueColors[0]
                }, {
                    label: 'Invendus',
                    data: data.map(item => item.invendus),
                    borderColor: greenColors[0],
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    fill: true,
                    tension: 0.2,
                    borderWidth: 3,
                    pointRadius: 3,
                    pointBackgroundColor: greenColors[0]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Fonction pour créer le graphique circulaire (pie, non doughnut)
    function createPieChart() {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        const data = {
            labels: @json($productNames),
            datasets: [{
                data: @json($productSales),
                backgroundColor: [...blueColors, ...greenColors, ...otherColors],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        };

        new Chart(ctx, {
            type: 'pie', // Utilise pie au lieu de doughnut pour un cercle plein
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    }

    // Fonction pour créer le graphique en barres
    function createBarChart() {
        const data = @json($produits);
        const ctx = document.getElementById('myChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.produit_nom),
                datasets: [{
                    label: 'Vendus',
                    data: data.map(item => item.total_quantite_vendu),
                    backgroundColor: blueColors[0],
                    borderColor: 'transparent',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                }, {
                    label: 'Invendus',
                    data: data.map(item => item.total_quantite_invendu),
                    backgroundColor: otherColors[0],
                    borderColor: 'transparent',
                    borderWidth: 0,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Fonction utilitaire pour formater les dates
    function formatDate(dateString) {
        const options = { day: 'numeric', month: 'short' };
        return new Date(dateString).toLocaleDateString('fr-FR', options);
    }

    // Initialisation des graphiques
    createLineChart();
    createPieChart();
    createBarChart();
});
</script>
@endsection
