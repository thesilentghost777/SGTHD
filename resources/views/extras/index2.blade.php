@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <!-- Header Section -->
        <div class="text-center mb-10">
            <div class="bg-blue-100 border-l-4 border-blue-600 p-4 rounded-r-lg my-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-blue-900 font-medium italic">
                        "Parce que nul n'est censé ignorer la loi"
                    </p>
                </div>
            </div>
            <h1 class="text-4xl font-bold text-blue-800 mb-4 tracking-tight">
                Liste des Réglementations
            </h1>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-blue-700">
                            <th class="px-6 py-4 text-left text-white font-semibold">Secteur</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Horaires</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Salaire</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Âge minimum</th>
                            <th class="px-6 py-4 text-left text-white font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($extras as $extra)
                        <tr class="hover:bg-blue-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-blue-800 font-medium">{{ $extra->secteur }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                    {{ $extra->heure_arriver_adequat->format('H:i') }} - {{ $extra->heure_depart_adequat->format('H:i') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-green-600 font-medium">{{ number_format($extra->salaire_adequat, 2) }} XAF</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $extra->age_adequat }} ans
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    <a href="{{ route('extras.show', $extra) }}"
                                       class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-md
                                              hover:bg-blue-200 transition-colors duration-200">
                                        Voir
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $extras->links('pagination::tailwind') }}
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.5s ease-out;
    }
</style>
@endsection
