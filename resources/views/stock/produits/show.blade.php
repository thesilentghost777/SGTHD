@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('produits.index') }}" class="mr-4 text-blue-600 hover:text-blue-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold">Détails du produit: {{ $produit->nom }}</h1>
    </div>

    <div class="bg-white shadow-md rounded-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Référence</p>
                <p class="text-lg font-medium">{{ $produit->reference }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Type</p>
                <p class="text-lg font-medium">{{ $produit->type }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Quantité en stock</p>
                <p class="text-lg font-medium {{ $produit->quantite < $produit->seuil_alerte ? 'text-red-600' : '' }}">
                    {{ $produit->quantite }}
                    @if($produit->quantite < $produit->seuil_alerte)
                        <span class="text-sm text-red-600">(Stock bas)</span>
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Prix unitaire</p>
                <p class="text-lg font-medium">{{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Seuil d'alerte</p>
                <p class="text-lg font-medium">{{ $produit->seuil_alerte }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Valeur totale</p>
                <p class="text-lg font-medium">{{ number_format($produit->quantite * $produit->prix_unitaire, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>

        <div class="mt-6 flex space-x-4">
            <a href="{{ route('produits.edit', $produit) }}"
                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                Modifier
            </a>
            <button type="button" onclick="openModal('entreeModal')"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Entrée de stock
            </button>
            <button type="button" onclick="openModal('sortieModal')"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                {{ $produit->quantite <= 0 ? 'disabled' : '' }}>
                Sortie de stock
            </button>
            <button type="button" onclick="openModal('deleteModal')"
                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                Supprimer
            </button>
        </div>
    </div>

    <!-- Historique des mouvements -->
    <h2 class="text-xl font-semibold mb-4">Historique des mouvements récents</h2>
    <div class="bg-white shadow-md rounded-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($mouvements as $mouvement)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $mouvement->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $mouvement->type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $mouvement->type === 'entree' ? 'Entrée' : 'Sortie' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $mouvement->quantite }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $mouvement->user->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $mouvement->motif }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            Aucun mouvement de stock récent
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-center">
        <a href="{{ route('stock.mouvements') }}?produit_id={{ $produit->id }}" class="text-blue-600 hover:text-blue-800">
            Voir tous les mouvements de ce produit
        </a>
    </div>

    <!-- Modals -->
    <!-- Modal d'entrée de stock -->
    <div id="entreeModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
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
                    <button type="button" onclick="closeModal('entreeModal')"
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

    <!-- Modal de sortie de stock -->
    <div id="sortieModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg max-w-md w-full">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sortie de stock - {{ $produit->nom }}</h3>
            <form action="{{ route('stock.sortie', $produit) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                    <input type="number" name="quantite" id="quantite" min="1" max="{{ $produit->quantite }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="motif" class="block text-sm font-medium text-gray-700">Motif</label>
                    <input type="text" name="motif" id="motif" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('sortieModal')"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de suppression -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg max-w-md w-full">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer la suppression</h3>
            <p class="text-sm text-gray-500 mb-4">
                Êtes-vous sûr de vouloir supprimer ce produit ? Cette action ne peut pas être annulée.
            </p>
            <form action="{{ route('produits.destroy', $produit) }}" method="POST" class="flex justify-end space-x-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('deleteModal')"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">
                    Supprimer
                </button>
            </form>
        </div>
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
