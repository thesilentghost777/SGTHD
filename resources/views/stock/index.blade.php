@extends('pages.chef_production.chef_production_default')

@section('page-content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Gestion des Stocks</h1>
        </div>
        <div class="flex justify-center space-x-4">
            <button
                class="relative px-6 py-3 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transition duration-200"
                @mouseenter="$el.classList.add('ring', 'ring-blue-300')"
                @mouseleave="$el.classList.remove('ring', 'ring-blue-300')"
            >
                <a href="{{ route('chef.matieres.index') }}" class="z-10 relative">
                    Gerer vos matières premières
                </a>
                <span
                    class="absolute inset-0 bg-blue-600 rounded-lg opacity-0 transition-opacity duration-300 hover:opacity-20"
                ></span>
            </button>
            <button
                class="relative px-6 py-3 bg-green-500 text-white font-semibold rounded-lg shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-offset-2 transition duration-200"
                @mouseenter="$el.classList.add('ring', 'ring-green-300')"
                @mouseleave="$el.classList.remove('ring', 'ring-green-300')"
            >
                <a href="{{ route('chef.produits.index') }}" class="z-10 relative">
                    Gerer vos produits
                </a>
                <span
                    class="absolute inset-0 bg-green-600 rounded-lg opacity-0 transition-opacity duration-300 hover:opacity-20"
                ></span>
            </button>
        </div>
        <br><br>

        <!-- Statistiques globales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Statistiques Matières Premières -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Matières premières en stock
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ round($total_matieres,1) }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-gray-500">
                            Valeur totale: {{ number_format($valeur_stock_matieres, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                </div>
            </div>

            <!-- Statistiques Produits -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Produits en stock
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ round($total_produits,1) }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3">
                    <div class="text-sm">
                        <span class="font-medium text-gray-500">
                            Valeur totale: {{ number_format($valeur_stock_produits, 0, ',', ' ') }} FCFA
                        </span>
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
        <div class="grid grid-cols-1 gap-6">
            <!-- Matières Premières -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Matières Premières</h3>
                        <div class="relative">
                            <input type="text" id="searchMatieres" placeholder="Rechercher une matière..."
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité<br>(unite,sac,bidon,sachet...)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité par Unité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Unitaire</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tableMatieres">
                                @foreach($matieres as $matiere)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $matiere->nom }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ round($matiere->quantite,1) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ round($matiere->quantite_par_unite,1) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $matiere->unite_classique }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($matiere->prix_unitaire, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="adjustQuantity('matiere', {{ $matiere->id }}, 'add')"
                                                class="text-green-600 hover:text-green-900 mr-2">Ajouter</button>
                                        <button onclick="adjustQuantity('matiere', {{ $matiere->id }}, 'subtract')"
                                                class="text-yellow-600 hover:text-yellow-900">Réduire</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Produits -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Produits</h3>
                        <div class="relative">
                            <input type="text" id="searchProduits" placeholder="Rechercher un produit..."
                                   class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tableProduits">
                                @foreach($produits as $produit)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $produit->nom }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $produit->quantite_totale }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($produit->prix, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="adjustQuantity('produit', {{ $produit->id }}, 'add')"
                                                class="text-green-600 hover:text-green-900 mr-2">Ajouter</button>
                                        <button onclick="adjustQuantity('produit', {{ $produit->id }}, 'subtract')"
                                                class="text-yellow-600 hover:text-yellow-900">Réduire</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajustement de quantité -->
<div id="adjustQuantityModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-8 max-w-md w-full">
<!-- Suite du Modal d'ajustement de quantité -->
<h2 class="text-xl font-bold mb-4" id="adjustQuantityModalTitle">Ajuster la quantité</h2>
<form id="adjustQuantityForm">
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Quantité à ajuster</label>
        <input type="number" step="0.01" id="adjustQuantityInput" name="quantite"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>
    <div class="flex justify-end space-x-3">
        <button type="button" onclick="closeAdjustQuantityModal()"
                class="bg-gray-200 px-4 py-2 rounded-md text-gray-700">Annuler</button>
        <button type="submit"
                class="bg-blue-600 px-4 py-2 rounded-md text-white">Confirmer</button>
    </div>
</form>
</div>
</div>
</div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Déplacer tout le code dans un gestionnaire DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let currentEditId = null;
    let currentEditType = null;
    let adjustQuantityData = {
        type: null,
        id: null,
        operation: null
    };

    // Initialiser les graphiques
    initializeCharts();

    // Initialiser les écouteurs d'événements
    initializeEventListeners();

    function initializeCharts() {
        const configMatieres = {
            type: 'doughnut',
            data: {
                labels: @json($data_matieres['labels']),
                datasets: [{
                    data: @json($data_matieres['data']),
                    backgroundColor: [
                        '#2563EB', '#059669', '#DC2626', '#D97706', '#7C3AED',
                        '#DB2777', '#2563EB', '#059669', '#DC2626', '#D97706'
                    ]
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
                    backgroundColor: [
                        '#2563EB', '#059669', '#DC2626', '#D97706', '#7C3AED',
                        '#DB2777', '#2563EB', '#059669', '#DC2626', '#D97706'
                    ]
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

        const chartMatieres = document.getElementById('chartMatieres');
        const chartProduits = document.getElementById('chartProduits');

        if (chartMatieres) new Chart(chartMatieres, configMatieres);
        if (chartProduits) new Chart(chartProduits, configProduits);
    }

    function initializeEventListeners() {
        // Formulaires
        const adjustQuantityForm = document.getElementById('adjustQuantityForm');


        if (adjustQuantityForm) {
            adjustQuantityForm.addEventListener('submit', handleAdjustQuantitySubmit);
        }

        // Champs de recherche
        const searchMatieres = document.getElementById('searchMatieres');
        const searchProduits = document.getElementById('searchProduits');

        if (searchMatieres) {
            searchMatieres.addEventListener('input', debounce(function(e) {
                searchMatieresData(e.target.value);
            }, 300));
        }

        if (searchProduits) {
            searchProduits.addEventListener('input', debounce(function(e) {
                searchProduitsData(e.target.value);
            }, 300));
        }
    }


    window.adjustQuantity = function(type, id, operation) {
        adjustQuantityData = { type, id, operation };
        const modalTitle = document.getElementById('adjustQuantityModalTitle');
        const modal = document.getElementById('adjustQuantityModal');
        const input = document.getElementById('adjustQuantityInput');

        if (modalTitle) {
            modalTitle.textContent = `${operation === 'add' ? 'Ajouter' : 'Réduire'} la quantité`;
        }
        if (modal) modal.classList.remove('hidden');
        if (input) input.value = '';
    };

    window.closeAdjustQuantityModal = function() {
        const modal = document.getElementById('adjustQuantityModal');
        if (modal) modal.classList.add('hidden');
        adjustQuantityData = { type: null, id: null, operation: null };
    };

    // Fonctions utilitaires
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

    async function handleAdjustQuantitySubmit(e) {
        e.preventDefault();

        const quantiteInput = document.getElementById('adjustQuantityInput');
        const quantite = quantiteInput ? quantiteInput.value : null;

        if (!quantite) {
            alert('Veuillez entrer une quantité');
            return;
        }

        const url = adjustQuantityData.type === 'matiere'
            ? `/stock/adjust-matiere-quantity/${adjustQuantityData.id}`
            : `/stock/adjust-produit-quantity/${adjustQuantityData.id}`;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    quantite: parseFloat(quantite),
                    operation: adjustQuantityData.operation
                })
            });

            const data = await response.json();
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            alert('Une erreur est survenue');
        }
    }
});

</script>
