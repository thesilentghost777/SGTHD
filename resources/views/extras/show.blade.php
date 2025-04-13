{{-- resources/views/extras/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 py-8 px-4">
    <div class="container mx-auto max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-green-600 px-8 py-6">
                <h1 class="text-3xl font-bold text-white">Détails de l'Extra</h1>
                <p class="text-blue-50 mt-2">{{ $extra->secteur }}</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Horaires et Durée -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="bg-blue-50 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-700">Horaires de travail</h2>
                        </div>
                        <div class="space-y-3">
                            <p class="text-gray-600">
                                <span class="font-medium">Début :</span> {{ $extra->heure_arriver_adequat->format('H:i') }}
                            </p>
                            <p class="text-gray-600">
                                <span class="font-medium">Fin :</span> {{ $extra->heure_depart_adequat->format('H:i') }}
                            </p>
                            <p class="text-gray-600">
                                <span class="font-medium">Durée totale :</span> {{ number_format($extra->duree_travail, 2) }} heures
                            </p>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-xl p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-700">Rémunération</h2>
                        </div>
                        <div class="space-y-3">
                            <p class="text-gray-600">
                                <span class="font-medium">Salaire total :</span> {{ number_format($extra->salaire_adequat, 2) }} XAF
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Conditions -->
                <div class="space-y-8">
                    <!-- Âge minimum -->
                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex items-center gap-3 mb-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Âge minimum requis</h3>
                        </div>
                        <p class="text-gray-600 ml-8">{{ $extra->age_adequat }} ans</p>
                    </div>

                    <!-- Interdits -->
                    @if($extra->interdit)
                    <div class="border-l-4 border-red-500 pl-4">
                        <div class="flex items-center gap-3 mb-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Interdictions</h3>
                        </div>
                        <ul class="list-disc ml-12 text-gray-600 space-y-1">
                            @foreach($extra->interditsArray as $interdit)
                            <li>{{ $interdit }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Règles -->
                    @if($extra->regles)
                    <div class="border-l-4 border-green-500 pl-4">
                        <div class="flex items-center gap-3 mb-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700">Règles à suivre</h3>
                        </div>
                        <ul class="list-disc ml-12 text-gray-600 space-y-1">
                            @foreach($extra->reglesArray as $regle)
                            <li>{{ $regle }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex gap-4 mt-8">
                    <a href="{{ route('extras.index2') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200
                               transition-all duration-200 inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Retourner
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
