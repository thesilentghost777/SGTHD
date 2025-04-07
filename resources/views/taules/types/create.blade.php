@extends('layouts.app')

@section('content')
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Créer un Type de Taule') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Calculateur automatique -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Calculateur automatique de formules</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="quantite_farine" class="block text-gray-700 text-sm font-bold mb-2">
                            Quantité de farine utilisée (kg):
                        </label>
                        <input type="number" id="quantite_farine" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" step="0.01" min="0">
                    </div>

                    <div>
                        <label for="quantite_eau" class="block text-gray-700 text-sm font-bold mb-2">
                            Quantité d'eau utilisée (L):
                        </label>
                        <input type="number" id="quantite_eau" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" step="0.01" min="0">
                    </div>

                    <div>
                        <label for="quantite_huile" class="block text-gray-700 text-sm font-bold mb-2">
                            Quantité d'huile utilisée (L):
                        </label>
                        <input type="number" id="quantite_huile" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" step="0.01" min="0">
                    </div>

                    <div>
                        <label for="quantite_autres" class="block text-gray-700 text-sm font-bold mb-2">
                            Quantité d'autres ingrédients (kg):
                        </label>
                        <input type="number" id="quantite_autres" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" step="0.01" min="0">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="nombre_taules" class="block text-gray-700 text-sm font-bold mb-2">
                        Nombre de taules produites:
                    </label>
                    <input type="number" id="nombre_taules" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1">
                </div>

                <button type="button" id="calculer_formules" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Calculer les formules
                </button>
            </div>
        </div>

        <!-- Formulaire de création de type de taule -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('taules.types.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="nom" class="block text-gray-700 text-sm font-bold mb-2">
                            Nom du type de taule:
                        </label>
                        <input type="text" name="nom" id="nom" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('nom') }}">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                            Description:
                        </label>
                        <textarea name="description" id="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="formule_farine" class="block text-gray-700 text-sm font-bold mb-2">
                            Formule pour la farine (utilisez 'n' pour le nombre de taules):
                        </label>
                        <input type="text" name="formule_farine" id="formule_farine" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="ex: 0.5 * n" value="{{ old('formule_farine') }}">
                        <p class="text-sm text-gray-500 mt-1">Exemple: Pour 0.5kg de farine par taule, entrez "0.5 * n"</p>
                    </div>

                    <div class="mb-4">
                        <label for="formule_eau" class="block text-gray-700 text-sm font-bold mb-2">
                            Formule pour l'eau:
                        </label>
                        <input type="text" name="formule_eau" id="formule_eau" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="ex: 0.3 * n" value="{{ old('formule_eau') }}">
                    </div>

                    <div class="mb-4">
                        <label for="formule_huile" class="block text-gray-700 text-sm font-bold mb-2">
                            Formule pour l'huile:
                        </label>
                        <input type="text" name="formule_huile" id="formule_huile" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="ex: 0.05 * n" value="{{ old('formule_huile') }}">
                    </div>

                    <div class="mb-4">
                        <label for="formule_autres" class="block text-gray-700 text-sm font-bold mb-2">
                            Formule pour les autres ingrédients:
                        </label>
                        <input type="text" name="formule_autres" id="formule_autres" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="ex: 0.1 * n" value="{{ old('formule_autres') }}">
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Créer
                        </button>
                        <a href="{{ route('taules.types.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calculerButton = document.getElementById('calculer_formules');

        calculerButton.addEventListener('click', function() {
            // Récupérer les valeurs des champs
            const quantiteFarine = parseFloat(document.getElementById('quantite_farine').value) || 0;
            const quantiteEau = parseFloat(document.getElementById('quantite_eau').value) || 0;
            const quantiteHuile = parseFloat(document.getElementById('quantite_huile').value) || 0;
            const quantiteAutres = parseFloat(document.getElementById('quantite_autres').value) || 0;
            const nombreTaules = parseInt(document.getElementById('nombre_taules').value) || 0;

            if (nombreTaules <= 0) {
                alert('Veuillez entrer un nombre de taules valide.');
                return;
            }

            // Calculer les formules (quantité par taule)
            const formuleFarine = (quantiteFarine / nombreTaules).toFixed(3) + ' * n';
            const formuleEau = (quantiteEau / nombreTaules).toFixed(3) + ' * n';
            const formuleHuile = (quantiteHuile / nombreTaules).toFixed(3) + ' * n';
            const formuleAutres = (quantiteAutres / nombreTaules).toFixed(3) + ' * n';

            // Remplir les champs de formulaire
            document.getElementById('formule_farine').value = formuleFarine;
            document.getElementById('formule_eau').value = formuleEau;
            document.getElementById('formule_huile').value = formuleHuile;
            document.getElementById('formule_autres').value = formuleAutres;

            // Afficher un message de confirmation
            alert('Les formules ont été calculées avec succès!');
        });
    });
</script>
@endsection