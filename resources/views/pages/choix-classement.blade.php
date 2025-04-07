@extends('layouts.app')

@section('content')
<div class="min-h-full" x-data="{ selected: null }">
    <main class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">
                Sélectionnez un classement
            </h1>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mt-8">
                <!-- Card Serveuse -->
                <div @click="selected = 'serveuse'"
                     :class="{'ring-4 ring-blue-500 transform scale-105': selected === 'serveuse'}"
                     class="relative bg-white rounded-lg shadow-lg overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-xl">
                    <div class="px-4 py-5 sm:p-6 bg-gradient-to-br from-blue-500 to-blue-600">
                        <div class="text-center">
                            <div class="h-12 w-12 mx-auto bg-blue-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-white">Classement Serveuse</h3>
                            <p class="mt-2 text-sm text-blue-100">
                                Performances et évaluations des serveuses
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card Producteur -->
                <div @click="selected = 'producteur'"
                     :class="{'ring-4 ring-green-500 transform scale-105': selected === 'producteur'}"
                     class="relative bg-white rounded-lg shadow-lg overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-xl">
                    <div class="px-4 py-5 sm:p-6 bg-gradient-to-br from-green-500 to-green-600">
                        <div class="text-center">
                            <div class="h-12 w-12 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-white">Classement Producteur</h3>
                            <p class="mt-2 text-sm text-green-100">
                                Production et efficacité des producteurs
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card Employé -->
                <div @click="selected = 'employe'"
                     :class="{'ring-4 ring-blue-500 transform scale-105': selected === 'employe'}"
                     class="relative bg-white rounded-lg shadow-lg overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-xl">
                    <div class="px-4 py-5 sm:p-6 bg-gradient-to-br from-blue-400 to-blue-500">
                        <div class="text-center">
                            <div class="h-12 w-12 mx-auto bg-blue-100 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-white">Classement Employé</h3>
                            <p class="mt-2 text-sm text-blue-100">
                                Performance globale des employés
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton de validation -->
            <div class="mt-8 flex justify-center">
                <button
                    x-show="selected"
                    @click="window.location.href = '/classement/' + selected"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Voir le classement
                </button>
            </div>
        </div>
    </main>
</div>
@endsection
