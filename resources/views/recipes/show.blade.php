@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $recipe->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('recipes.edit', $recipe) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('recipes.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition">
                    {{ __('Retour à la liste') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Informations générales -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Informations générales</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500">Catégorie</p>
                                <p class="font-medium">{{ $recipe->category ? $recipe->category->name : 'Non catégorisé' }}</p>

                                <p class="mt-4 text-sm text-gray-500">Niveau de difficulté</p>
                                <p class="font-medium">{{ $recipe->difficulty_level ?: 'Non défini' }}</p>

                                <p class="mt-4 text-sm text-gray-500">Quantité produite</p>
                                <p class="font-medium">{{ $recipe->yield_quantity ?: 'Non défini' }}</p>

                                <p class="mt-4 text-sm text-gray-500">Statut</p>
                                <p class="font-medium">
                                    @if ($recipe->active)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactif
                                        </span>
                                    @endif
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Temps de préparation</p>
                                <p class="font-medium">{{ $recipe->preparation_time ? $recipe->preparation_time . ' min' : 'Non défini' }}</p>

                                <p class="mt-4 text-sm text-gray-500">Temps de cuisson</p>
                                <p class="font-medium">{{ $recipe->cooking_time ? $recipe->cooking_time . ' min' : 'Non défini' }}</p>

                                <p class="mt-4 text-sm text-gray-500">Temps de repos</p>
                                <p class="font-medium">{{ $recipe->rest_time ? $recipe->rest_time . ' min' : 'Non défini' }}</p>

                                <p class="mt-4 text-sm text-gray-500">Temps total</p>
                                <p class="font-medium">{{ $recipe->total_time > 0 ? $recipe->total_time . ' min' : 'Non défini' }}</p>
                            </div>
                        </div>

                        @if ($recipe->description)
                            <div class="mt-6">
                                <p class="text-sm text-gray-500">Description</p>
                                <p class="mt-1">{{ $recipe->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Ingrédients -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Ingrédients</h3>

                        @if ($recipe->ingredients->isEmpty())
                            <p class="text-gray-500">Aucun ingrédient n'a été ajouté à cette recette.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingrédient</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($recipe->ingredients as $recipeIngredient)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $recipeIngredient->ingredient->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $recipeIngredient->quantity }} {{ $recipeIngredient->unit ?: $recipeIngredient->ingredient->unit }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {{ $recipeIngredient->notes }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Étapes -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">Étapes de préparation</h3>

                        @if ($recipe->steps->isEmpty())
                            <p class="text-gray-500">Aucune étape n'a été ajoutée à cette recette.</p>
                        @else
                            <div class="space-y-6">
                                @foreach ($recipe->steps as $step)
                                    <div class="p-4 border border-gray-200 rounded-md">
                                        <h4 class="font-medium text-blue-700">Étape {{ $step->step_number }}</h4>

                                        <div class="mt-2">
                                            <p>{{ $step->instruction }}</p>
                                        </div>

                                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @if ($step->tips)
                                                <div>
                                                    <p class="text-sm text-gray-500">Astuces:</p>
                                                    <p class="text-sm text-gray-700">{{ $step->tips }}</p>
                                                </div>
                                            @endif

                                            @if ($step->time_required)
                                                <div>
                                                    <p class="text-sm text-gray-500">Temps nécessaire:</p>
                                                    <p class="text-sm text-gray-700">{{ $step->time_required }} minutes</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
