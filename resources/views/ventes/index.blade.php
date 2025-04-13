@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-r from-blue-50 to-blue-100 py-6">
    <div class="container mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-700 to-blue-900 text-white p-5">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold">Liste Détaillée des Opérations de Vente</h1>
                    <div class="flex space-x-2">
                        <a href="{{ route('ventes.compare') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Comparer Vendeurs
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="mb-6 bg-gray-50 rounded-lg p-4 shadow-sm">
                    <h2 class="text-lg text-blue-800 font-semibold mb-3">Filtres</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                            <input type="text" id="searchInput" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Rechercher...">
                        </div>
                        <div>
                            <label for="typeFilter" class="block text-sm font-medium text-gray-700 mb-1">Type d'opération</label>
                            <select id="typeFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Tous les types</option>
                                <option value="Vente">Vente</option>
                                <option value="Produit invendu">Produit Invendu</option>
                                <option value="Produit Avarie">Produit Avarie</option>
                            </select>
                        </div>
                        <div>
                            <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" id="dateFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div class="flex items-end">
                            <button id="resetFilters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 transition w-full">
                                Réinitialiser
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-blue-800 text-white">
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Serveur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Quantité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Prix</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Monnaie</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($ventes as $vente)
                            <tr data-type="{{ $vente->type }}" data-date="{{ $vente->date_vente }}" class="hover:bg-blue-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $vente->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $vente->date_vente }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $vente->nom_produit ?? 'Non spécifié' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $vente->nom_serveur ?? 'Non spécifié' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $vente->quantite }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $vente->prix ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($vente->prix && $vente->type == 'Vente')
                                        {{ number_format($vente->prix * $vente->quantite, 0, ',', ' ') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($vente->type == 'Vente')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $vente->type }}</span>
                                    @elseif($vente->type == 'Produit invendu')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ $vente->type }}</span>
                                    @elseif($vente->type == 'Produit Avarie')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $vente->type }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $vente->type }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $vente->monnaie ?? 'XAF' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const dateFilter = document.getElementById('dateFilter');
        const resetFilters = document.getElementById('resetFilters');
        const tableRows = document.querySelectorAll('tbody tr');

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const typeValue = typeFilter.value;
            const dateValue = dateFilter.value;

            tableRows.forEach(row => {
                const rowType = row.getAttribute('data-type');
                const rowDate = row.getAttribute('data-date');
                const rowText = row.textContent.toLowerCase();

                const matchesSearch = !searchTerm || rowText.includes(searchTerm);
                const matchesType = !typeValue || rowType === typeValue;
                const matchesDate = !dateValue || rowDate === dateValue;

                if (matchesSearch && matchesType && matchesDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', applyFilters);
        typeFilter.addEventListener('change', applyFilters);
        dateFilter.addEventListener('change', applyFilters);

        resetFilters.addEventListener('click', function() {
            searchInput.value = '';
            typeFilter.value = '';
            dateFilter.value = '';

            tableRows.forEach(row => {
                row.style.display = '';
            });
        });
    });
</script>
@endpush
@endsection
