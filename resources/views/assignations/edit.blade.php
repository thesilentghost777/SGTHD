@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier une assignation</h1>
        <p class="text-gray-600">Mise à jour de l'assignation de matière pour {{ $assignation->producteur->name }}</p>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-800">Informations de l'assignation</h2>
            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Producteur :</p>
                    <p class="font-medium">{{ $assignation->producteur->name }} ({{ ucfirst($assignation->producteur->role) }})</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Matière première :</p>
                    <p class="font-medium">{{ $assignation->matiere->nom }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('assignations.update', $assignation->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="quantite" class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                    <input type="number" id="quantite" name="quantite" value="{{ $assignation->quantite_assignee }}" step="0.001" min="0.001" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div>
                    <label for="unite" class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                    <select id="unite" name="unite" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        @foreach(array_keys($unites) as $unite)
                            <option value="{{ $unite }}" {{ $assignation->unite_assignee == $unite ? 'selected' : '' }}>
                                {{ strtoupper($unite) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date_limite" class="block text-sm font-medium text-gray-700 mb-1">Date limite d'utilisation (optionnel)</label>
                    <input type="date" id="date_limite" name="date_limite" value="{{ $assignation->date_limite_utilisation ? $assignation->date_limite_utilisation->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('assignations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
