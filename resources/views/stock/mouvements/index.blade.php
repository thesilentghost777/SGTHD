@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Historique des mouvements de stock</h1>
        <a href="{{ route('produits.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            Retour aux produits
        </a>
    </div>

    <!-- Filtres -->
    <div class="mb-6 p-4 bg-white rounded-md shadow">
        <form action="{{ route('stock.mouvements') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="produit_id" class="block text-sm font-medium text-gray-700 mb-1">Produit</label>
                <select name="produit_id" id="produit_id"
                    class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tous les produits</option>
                    @foreach(\App\Models\Produit::orderBy('nom')->get() as $p)
                        <option value="{{ $p->id }}" {{ request('produit_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->reference }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type"
                    class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tous</option>
                    <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>Entrées</option>
                    <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>Sorties</option>
                </select>
            </div>
            <div class="w-full sm:w-auto">
                <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" name="date_debut" id="date_debut" value="{{ request('date_debut') }}"
                    class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="w-full sm:w-auto">
                <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input type="date" name="date_fin" id="date_fin" value="{{ request('date_fin') }}"
                    class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="w-full sm:w-auto flex items-end">
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Tableau des mouvements -->
    <div class="bg-white shadow-md rounded-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
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
                            <a href="{{ route('produits.show', $mouvement->produit) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $mouvement->produit->nom }}
                            </a>
                            <div class="text-xs text-gray-500">{{ $mouvement->produit->reference }}</div>
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
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Aucun mouvement de stock trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $mouvements->links() }}
    </div>
</div>
@endsection
