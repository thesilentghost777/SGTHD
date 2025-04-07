@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-teal-50 to-green-50 p-6 antialiased">
    <div class="container mx-auto">
        <header class="mb-12">
            <div class="bg-white shadow-xl rounded-2xl p-6 border-b-4 border-blue-500 transform transition-all hover:scale-[1.01]">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-teal-400">
                            Registre Détaillé des Absences
                        </h1>
                        <p class="text-gray-500 mt-2 text-lg">Analyse comprehensive des présences et absences</p>
                    </div>
                    <div class="flex space-x-3">
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-9.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Exporter
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($absenceParEmploye as $employeId => $absence)
                <div class="bg-white rounded-2xl shadow-2xl border-l-4 border-blue-500 p-6 transform transition-all hover:scale-105 hover:shadow-3xl">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-blue-800">{{ $absence['name'] }}</h3>
                            <span class="text-sm text-gray-500 bg-blue-50 px-2 py-1 rounded-full">
                                {{ $absence['secteur'] }}
                            </span>
                        </div>
                        <div class="bg-red-100 text-red-600 px-3 py-1 rounded-full font-semibold">
                            {{ $absence['nombre_absences'] }} absences
                        </div>
                    </div>

                    @if($absence['raison_conges'])
                        <div class="bg-teal-50 border-l-4 border-teal-500 p-3 mb-4 rounded-r-lg">
                            <p class="text-teal-700 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                Congés: {{ ucfirst($absence['raison_conges']) }}
                            </p>
                        </div>
                    @endif

                    <div class="max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-blue-300 scrollbar-track-blue-100 pr-2">
                        <h4 class="text-lg font-semibold text-gray-700 mb-3 border-b pb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            Jours d'Absence
                        </h4>
                        <ul class="space-y-2">
                            @foreach($absence['jours_absences'] as $jour)
                                <li class="flex items-center text-gray-600 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($jour)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl shadow-xl p-12 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-blue-400 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <h2 class="text-3xl font-bold text-blue-600 mb-4">Aucune Absence Détectée</h2>
                    <p class="text-gray-500">Félicitations ! Aucune Absence pour l'instant</p>
                </div>
            @endforelse
        </div>
    </div>
</body>
@endsection
