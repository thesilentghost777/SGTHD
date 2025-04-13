@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Modifier le Versement</h2>
            </div>

            <form action="{{ route('versements.update', $versement) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="libelle" class="block text-sm font-medium text-gray-700 mb-2">
                        Libellé
                    </label>
                    <input type="text"
                           name="libelle"
                           id="libelle"
                           class="form-input w-full rounded-md shadow-sm"
                           value="{{ old('libelle', $versement->libelle) }}"
                           required>
                    @error('libelle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="montant" class="block text-sm font-medium text-gray-700 mb-2">
                        Montant (FCFA)
                    </label>
                    <input type="number"
                           name="montant"
                           id="montant"
                           class="form-input w-full rounded-md shadow-sm"
                           value="{{ old('montant', $versement->montant) }}"
                           required>
                    @error('montant')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('versements.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md text-sm font-medium hover:bg-blue-600">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
