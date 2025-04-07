@extends('layouts.app')

@section('title', 'Déclarer des Sacs Avariés')

@section('content')
<div class="container mx-auto py-6">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-blue-700">
                <i class="fas fa-exclamation-triangle mr-2"></i> Déclarer des Sacs Avariés
            </h1>
            <p class="text-gray-600 mt-2">Veuillez indiquer la quantité de sacs avariés à déduire du stock.</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h2 class="text-lg font-semibold text-blue-800 mb-2">Informations sur le sac</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Nom du sac</p>
                    <p class="font-medium">{{ $bag->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Prix unitaire</p>
                    <p class="font-medium">{{ number_format($bag->price, 2) }} XAF</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Stock disponible</p>
                    <p class="font-medium">{{ $bag->stock_quantity }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Seuil d'alerte</p>
                    <p class="font-medium">{{ $bag->alert_threshold }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('damaged-bags.store', $bag->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="damaged_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantité de sacs avariés</label>
                <input
                    type="number"
                    name="damaged_quantity"
                    id="damaged_quantity"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('damaged_quantity') border-red-500 @enderror"
                    min="1"
                    max="{{ $bag->stock_quantity }}"
                    value="{{ old('damaged_quantity', 1) }}"
                    required
                >
                @error('damaged_quantity')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Maximum: {{ $bag->stock_quantity }} sacs</p>
            </div>

            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Motif de l'avarie</label>
                <select
                    name="reason"
                    id="reason"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('reason') border-red-500 @enderror"
                    required
                >
                    <option value="">Sélectionner un motif</option>
                    <option value="Défaut de fabrication" {{ old('reason') == 'Défaut de fabrication' ? 'selected' : '' }}>Défaut de fabrication</option>
                    <option value="Endommagé lors du transport" {{ old('reason') == 'Endommagé lors du transport' ? 'selected' : '' }}>Endommagé lors du transport</option>
                    <option value="Endommagé lors du stockage" {{ old('reason') == 'Endommagé lors du stockage' ? 'selected' : '' }}>Endommagé lors du stockage</option>
                    <option value="Humidité/Dégât des eaux" {{ old('reason') == 'Humidité/Dégât des eaux' ? 'selected' : '' }}>Humidité/Dégât des eaux</option>
                    <option value="Autre" {{ old('reason') == 'Autre' ? 'selected' : '' }}>Autre</option>
                </select>
                @error('reason')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('damaged-bags.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-md hover:from-blue-700 hover:to-blue-800 transition">
                    Valider l'avarie
                </button>
            </div>
        </form>
    </div>
</div>
@endsection