@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8 bg-blue-600 rounded-xl shadow-lg">
            <div class="px-6 py-5">
                <h2 class="text-2xl font-bold text-white">
                    {{ __('Clôturer la Distribution') }}
                </h2>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white overflow-hidden shadow-xl rounded-xl border border-gray-200">
            <div class="p-6 sm:p-8">
                <!-- Information Alert -->
                <div class="mb-8 bg-blue-50 p-5 rounded-xl shadow-md border-l-4 border-blue-500">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 pt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-blue-800">Informations importantes</h3>
                            <div class="mt-2 text-base text-blue-700">
                                <p>Vous êtes sur le point de clôturer la distribution de monnaie pour <span class="font-semibold">{{ $distribution->user->name }}</span> du <span class="font-semibold">{{ $distribution->date->format('d/m/Y') }}</span>.</p>
                                <p class="mt-2 font-medium">Cette action est irréversible. Une fois clôturée, la distribution ne pourra plus être modifiée.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribution Details -->
                <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-gray-50 rounded-xl shadow-md">
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-400">
                        <p class="text-sm font-medium text-blue-600 mb-1">Vendeuse</p>
                        <p class="font-bold text-gray-900 text-lg">{{ $distribution->user->name }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-blue-400">
                        <p class="text-sm font-medium text-blue-600 mb-1">Date</p>
                        <p class="font-bold text-gray-900 text-lg">{{ $distribution->date->format('d/m/Y') }}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-teal-400">
                        <p class="text-sm font-medium text-teal-600 mb-1">Montant des ventes</p>
                        <p class="font-bold text-gray-900 text-lg">{{ number_format($distribution->sales_amount, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-teal-400">
                        <p class="text-sm font-medium text-teal-600 mb-1">Montant obtenu pour la vente</p>
                        <p class="font-bold text-gray-900 text-lg">{{ number_format($distribution->bill_amount, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-teal-400">
                        <p class="text-sm font-medium text-teal-600 mb-1">Monnaie initiale</p>
                        <p class="font-bold text-gray-900 text-lg">{{ number_format($distribution->initial_coin_amount, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>

                <form action="{{ route('cash.distributions.close', $distribution) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Final Coin Amount -->
                        <div class="bg-blue-50 p-5 rounded-xl shadow-md border-l-4 border-blue-500">
                            <label for="final_coin_amount" class="block text-base font-semibold text-blue-800 mb-2">Monnaie Finale</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-blue-500">FCFA</span>
                                </div>
                                <input type="number" name="final_coin_amount" id="final_coin_amount" min="0" step="1" value="{{ old('final_coin_amount', 0) }}" required
                                    class="pl-14 focus:ring-blue-500 focus:border-blue-500 block w-full text-base border-blue-300 rounded-lg p-3 bg-white font-medium">
                            </div>
                            <p class="mt-2 text-sm text-blue-700">Montant de monnaie restant à la fin de la journée.</p>
                            @error('final_coin_amount')
                                <p class="mt-2 text-sm font-medium text-white bg-red-500 p-2 rounded-lg">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deposited Amount -->
                        <div class="bg-teal-50 p-5 rounded-xl shadow-md border-l-4 border-teal-500">
                            <label for="deposited_amount" class="block text-base font-semibold text-teal-800 mb-2">Montant Versé</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-teal-500">FCFA</span>
                                </div>
                                <input type="number" name="deposited_amount" id="deposited_amount" min="0" step="1" value="{{ old('deposited_amount', 0) }}" required
                                    class="pl-14 focus:ring-teal-500 focus:border-teal-500 block w-full text-base border-teal-300 rounded-lg p-3 bg-white font-medium">
                            </div>
                            <p class="mt-2 text-sm text-teal-700">Montant total versé par la vendeuse.</p>
                            @error('deposited_amount')
                                <p class="mt-2 text-sm font-medium text-white bg-red-500 p-2 rounded-lg">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="bg-yellow-50 p-5 rounded-xl border-l-4 border-yellow-500 shadow-md mb-8">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-yellow-800">Calcul automatique du manquant</h3>
                                <div class="mt-2 text-base text-yellow-700">
                                    <p>Le système calculera automatiquement le montant manquant selon la formule :</p>
                                    <p class="font-mono mt-2 p-2 bg-yellow-100 rounded-lg text-yellow-800">(Ventes + Billets + (Monnaie initiale - Monnaie finale)) - Versement = Manquant</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-10">
                        <a href="{{ route('cash.distributions.show', $distribution) }}" class="inline-flex items-center justify-center px-8 py-4 bg-gray-600 rounded-xl font-bold text-base text-white uppercase tracking-wider hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-300 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-4 bg-blue-600 rounded-xl font-bold text-base text-white uppercase tracking-wider hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10H12V2Z"/><path d="M12 2a10 10 0 0 1 10 10"/><path d="M12 12h10"/></svg>
                            Clôturer la distribution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
