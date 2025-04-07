@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ajuster un Manquant</h1>
        <a href="{{ route('manquants.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="mdi mdi-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="mdi mdi-information-outline text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="font-medium">Ajustement pour {{ $manquant->employe->name }}</p>
                <p>Fonction: {{ ucfirst($manquant->employe->role) }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('manquants.sauvegarder-ajustement', $manquant->id) }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="montant" class="block text-sm font-medium text-gray-700 mb-1">Montant du Manquant</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number" name="montant" id="montant" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-12 py-3 sm:text-sm border-gray-300 rounded-md" value="{{ $manquant->montant }}" required>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">FCFA</span>
                    </div>
                </div>
                @error('montant')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="explication" class="block text-sm font-medium text-gray-700 mb-1">Explication du Manquant</label>
            <div class="mt-1">
                <pre class="w-full bg-gray-50 p-4 border border-gray-300 rounded-md text-sm text-gray-900 whitespace-pre-wrap">{{ $manquant->explication }}</pre>
            </div>
        </div>

        <div>
            <label for="commentaire_dg" class="block text-sm font-medium text-gray-700 mb-1">Commentaire du DG</label>
            <div class="mt-1">
                <textarea id="commentaire_dg" name="commentaire_dg" rows="4" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ $manquant->commentaire_dg }}</textarea>
            </div>
            <p class="mt-1 text-sm text-gray-500">Explication de l'ajustement ou commentaires supplémentaires</p>
            @error('commentaire_dg')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('manquants.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Annuler
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Enregistrer l'Ajustement
            </button>
        </div>
    </form>
</div>
@endsection
