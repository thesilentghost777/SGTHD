<!-- resources/views/delis/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête avec informations principales -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-blue-600">{{ $deli->nom }}</h1>
                    <p class="text-gray-600 mt-2">Créé le {{ $deli->created_at->format('d/m/Y') }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('delis.edit', $deli) }}"
                       class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        Modifier
                    </a>
                    <form action="{{ route('delis.destroy', $deli) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce deli ?')">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Détails du deli -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations générales -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-blue-600 mb-4">Informations</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                        <p class="mt-1 text-gray-900">{{ $deli->description }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Montant</h3>
                        <p class="mt-1 text-gray-900 text-lg font-semibold">
                            {{ number_format($deli->montant, 0, ',', ' ') }} F CFA
                        </p>
                    </div>
                </div>
            </div>

            <!-- Employés concernés -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-blue-600 mb-4">Employés concernés</h2>
                <div class="space-y-4">
                    @foreach($deli->employes as $employe)
                        <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $employe->name }}</h3>
                                <p class="text-sm text-gray-500">Date de l'incident : {{ $employe->pivot->date_incident }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
