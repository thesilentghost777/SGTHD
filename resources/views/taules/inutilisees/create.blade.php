@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Déclarer des Taules Inutilisées') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                            @if(session('errorDetails'))
                                <details class="mt-2 text-sm">
                                    <summary class="cursor-pointer">Voir les détails techniques</summary>
                                    <pre class="mt-2 p-2 bg-red-50 rounded overflow-auto">{{ session('errorDetails') }}</pre>
                                </details>
                            @endif
                        </div>
                    @endif

                    <!-- Formulaire de calcul des matières -->
                    <form action="{{ route('taules.inutilisees.calculer') }}" method="POST" id="calcul-form">
                        @csrf

                        <div class="mb-4">
                            <label for="type_taule_id" class="block text-gray-700 text-sm font-bold mb-2">
                                Type de taule:
                            </label>
                            <select name="type_taule_id" id="type_taule_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">Sélectionnez un type de taule</option>
                                @foreach($typesTaules as $type)
                                    <option value="{{ $type->id }}"
                                        data-farine="{{ $type->formule_farine }}"
                                        data-eau="{{ $type->formule_eau }}"
                                        data-huile="{{ $type->formule_huile }}"
                                        data-autres="{{ $type->formule_autres }}"
                                        {{ old('type_taule_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="nombre_taules" class="block text-gray-700 text-sm font-bold mb-2">
                                Nombre de taules inutilisées:
                            </label>
                            <input type="number" name="nombre_taules" id="nombre_taules" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('nombre_taules', 1) }}">
                        </div>

                        <div class="mb-4">
                            <button id="calcul_button" type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Calculer les matières
                            </button>
                        </div>
                    </form>

                    <!-- Affichage des résultats (s'il y en a) -->
                    @if(session('matieres'))
                        <div id="resultats_calcul" class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Résultats du calcul</h3>

                            <div class="bg-gray-100 p-4 rounded">
                                <table class="min-w-full">
                                    <thead>
                                        <tr>
                                            <th class="text-left font-medium text-gray-700 py-2">Matière</th>
                                            <th class="text-left font-medium text-gray-700 py-2">Quantité</th>
                                            <th class="text-left font-medium text-gray-700 py-2">Prix</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('matieres') as $matiere)
                                            <tr>
                                                <td class="py-2">{{ $matiere['nom'] }}</td>
                                                <td class="py-2">{{ number_format($matiere['quantite'], 2) }} {{ $matiere['unite'] }}</td>
                                                <td class="py-2">{{ number_format($matiere['prix'], 0) }} FCFA</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="border-t">
                                            <td class="py-2 font-bold">Total</td>
                                            <td></td>
                                            <td class="py-2 font-bold">{{ number_format(session('prixTotal'), 0) }} FCFA</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Formulaire d'enregistrement -->
                            <form action="{{ route('taules.inutilisees.store') }}" method="POST" class="mt-4" id="taules-form">
                                @csrf
                                <input type="hidden" name="type_taule_id" value="{{ session('typeTaule')->id }}">
                                <input type="hidden" name="nombre_taules" value="{{ session('nombreTaules') }}">

                                <div class="flex justify-end">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                        Enregistrer ces taules inutilisées
                                    </button>

                                    <a href="{{ route('taules.inutilisees.index') }}" class="ml-2 inline-block align-baseline font-bold py-2 px-4 text-blue-500 hover:text-blue-800">
                                        Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="mt-4 border-t pt-4">
                            <p class="text-gray-600 italic">Veuillez sélectionner un type de taule et indiquer le nombre de taules pour calculer les matières premières nécessaires.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
