@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Administration des Recettes') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Cards for quick access to recipe management functions -->

                <!-- Recipes Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-900">Gestion des Recettes</h2>
                                <p class="mt-1 text-sm text-gray-600">Gérer toutes vos recettes de production</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <div class="flex space-x-3">
                                <a href="{{ route('recipes.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                                    Voir les recettes
                                </a>
                                <a href="{{ route('recipes.create') }}" class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 rounded-md transition">
                                    Ajouter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-900">Catégories</h2>
                                <p class="mt-1 text-sm text-gray-600">Gérer les catégories de recettes</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <div class="flex space-x-3">
                                <a href="{{ route('recipe.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition">
                                    Voir les catégories
                                </a>
                                <a href="{{ route('recipe.categories.create') }}" class="inline-flex items-center px-4 py-2 border border-green-600 text-green-600 hover:bg-green-50 rounded-md transition">
                                    Ajouter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingredients Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-900">Ingrédients</h2>
                                <p class="mt-1 text-sm text-gray-600">Gérer la liste des ingrédients</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <div class="flex space-x-3">
                                <a href="{{ route('recipe.ingredients.index') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-md transition">
                                    Voir les ingrédients
                                </a>
                                <a href="{{ route('recipe.ingredients.create') }}" class="inline-flex items-center px-4 py-2 border border-yellow-600 text-yellow-600 hover:bg-yellow-50 rounded-md transition">
                                    Ajouter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Recipes Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg md:col-span-2">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recettes Récentes</h2>
                        @php
                            $recentRecipes = \App\Models\Recipe::with('category')->orderBy('created_at', 'desc')->take(5)->get();
                        @endphp

                        @if($recentRecipes->isEmpty())
                            <p class="text-gray-500">Aucune recette n'a été créée récemment.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulté</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de création</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentRecipes as $recipe)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $recipe->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $recipe->category ? $recipe->category->name : 'Non catégorisé' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $recipe->difficulty_level ?: 'Non défini' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $recipe->created_at->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('recipes.show', $recipe) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                        Voir
                                                    </a>
                                                    <a href="{{ route('recipes.edit', $recipe) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Modifier
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h2>
                        @php
                            $totalRecipes = \App\Models\Recipe::count();
                            $totalActiveRecipes = \App\Models\Recipe::where('active', true)->count();
                            $totalCategories = \App\Models\RecipeCategory::count();
                            $totalIngredients = \App\Models\Ingredient::count();
                        @endphp

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <p class="text-sm text-blue-600">Recettes</p>
                                <p class="text-2xl font-bold text-blue-700">{{ $totalRecipes }}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <p class="text-sm text-green-600">Recettes Actives</p>
                                <p class="text-2xl font-bold text-green-700">{{ $totalActiveRecipes }}</p>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <p class="text-sm text-yellow-600">Catégories</p>
                                <p class="text-2xl font-bold text-yellow-700">{{ $totalCategories }}</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <p class="text-sm text-purple-600">Ingrédients</p>
                                <p class="text-2xl font-bold text-purple-700">{{ $totalIngredients }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
