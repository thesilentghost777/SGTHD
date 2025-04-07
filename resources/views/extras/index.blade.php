@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <!-- Header Section -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-blue-800 mb-4 tracking-tight">
                Liste des Réglementations
            </h1>
            <a href="{{ route('extras.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg
                      shadow-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nouvelle Réglementation
            </a>
        </div>

        <!-- Alert Message -->
        @if(session('success'))
        <div class="max-w-4xl mx-auto mb-8">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r shadow-md animate-fade-in"
                 role="alert">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
        @endif

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
                                    <a href="{{ route('extras.edit', $extra) }}"
                                       class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-md
                                              hover:bg-yellow-200 transition-colors duration-200">
                                        Modifier
                                    </a>
                                    <form action="{{ route('extras.destroy', $extra) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md
                                                       hover:bg-red-200 transition-colors duration-200"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réglementation ?')">
                                            Supprimer
                                        </button>
                                    </form>
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
