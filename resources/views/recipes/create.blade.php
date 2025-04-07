
@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une Nouvelle Recette') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('recipes.store') }}" method="POST" id="recipeForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Informations générales -->
                            <div class="col-span-1 md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Nom de la recette*</label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-gray-700">Catégorie</label>
                                        <select name="category_id" id="category_id"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <option value="">-- Sélectionner une catégorie --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea name="description" id="description" rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                    <div>
                                        <label for="preparation_time" class="block text-sm font-medium text-gray-700">Temps de préparation (min)</label>
                                        <input type="number" name="preparation_time" id="preparation_time" min="0" value="{{ old('preparation_time') }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @error('preparation_time')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="cooking_time" class="block text-sm font-medium text-gray-700">Temps de cuisson (min)</label>
                                        <input type="number" name="cooking_time" id="cooking_time" min="0" value="{{ old('cooking_time') }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @error('cooking_time')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="rest_time" class="block text-sm font-medium text-gray-700">Temps de repos (min)</label>
                                        <input type="number" name="rest_time" id="rest_time" min="0" value="{{ old('rest_time') }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @error('rest_time')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="yield_quantity" class="block text-sm font-medium text-gray-700">Quantité produite</label>
                                        <input type="number" name="yield_quantity" id="yield_quantity" min="1" value="{{ old('yield_quantity') }}"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @error('yield_quantity')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="difficulty_level" class="block text-sm font-medium text-gray-700">Niveau de difficulté</label>
                                        <select name="difficulty_level" id="difficulty_level"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <option value="">-- Sélectionner un niveau --</option>
                                            <option value="Facile" {{ old('difficulty_level') == 'Facile' ? 'selected' : '' }}>Facile</option>
                                            <option value="Moyen" {{ old('difficulty_level') == 'Moyen' ? 'selected' : '' }}>Moyen</option>
                                            <option value="Difficile" {{ old('difficulty_level') == 'Difficile' ? 'selected' : '' }}>Difficile</option>
                                        </select>
                                        @error('difficulty_level')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="active" value="1" checked
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Recette active (visible pour les employés)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Ingrédients -->
                            <div class="col-span-1 md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Ingrédients</h3>

                                <div id="ingredients-container">
                                    <div class="ingredients-entry mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Ingrédient*</label>
                                                <select name="ingredients[0][id]" required
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    <option value="">-- Sélectionner un ingrédient --</option>
                                                    @foreach($ingredients as $ingredient)
                                                        <option value="{{ $ingredient->id }}">
                                                            {{ $ingredient->name }} {{ $ingredient->unit ? '('.$ingredient->unit.')' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Quantité*</label>
                                                <input type="number" name="ingredients[0][quantity]" required min="0" step="0.01" value="0"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Unité</label>
                                                <input type="text" name="ingredients[0][unit]" placeholder="g, kg, ml..."
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            </div>

                                            <div class="flex items-end">
                                                <button type="button" class="remove-ingredient text-red-600 px-2 py-1 text-sm" style="visibility: hidden;">
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-2">
                                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                                            <input type="text" name="ingredients[0][notes]"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="add-ingredient" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Ajouter un ingrédient
                                </button>
                            </div>

                            <!-- Étapes de préparation -->
                            <div class="col-span-1 md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Étapes de préparation</h3>

                                <div id="steps-container">
                                    <div class="steps-entry mb-4 p-4 border border-gray-200 rounded-md bg-gray-50">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="font-medium text-gray-900">Étape 1</h4>
                                            <button type="button" class="remove-step text-red-600 px-2 py-1 text-sm" style="visibility: hidden;">
                                                Supprimer
                                            </button>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Instructions*</label>
                                            <textarea name="steps[0][instruction]" required rows="3"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Conseils ou astuces</label>
                                                <input type="text" name="steps[0][tips]"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Temps nécessaire (min)</label>
                                                <input type="number" name="steps[0][time_required]" min="0"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="add-step" class="mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Ajouter une étape
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('recipes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Annuler
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Créer la recette
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des ingrédients
            const ingredientsContainer = document.getElementById('ingredients-container');
            const addIngredientButton = document.getElementById('add-ingredient');
            let ingredientCounter = 0;

            addIngredientButton.addEventListener('click', function() {
                ingredientCounter++;
                const newIngredient = document.querySelector('.ingredients-entry').cloneNode(true);

                // Mettre à jour les indices
                newIngredient.querySelectorAll('select, input, textarea').forEach(function(element) {
                    const name = element.getAttribute('name');
                    if (name) {
                        element.setAttribute('name', name.replace(/\[\d+\]/, '[' + ingredientCounter + ']'));

                        // Réinitialiser les valeurs
                        if (element.tagName === 'SELECT') {
                            element.selectedIndex = 0;
                        } else if (element.tagName === 'INPUT') {
                            if (element.type === 'checkbox') {
                                element.checked = false;
                            } else if (element.type === 'number') {
                                element.value = element.min || '0';
                            } else {
                                element.value = '';
                            }
                        } else if (element.tagName === 'TEXTAREA') {
                            element.value = '';
                        }
                    }
                });

                // Afficher le bouton de suppression
                const removeButton = newIngredient.querySelector('.remove-ingredient');
                removeButton.style.visibility = 'visible';
                removeButton.addEventListener('click', function() {
                    newIngredient.remove();
                });

                ingredientsContainer.appendChild(newIngredient);
            });

            // Gestion des étapes
            const stepsContainer = document.getElementById('steps-container');
            const addStepButton = document.getElementById('add-step');
            let stepCounter = 0;

            addStepButton.addEventListener('click', function() {
                stepCounter++;
                const newStep = document.querySelector('.steps-entry').cloneNode(true);

                // Mettre à jour les indices et l'étiquette de l'étape
                newStep.querySelector('h4').textContent = 'Étape ' + (stepCounter + 1);

                newStep.querySelectorAll('input, textarea').forEach(function(element) {
                    const name = element.getAttribute('name');
                    if (name) {
                        element.setAttribute('name', name.replace(/\[\d+\]/, '[' + stepCounter + ']'));
                        element.value = '';
                    }
                });

                // Afficher le bouton de suppression
                const removeButton = newStep.querySelector('.remove-step');
                removeButton.style.visibility = 'visible';
                removeButton.addEventListener('click', function() {
                    newStep.remove();

                    // Mettre à jour les numéros d'étape
                    document.querySelectorAll('.steps-entry').forEach(function(step, index) {
                        step.querySelector('h4').textContent = 'Étape ' + (index + 1);
                    });
                });

                stepsContainer.appendChild(newStep);
            });
        });
    </script>
@endsection

