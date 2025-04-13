@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête avec informations utilisateur -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Gestion des Stocks</h1>
            </div>
        </div>

        <!-- Boutons de navigation -->
        <div class="flex justify-center space-x-4 mb-8">
            <a href="{{ route('chef.matieres.index') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Gérer vos matières premières
            </a>
            <a href="{{ route('chef.produits.index') }}"
               class="inline-flex items-center px-6 py-3 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-offset-2 transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Gérer vos produits
            </a>
        </div>

        <!-- Cartes de statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Statistiques Matières Premières -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 truncate">Matières premières en stock</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($total_matieres, 0, ',', ' ') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Valeur totale</p>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ number_format($valeur_stock_matieres, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                    @if($matiere_max)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm text-gray-500">Stock le plus important</p>
                        <p class="font-medium">{{ $matiere_max->nom }} ({{ $matiere_max->quantite }} {{ $matiere_max->unite_classique }})</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques Produits -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 truncate">Produits en stock</p>
                            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($total_produits, 0, ',', ' ') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Valeur totale</p>
                            <p class="mt-1 text-lg font-medium text-gray-900">{{ number_format($valeur_stock_produits, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                    @if($produit_max)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm text-gray-500">Produit le plus stocké</p>
                        <p class="font-medium">{{ $produit_max->nom }} ({{ $produit_max->quantite_totale }} unités)</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistiques par Catégorie -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition par catégorie</h3>
                    <div class="space-y-4">
                        @foreach($stats['produits_par_categorie'] as $categorie => $stat)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">{{ $categorie }}</span>
                            <span class="text-sm text-gray-900">{{ $stat['nombre_produits'] }} produits</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition des Matières Premières</h3>
                <canvas id="chartMatieres"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition des Produits</h3>
                <canvas id="chartProduits"></canvas>
            </div>
        </div>

        <!-- Tableaux -->
        @include('stock.partials.matieres-table', ['matieres' => $matieres])
        @include('stock.partials.produits-table', ['produits' => $produits])

        <!-- Modal d'ajustement -->
        @include('stock.partials.adjust-quantity-modal')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartColors = [
        '#2563EB', '#059669', '#DC2626', '#D97706', '#7C3AED',
        '#DB2777', '#2563EB', '#059669', '#DC2626', '#D97706'
    ];

    // Configuration des graphiques
    const configMatieres = {
        type: 'doughnut',
        data: {
            labels: @json($data_matieres['labels']),
            datasets: [{
                data: @json($data_matieres['data']),
                backgroundColor: chartColors
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    };

    const configProduits = {
        type: 'doughnut',
        data: {
            labels: @json($data_produits['labels']),
            datasets: [{
                data: @json($data_produits['data']),
                backgroundColor: chartColors
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    };

    // Initialisation des graphiques
    new Chart(document.getElementById('chartMatieres'), configMatieres);
    new Chart(document.getElementById('chartProduits'), configProduits);

    // Recherche dynamique
    const searchMatieres = document.getElementById('searchMatieres');
    const searchProduits = document.getElementById('searchProduits');

    if (searchMatieres) {
        searchMatieres.addEventListener('input', debounce(function(e) {
            filterTable('tableMatieres', e.target.value);
        }, 300));
    }

    if (searchProduits) {
        searchProduits.addEventListener('input', debounce(function(e) {
            filterTable('tableProduits', e.target.value);
        }, 300));
    }
});

function filterTable(tableId, query) {
    const rows = document.querySelectorAll(`#${tableId} tr:not(.header)`);
    const lowercaseQuery = query.toLowerCase();

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(lowercaseQuery) ? '' : 'none';
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endsection
