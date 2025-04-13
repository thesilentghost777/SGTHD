@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $recipe->name }} - Instructions de Production
            </h2>
            <a href="{{ route('recipes.instructions') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition">
                {{ __('Retour à la liste des recettes') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informations générales -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <h3 class="text-xl font-medium text-gray-900 mb-4">À propos de cette recette</h3>

                            @if ($recipe->description)
                                <p class="text-gray-700 mb-4">{{ $recipe->description }}</p>
                            @endif

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                                <div class="bg-blue-50 rounded-lg p-4 flex flex-col items-center">
                                    <span class="text-blue-700 font-medium">Préparation</span>
                                    <span class="text-xl font-bold mt-2">{{ $recipe->preparation_time ?? 0 }} min</span>
                                </div>

                                <div class="bg-green-50 rounded-lg p-4 flex flex-col items-center">
                                    <span class="text-green-700 font-medium">Cuisson</span>
                                    <span class="text-xl font-bold mt-2">{{ $recipe->cooking_time ?? 0 }} min</span>
                                </div>

                                <div class="bg-indigo-50 rounded-lg p-4 flex flex-col items-center">
                                    <span class="text-indigo-700 font-medium">Repos</span>
                                    <span class="text-xl font-bold mt-2">{{ $recipe->rest_time ?? 0 }} min</span>
                                </div>

                                <div class="bg-purple-50 rounded-lg p-4 flex flex-col items-center">
                                    <span class="text-purple-700 font-medium">Total</span>
                                    <span class="text-xl font-bold mt-2">{{ $recipe->total_time }} min</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations</h3>

                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                    </svg>
                                    <div>
                                        <span class="text-gray-600 text-sm">Catégorie:</span>
                                        <span class="ml-1 font-medium">{{ $recipe->category ? $recipe->category->name : 'Non catégorisé' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <div>
                                        <span class="text-gray-600 text-sm">Difficulté:</span>
                                        <span class="ml-1 font-medium">{{ $recipe->difficulty_level ?: 'Non définie' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <div>
                                        <span class="text-gray-600 text-sm">Quantité produite:</span>
                                        <span class="ml-1 font-medium">{{ $recipe->yield_quantity ?: 'Non définie' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingrédients -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-medium text-gray-900 mb-4">Ingrédients nécessaires</h3>

                    @if ($recipe->ingredients->isEmpty())
                        <p class="text-gray-500">Aucun ingrédient n'a été ajouté à cette recette.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingrédient</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructions spéciales</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($recipe->ingredients as $recipeIngredient)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $recipeIngredient->ingredient->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="font-medium">{{ $recipeIngredient->quantity }}</span> {{ $recipeIngredient->unit ?: $recipeIngredient->ingredient->unit }}
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
            </div>

            <!-- Étapes -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-medium text-gray-900 mb-6">Étapes de préparation</h3>

                    @if ($recipe->steps->isEmpty())
                        <p class="text-gray-500">Aucune étape n'a été ajoutée à cette recette.</p>
                    @else
                        <div class="space-y-8">
                            @foreach ($recipe->steps as $step)
                                <div class="relative pl-8 pb-8 border-l-2 border-blue-200">
                                    <!-- Numéro d'étape -->
                                    <div class="absolute -left-4 top-0 flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white font-bold">
                                        {{ $step->step_number }}
                                    </div>

                                    <div class="ml-6">
                                        <div class="p-4 bg-gradient-to-r from-blue-50 to-white rounded-lg border border-blue-100">
                                            <!-- Instruction principale -->
                                            <p class="text-gray-800">{{ $step->instruction }}</p>

                                            <!-- Informations supplémentaires -->
                                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @if ($step->time_required)
                                                    <div class="flex items-center bg-blue-50 p-2 rounded">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="text-sm">
                                                            <span class="text-gray-600">Temps:</span>
                                                            <span class="font-medium text-blue-800">{{ $step->time_required }} minutes</span>
                                                        </span>
                                                    </div>
                                                @endif

                                                @if ($step->tips)
                                                    <div class="flex items-start bg-green-50 p-2 rounded">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="text-sm">
                                                            <span class="text-gray-600">Conseil:</span>
                                                            <span class="font-medium text-green-800">{{ $step->tips }}</span>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
