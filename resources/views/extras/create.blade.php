{{-- resources/views/extras/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 py-8 px-4">
    <div class="container mx-auto max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-green-600 px-8 py-6">
                <h1 class="text-3xl font-bold text-white">Nouvelle Réglementation</h1>
                <p class="text-blue-50 mt-2">Veuillez remplir les informations ci-dessous</p>
            </div>

            <!-- Form -->
            <form action="{{ route('extras.store') }}" method="POST" class="p-8 space-y-8">
                @csrf

                <!-- Secteur -->
<div class="space-y-3">
    <label for="secteur" class="text-base font-semibold text-gray-700 flex items-center gap-2">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        Secteur d'activité
    </label>
    <div class="relative">
        <select
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 appearance-none bg-white @error('secteur') border-red-300 @enderror"
            id="secteur"
            name="secteur"
            required>
            <option value="">Sélectionnez un secteur</option>
            <option value="administration" {{ old('secteur', $extra->secteur ?? '') == 'administration' ? 'selected' : '' }}>Administration</option>
            <option value="alimentation" {{ old('secteur', $extra->secteur ?? '') == 'alimentation' ? 'selected' : '' }}>Alimentation</option>
            <option value="glace" {{ old('secteur', $extra->secteur ?? '') == 'glace' ? 'selected' : '' }}>Glace</option>
            <option value="production" {{ old('secteur', $extra->secteur ?? '') == 'production' ? 'selected' : '' }}>Production</option>
        </select>
        <div class="pointer-events-none absolute right-3 top-3.5">
            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>
    @error('secteur')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

                <!-- Horaires Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Heure d'arrivée -->
                    <div class="space-y-3">
                        <label for="heure_arriver_adequat" class="text-base font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Heure d'arrivée
                        </label>
                        <input type="time"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 @error('heure_arriver_adequat') border-red-300 @enderror"
                            id="heure_arriver_adequat"
                            name="heure_arriver_adequat"
                            value="{{ old('heure_arriver_adequat', isset($extra) ? $extra->heure_arriver_adequat->format('H:i') : '') }}"
                            required>
                        @error('heure_arriver_adequat')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Heure de départ -->
                    <div class="space-y-3">
                        <label for="heure_depart_adequat" class="text-base font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Heure de départ
                        </label>
                        <input type="time"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 @error('heure_depart_adequat') border-red-300 @enderror"
                            id="heure_depart_adequat"
                            name="heure_depart_adequat"
                            value="{{ old('heure_depart_adequat', isset($extra) ? $extra->heure_depart_adequat->format('H:i') : '') }}"
                            required>
                        @error('heure_depart_adequat')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Salaire -->
                <div class="space-y-3">
                    <label for="salaire_adequat" class="text-base font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Salaire mensuel standard
                    </label>
                    <div class="relative">
                        <input type="number"
                            step="0.01"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 @error('salaire_adequat') border-red-300 @enderror"
                            id="salaire_adequat"
                            name="salaire_adequat"
                            value="{{ old('salaire_adequat', $extra->salaire_adequat ?? '') }}"
                            placeholder="0.00"
                            required>
                        <span class="absolute right-4 top-3 text-gray-500 font-medium">XAF</span>
                    </div>
                    @error('salaire_adequat')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Règles et Interdits Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Interdits -->
                    <div class="space-y-3">
                        <label for="interdit" class="text-base font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            Interdictions
                        </label>
                        <textarea
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 @error('interdit') border-red-300 @enderror"
                            id="interdit"
                            name="interdit"
                            rows="4"
                            placeholder="Ex: téléphone portable, nourriture...">{{ old('interdit', $extra->interdit ?? '') }}</textarea>
                        <p class="text-sm text-gray-500">Séparez les interdictions par des virgules</p>
                        @error('interdit')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Règles -->
                    <div class="space-y-3">
                        <label for="regles" class="text-base font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Règles à suivre
                        </label>
                        <textarea
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 @error('regles') border-red-300 @enderror"
                            id="regles"
                            name="regles"
                            rows="4"
                            placeholder="Ex: ponctualité, tenue correcte...">{{ old('regles', $extra->regles ?? '') }}</textarea>
                        <p class="text-sm text-gray-500">Séparez les règles par des virgules</p>
                        @error('regles')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Âge minimum -->
                <div class="space-y-3">
                    <label for="age_adequat" class="text-base font-semibold text-gray-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Âge minimum requis
                    </label>
                    <input type="number"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-200 @error('age_adequat') border-red-300 @enderror"
                        id="age_adequat"
                        name="age_adequat"
                        value="{{ old('age_adequat', $extra->age_adequat ?? '') }}"
                        min="16"
                        max="70"
                        required>
                    @error('age_adequat')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-green-600 text-white font-semibold rounded-xl
                               shadow-lg hover:from-blue-700 hover:to-green-700 transition-all duration-200 transform hover:-translate-y-1">
                        Créer la réglementation
                    </button>
                    <a href="{{ route('extras.index') }}"
                        class="px-8 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200
                               transition-all duration-200 text-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
