@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Alertes de stock</h1>
        <a href="{{ route('produits.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            Retour aux produits
        </a>
    </div>

    <div class="bg-red-100 text-red-700 p-4 rounded-md mb-6">
        <h3 class="font-bold text-lg mb-2">Produits en alerte de stock</h3>
        <p>Les produits suivants sont en alerte de stock (quantité sous le seuil d'alerte).</p>
    </div>

    <!-- Tableau des produits en alerte -->
    <div class="bg-white shadow-md rounded-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seuil d'alerte</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($produits as $produit)
                    <tr class="bg-red-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $produit->reference }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $produit->nom }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $produit->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-red-600">
                            {{ $produit->quantite }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $produit->seuil_alerte }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 whitespace-nowrap space-x-2">
                            <a href="{{ route('produits.show', $produit) }}" class="text-indigo-600 hover:text-indigo-900">Détails</a>
                            <button
                                type="button"
                                onclick="openModal('entreeModal{{ $produit->id }}')"
                                class="text-green-600 hover:text-green-900">
                                Entrée
                            </button>
                        </td>
                    </tr>
                    <!-- Modal d'entrée de stock -->
                    <div id="entreeModal{{ $produit->id }}" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
                        <div class="bg-white p-6 rounded-lg max-w-md w-full">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Entrée de stock - {{ $produit->nom }}</h3>
                            <form action="{{ route('stock.entree', $produit) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                                    <input type="number" name="quantite" id="quantite" min="1" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div class="mb-4">
                                    <label for="motif" class="block text-sm font-medium text-gray-700">Motif</label>
                                    <input type="text" name="motif" id="motif" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="closeModal('entreeModal{{ $produit->id }}')"
                                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                                        Annuler
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md">
                                        Confirmer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            Aucun produit en alerte de stock
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $produits->links() }}
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
