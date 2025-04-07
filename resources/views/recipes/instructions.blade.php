@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Instructions de Production') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-6">Sélectionnez une recette pour voir les instructions détaillées</h3>

                @if ($recipes->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500">Aucune recette active n'est disponible pour le moment.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($recipes as $recipe)
                            <a href="{{ route('recipes.show_instructions', $recipe) }}" class="block group">
                                <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition duration-200">
                                    <div class="bg-gradient-to-r from-blue-100 to-green-50 p-4">
                                        <h3 class="text-lg font-medium text-gray-900 group-hover:text-blue-700 transition">{{ $recipe->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $recipe->category ? $recipe->category->name : 'Sans catégorie' }}</p>
                                    </div>

                                    <div class="p-4">
                                        <div class="flex items-center text-sm mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>{{ $recipe->total_time }} minutes au total</span>
                                        </div>

                                        <div class="flex items-center text-sm mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span>{{ $recipe->steps->count() }} étapes</span>
                                        </div>

                                        <div class="flex items-center text-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                            </svg>
                                            <span>{{ $recipe->difficulty_level ?: 'Difficulté non spécifiée' }}</span>
                                        </div>

                                        <div class="mt-4 pt-4 border-t border-gray-100 text-right">
                                            <span class="inline-flex items-center text-sm font-medium text-blue-600 group-hover:text-blue-800 transition">
                                                Voir les instructions
                                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
