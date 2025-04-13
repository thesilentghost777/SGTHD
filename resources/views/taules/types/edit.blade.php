@extends('layouts.app')

@section('content')

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Modifier le Type de Taule') }}
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

                <form action="{{ route('taules.types.update', $type) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="nom" class="block text-gray-700 text-sm font-bold mb-2">
                            Nom du type de taule:
                        </label>
                        <input type="text" name="nom" id="nom" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required value="{{ old('nom', $type->nom) }}">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                            Description:
                        </label>
                        <textarea name="description" id="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $type->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="formule_farine" class="block text-gray-700 text-sm font-bold mb-2">
                            Formule pour la farine (utilisez 'n' pour le nombre de taules):
                        </label>
                        <input type="text" name="formule_farine" id="formule_farine" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="ex: 0.5 * n" value="{{ old('formule_farine', $type->formule_farine) }}">
                        <p class="text-sm text-gray-500 mt-1">Exemple: Pour 0.5kg de farine par taule, entrez "0.5 * n"</p>
                    </div>

                    <div class="mb-4">
                        <label for="formule_eau" class="block text-gray-700 text-sm font-bold mb-2">
                            Formule pour l'eau:
                        </label>
                        <input type="text" name="formule_eau" id="formule_eau" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="ex: 0.3 * n" value="{{ old('formule_eau', $type->formule_eau) }}">
                    </div>

                    <div class="mb-4">
                        <label for="formule_huile" class="block text-gray-700 text-sm font-bold mb-2">
                            Formule pour l'huile:
                        </label>
                        <input type="text" name="formule_huile" id="formule_huile" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="ex: 0.05 * n" value="{{ old('formule_huile', $type->formule_huile) }}">
                    </div>

                    <div class="mb-4">
                        <label for="formule_autres" class="block text-gray-700 text-sm font-bold mb-2">
                            Formule pour les autres ingrédients:
                        </label>
                        <input type="text" name="formule_autres" id="formule_autres" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="ex: 0.1 * n" value="{{ old('formule_autres', $type->formule_autres) }}">
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Mettre à jour
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
@endsection