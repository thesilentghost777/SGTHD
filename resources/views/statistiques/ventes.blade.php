@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gray-50">
    <!-- En-tête -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6">
        <h1 class="text-3xl font-bold text-white">Tableau de bord des statistiques</h1>
        <p class="text-blue-100 mt-2">Vue d'ensemble des performances et indicateurs clés</p>
    </div>

    <!-- Conteneur principal -->
    <div class="container mx-auto px-4 py-8">
        <!-- Section 1: Chiffres clés -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Chiffres clés</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- CA Journalier -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <h3 class="text-gray-500 text-sm font-semibold">CA Journalier</h3>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($chiffreAffaires['journalier'], 0, ',', ' ') }} XAF</p>
                    <div class="mt-2 text-sm">
                        @if($chiffreAffaires['hebdomadaire'] > 0)
                            <span class="text-green-600">
                                <i class="fas fa-arrow-up"></i> +{{ number_format(($chiffreAffaires['journalier'] / $chiffreAffaires['hebdomadaire']) * 100, 1) }}%
                            </span>
                        @else
                            <span class="text-gray-500">INDISPONIBLE</span>
                        @endif
                    </div>
                </div>

                <!-- CA Hebdomadaire -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <h3 class="text-gray-500 text-sm font-semibold">CA Hebdomadaire</h3>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($chiffreAffaires['hebdomadaire'], 0, ',', ' ') }} XAF</p>
                </div>

                <!-- CA Mensuel -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                    <h3 class="text-gray-500 text-sm font-semibold">CA Mensuel</h3>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($chiffreAffaires['mensuel'], 0, ',', ' ') }} XAF</p>
                </div>

                <!-- Total Versements -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                    <h3 class="text-gray-500 text-sm font-semibold">Total Versements</h3>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($versements['total'], 0, ',', ' ') }} XAF</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Graphiques de performance -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Performance des ventes</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Évolution des ventes -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Évolution des ventes</h3>
                    <div style="height: 300px; width: 100%;">
                        <canvas id="evolutionVentes"></canvas>
                    </div>
                </div>

                <!-- Top 5 des produits vendus -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 5 des produits vendus</h3>
                    <div style="height: 300px; width: 100%;">
                        <canvas id="produitsPopulaires"></canvas>
                    </div>
                </div>
            </div>

            <!-- Courbes de Vente -->
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2" x-data="chartData()">
            <h2 class="text-xl font-semibold text-blue-600 mb-6">Évolution des Ventes par mois</h2>
            <div class="h-96">
                <canvas id="ventesChart"></canvas>
            </div>
        </div>
        <br>
        </div>
        <br><br>
        <!-- Section 3: Gestion des stocks -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Gestion des stocks et alertes</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- État des stocks -->
                <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">État des stocks</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Invendu</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avarié</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($stocks as $stock)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->produit->nom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stock->quantite_en_stock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stock->quantite_invendu }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">{{ $stock->quantite_avarie }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Alertes Ruptures de Stock -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                    <h2 class="text-lg font-semibold text-red-600 mb-4">Alertes Ruptures de Stock</h2>
                    <div class="space-y-4">
                        @foreach($rupturesPotentielles as $rupture)
                            <div class="p-3 bg-red-50 rounded-md">
                                <div class="flex justify-between items-center">
                                    <span class="text-red-700">Produit #{{ $rupture->id_produit }}</span>
                                    <span class="text-red-600 font-medium">
                                        Stock: {{ $rupture->quantite_en_stock }}
                                    </span>
                                </div>
                                <div class="mt-1 text-sm text-red-600">
                                    Vente moyenne: {{ number_format($rupture->vente_moyenne, 1) }}/jour
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Analyses détaillées -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Performance des Serveurs -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-blue-600 mb-4">Performance des Serveurs</h2>
                <div class="space-y-6">
                    @foreach($performanceServeurs as $serveur)
                        <div class="border-b pb-4 last:border-0">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-lg font-medium text-gray-700">{{ $serveur->nom_serveur }}</span>
                                <span class="text-green-600 font-semibold">
                                    {{ number_format($serveur->chiffre_affaires) }} XAF
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                Total ventes: {{ number_format($serveur->total_ventes) }} unités
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Proportion Reçu/Vendu -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-blue-600 mb-4">Proportion Reçu/Vendu</h2>
                <div class="space-y-4">
                    @foreach($proportionRecuVendu as $proportion)
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span class="font-medium">{{ $proportion->nom_produit }}</span>
                                <span>{{ number_format(($proportion->total_vendu / $proportion->total_recu) * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full"
                                     style="width: {{ ($proportion->total_vendu / $proportion->total_recu) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top 5 Produits Avariés -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-red-600 mb-4">Top 5 Produits Avariés</h2>
                <div class="space-y-4">
                    @foreach($topProduitsAvaries as $produit)
                        <div class="relative">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700">{{ $produit->nom }}</span>
                                <span class="text-red-600">{{ number_format($produit->pourcentage_avarie, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-red-600 h-2.5 rounded-full"
                                     style="width: {{ min($produit->pourcentage_avarie, 100) }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Section 5: Évolution et tendances -->
        <div class="mt-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Évolution et tendances</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Évolution annuelle -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Évolution annuelle</h3>
                    <canvas id="evolutionAnnuelle" class="w-full h-64"></canvas>
                </div>

                <!-- Évolution des Versements -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-blue-600 mb-4">Évolution Versements</h3>
                    <div class="space-y-4">
                        @foreach($evolutionVersements as $versement)
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-gray-600">{{ $versement->date }}</span>
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full
                                        {{ $versement->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $versement->status }}
                                    </span>
                                </div>
                                <span class="font-medium">{{ number_format($versement->total) }} XAF</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des couleurs
    const colors = {
        blue: 'rgb(59, 130, 246)',
        green: 'rgb(16, 185, 129)',
        purple: 'rgb(139, 92, 246)',
        yellow: 'rgb(245, 158, 11)',
        red: 'rgb(239, 68, 68)'
    };

    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    };

    // Évolution des ventes
    new Chart(document.getElementById('evolutionVentes'), {
        type: 'line',
        data: {
            labels: {!! json_encode($evolutionVentes->pluck('date')) !!},
            datasets: [{
                label: 'Chiffre d\'affaires',
                data: {!! json_encode($evolutionVentes->pluck('total')) !!},
                borderColor: colors.blue,
                tension: 0.4
            }]
        },
        options: defaultOptions
    });

    // Performance des serveurs
    new Chart(document.getElementById('performanceServeurs'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($performanceServeurs->pluck('user.name')) !!},
            datasets: [{
                label: 'Chiffre d\'affaires',
                data: {!! json_encode($performanceServeurs->pluck('chiffre_affaires')) !!},
                backgroundColor: colors.green
            }]
        },
        options: defaultOptions
    });

    // Produits populaires
    new Chart(document.getElementById('produitsPopulaires'), {

type: 'doughnut',

data: {

    labels: {!! json_encode($produitsPopulaires->pluck('nom_produit')) !!},

    datasets: [{

        data: {!! json_encode($produitsPopulaires->pluck('total_vendu')) !!},

        backgroundColor: [colors.blue, colors.green, colors.purple, colors.yellow, colors.red]

    }]

},

options: {

    ...defaultOptions,

    cutout: '70%',

    plugins: {

        ...defaultOptions.plugins,

        tooltip: {

            callbacks: {

                label: function(context) {

                    const label = context.label || '';

                    const value = context.raw || 0;

                    return `${label}: ${value} unités`;

                }

            }

        }

    }

}

});


    // Évolution annuelle
    new Chart(document.getElementById('evolutionAnnuelle'), {
        type: 'line',
        data: {
            labels: {!! json_encode($evolutionAnnuelle->map(function($item) {
                return date('F Y', mktime(0, 0, 0, $item->mois, 1, $item->annee));
            })) !!},
            datasets: [{
                label: 'Chiffre d\'affaires',
                data: {!! json_encode($evolutionAnnuelle->pluck('total')) !!},
                borderColor: colors.purple,
                tension: 0.4
            }]
        },
        options: defaultOptions
    });
});

function chartData() {

return {

    periode: 'mois',

    init() {

        const ctx = document.getElementById('ventesChart').getContext('2d');

        const ventesData = @json($ventesParMois);

        // Préparation des données pour le graphique

        const mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

        // Grouper les données par produit

        const produitsUniques = [...new Set(ventesData.map(item => item.produit))];

        const datasets = produitsUniques.map(produit => {

            const donneesProduit = ventesData.filter(item => item.produit === produit);

            return {

                label: produit,

                data: donneesProduit.map(item => item.total_ventes),

                borderColor: this.getRandomColor(),

                fill: false,

                tension: 0.4

            };

        });

        new Chart(ctx, {

            type: 'line',

            data: {

                labels: mois,

                datasets: datasets

            },

            options: {

                responsive: true,

                plugins: {

                    legend: {

                        position: 'top',

                    },

                    title: {

                        display: true,

                        text: 'Évolution des ventes par produit'

                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        title: {

                            display: true,

                            text: 'Quantité vendue'

                        }

                    }

                }

            }

        });

    },

    getRandomColor() {

        const letters = '0123456789ABCDEF';

        let color = '#';

        for (let i = 0; i < 6; i++) {

            color += letters[Math.floor(Math.random() * 16)];

        }

        return color;

    }

}

}
</script>
@endsection
