@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Facturer un Manquant</h1>
        <a href="{{ route('manquants.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="mdi mdi-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <form action="{{ route('manquant.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="employe_id" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
            <select name="employe_id" id="employe_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                <option value="">Sélectionner un producteur</option>
                @foreach($producteurs as $producteur)
                    <option value="{{ $producteur->id }}">{{ $producteur->name }}</option>
                @endforeach
            </select>
            @error('employe_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="montant" class="block text-sm font-medium text-gray-700 mb-1">Montant du Manquant</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="number" name="montant" id="montant" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-12 py-2 sm:text-sm border-gray-300 rounded-md" placeholder="0" required>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">FCFA</span>
                </div>
            </div>
            @error('montant')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="explication" class="block text-sm font-medium text-gray-700 mb-1">Explication du Manquant</label>
            <div class="mt-1">
                <textarea id="explication" name="explication" rows="4" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Détaillez la raison de ce manquant..." required></textarea>
            </div>
            @error('explication')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('manquants.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Facturer le Manquant
            </button>
        </div>
    </form>
</div>
@endsection