@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Réserver des Matières Premières</h1>

        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if($errors->any())
            <x-alert type="error" :message="$errors->first()" />
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('producteur.reservations.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Matière Première
                        </label>
                        <select name="matiere_id" required class="form-select rounded-md shadow-sm border-gray-300 w-full">
                            <option value="">Sélectionner une matière</option>
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}">
                                    {{ $matiere->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Quantité Demandée
                        </label>
                        <input type="number"
                               name="quantite_demandee"
                               step="0.001"
                               required
                               class="form-input rounded-md shadow-sm border-gray-300 w-full" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Unité
                        </label>
                        <x-unite-select name="unite_demandee" required />
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Envoyer la demande de réservation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
