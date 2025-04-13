<!-- resources/views/delis/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Modifier le Deli</h1>

        <form action="{{ route('delis.update', $deli) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom du deli</label>
                    <input type="text" name="nom" id="nom" value="{{ $deli->nom }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $deli->description }}</textarea>
                </div>

                <div>
                    <label for="montant" class="block text-sm font-medium text-gray-700">Montant (F CFA)</label>
                    <input type="number" name="montant" id="montant" value="{{ $deli->montant }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="date_incident" class="block text-sm font-medium text-gray-700">Date de l'incident</label>
                    <input type="date" name="date_incident" id="date_incident"
                           value="{{ $deli->employes->first()->pivot->date_incident ?? '' }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Employés concernés</label>
                    <div class="mt-2 space-y-2">
                        @foreach($employes as $employe)
                        <div class="flex items-center">
                            <input type="checkbox" name="employes[]" value="{{ $employe->id }}"
                                   {{ $deli->employes->contains($employe) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label class="ml-2 text-sm text-gray-600">{{ $employe->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('delis.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Annuler
                </a>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
