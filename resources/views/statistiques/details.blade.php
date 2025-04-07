@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-10">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6">
        <div class="flex justify-between items-center w-full">
            <h1 class="text-3xl font-bold text-white">Statistiques de Production</h1>
            <div class="flex gap-4">
                <a href="{{ route('employee.performance') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition-colors duration-200 font-medium">
                    Voir statistiques par producteur
                </a>
                <a href="{{ route('statistiques.details') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition-colors duration-200 font-medium">
                    Voir statistiques ultra détaillées
                </a>
            </div>
        </div>
    </div>


    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <form action="{{ route('statistiques.details') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Producteur</label>
                <select name="producteur" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">Tous les producteurs</option>
                    @foreach($producteurs as $producteur)
                        <option value="{{ $producteur->id }}" {{ request('producteur') == $producteur->id ? 'selected' : '' }}>
                            {{ $producteur->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Produit</label>
                <select name="produit" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">Tous les produits</option>
                    @foreach($produits as $produit)
                        <option value="{{ $produit->code_produit }}" {{ request('produit') == $produit->code_produit ? 'selected' : '' }}>
                            {{ $produit->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 lg:col-span-4 flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Tableau des résultats -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-100">
                    <tr>
                        @foreach(['Lot ID', 'Date', 'Producteur', 'Produit', 'Quantité', 'Prix Vente', 'Coût', 'Bénéfice', 'Respect', 'Gaspillage'] as $header)
                            <th class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($productions as $prod)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $prod['id_lot'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $prod['date_production'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $prod['producteur'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $prod['produit'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($prod['quantite'], 0) }}</td>
                        <td class="px-6 py-4 text-sm text-blue-600">{{ number_format($prod['chiffre_affaires'], 0) }} F</td>
                        <td class="px-6 py-4 text-sm text-red-600">{{ number_format($prod['cout_production'], 0) }} F</td>
                        <td class="px-6 py-4 text-sm {{ $prod['benefice_brut'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($prod['benefice_brut'], 0) }} F
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($prod['taux_respect'] !== null)
                                <span class="px-2 inline-flex text-xs font-semibold rounded-full {{ $prod['taux_respect'] >= 100 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ number_format($prod['taux_respect'], 1) }}%
                                </span>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                {{ number_format($prod['taux_gaspillage'], 1) }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
