@extends('layouts.app')

@section('title', 'Gestion des Sacs Avariés')

@section('content')
<div class="container mx-auto py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-blue-700">
                <i class="fas fa-trash-alt mr-2"></i> Gestion des Sacs Avariés
            </h1>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="mb-4 bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
            <p class="text-blue-700">
                <i class="fas fa-info-circle mr-2"></i> Cette interface vous permet de déclarer des sacs avariés.
                La quantité déclarée sera automatiquement déduite du stock disponible.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-left text-xs font-medium text-white uppercase tracking-wider">Nom du Sac</th>
                        <th class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-400 text-left text-xs font-medium text-white uppercase tracking-wider">Prix Unitaire</th>
                        <th class="px-6 py-3 bg-gradient-to-r from-blue-400 to-green-500 text-left text-xs font-medium text-white uppercase tracking-wider">Stock Disponible</th>
                        <th class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-left text-xs font-medium text-white uppercase tracking-wider">Seuil d'Alerte</th>
                        <th class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-left text-xs font-medium text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bags as $bag)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $bag->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($bag->price, 2) }} XAF</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bag->stock_quantity <= $bag->alert_threshold ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $bag->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bag->alert_threshold }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('damaged-bags.create', $bag->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded-md transition">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Déclarer avarie
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun sac en stock actuellement.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection