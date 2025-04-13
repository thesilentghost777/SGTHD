@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Modifier un Sac</h1>
        <p class="text-gray-600 mt-1">Modifiez les informations du sac {{ $bag->name }}</p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('bags.update', $bag) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nom du sac</label>
                <input type="text" name="name" id="name" value="{{ old('name', $bag->name) }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Prix unitaire (FCFA)</label>
                <input type="number" name="price" id="price" value="{{ old('price', $bag->price) }}" required min="0" step="0.01"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('price')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="stock_quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantité en stock</label>
                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $bag->stock_quantity) }}" required min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('stock_quantity')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="alert_threshold" class="block text-gray-700 text-sm font-bold mb-2">Seuil d'alerte</label>
                <input type="number" name="alert_threshold" id="alert_threshold" value="{{ old('alert_threshold', $bag->alert_threshold) }}" required min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-gray-500 text-xs mt-1">Vous serez alerté lorsque le stock descendra en dessous de ce seuil</p>
                @error('alert_threshold')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between">
                <a href="{{ route('bags.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                    Annuler
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
