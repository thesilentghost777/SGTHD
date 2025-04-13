@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'ingrédient') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('recipe.ingredients.update', $ingredient) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nom*</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $ingredient->name) }}" required
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="unit" class="block text-sm font-medium text-gray-700">Unité par défaut</label>
                            <select name="unit" id="unit" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">-- Aucune unité --</option>
                                <option value="g" {{ old('unit', $ingredient->unit) == 'g' ? 'selected' : '' }}>Gramme (g)</option>
                                <option value="kg" {{ old('unit', $ingredient->unit) == 'kg' ? 'selected' : '' }}>Kilogramme (kg)</option>
                                <option value="ml" {{ old('unit', $ingredient->unit) == 'ml' ? 'selected' : '' }}>Millilitre (ml)</option>
                                <option value="l" {{ old('unit', $ingredient->unit) == 'l' ? 'selected' : '' }}>Litre (l)</option>
                                <option value="cs" {{ old('unit', $ingredient->unit) == 'cs' ? 'selected' : '' }}>Cuillère à soupe</option>
                                <option value="cc" {{ old('unit', $ingredient->unit) == 'cc' ? 'selected' : '' }}>Cuillère à café</option>
                                <option value="pièce" {{ old('unit', $ingredient->unit) == 'pièce' ? 'selected' : '' }}>Pièce</option>
                                <option value="pincée" {{ old('unit', $ingredient->unit) == 'pincée' ? 'selected' : '' }}>Pincée</option>
                            </select>

                            @error('unit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('recipe.ingredients.index') }}" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                                Annuler
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

