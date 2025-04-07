@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8 bg-gradient-to-r from-blue-600 to-teal-500 rounded-xl shadow-lg">
            <div class="px-6 py-5">
                <h2 class="text-2xl font-bold text-white">
                    {{ __('Nouvelle Distribution de Monnaie') }}
                </h2>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white overflow-hidden shadow-xl rounded-xl border border-blue-100">
            <div class="p-6 sm:p-8">
                <form action="{{ route('cash.distributions.store') }}" method="POST">
                    @csrf

                    <!-- Date Field -->
                    <div class="mb-8">
                        <label for="date" class="block text-lg font-semibold text-blue-700 mb-2">Date</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="date" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                                class="pl-10 shadow-md focus:ring-blue-500 focus:border-blue-500 block w-full text-base border-blue-300 rounded-lg p-3 bg-blue-50 hover:bg-blue-100 transition-colors">
                        </div>
                        @error('date')
                            <p class="mt-2 text-sm font-medium text-white bg-red-500 p-2 rounded-lg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amounts Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Bills Amount -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-xl shadow-md border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                            <label for="bill_amount" class="block text-base font-semibold text-blue-800 mb-2">Montant en obtenu pour les ventes</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-blue-500"></span>
                                </div>
                                <input type="number" name="bill_amount" id="bill_amount" min="0" step="1" value="{{ old('bill_amount', 0) }}" required
                                    class="pl-14 focus:ring-blue-500 focus:border-blue-500 block w-full text-base border-blue-300 rounded-lg p-3 bg-white font-medium">
                            </div>
                            @error('bill_amount')
                                <p class="mt-2 text-sm font-medium text-white bg-red-500 p-2 rounded-lg">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Coins Amount -->
                        <div class="bg-gradient-to-br from-teal-50 to-teal-100 p-5 rounded-xl shadow-md border-l-4 border-teal-500 hover:shadow-lg transition-shadow">
                            <label for="initial_coin_amount" class="block text-base font-semibold text-teal-800 mb-2">Montant en Monnaie</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-teal-500"></span>
                                </div>
                                <input type="number" name="initial_coin_amount" id="initial_coin_amount" min="0" step="1" value="{{ old('initial_coin_amount', 0) }}" required
                                    class="pl-14 focus:ring-teal-500 focus:border-teal-500 block w-full text-base border-teal-300 rounded-lg p-3 bg-white font-medium">
                            </div>
                            @error('initial_coin_amount')
                                <p class="mt-2 text-sm font-medium text-white bg-red-500 p-2 rounded-lg">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="mb-8 bg-gradient-to-br from-blue-50 to-teal-50 p-5 rounded-xl shadow-md hover:shadow-lg transition-shadow">
                        <label for="notes" class="block text-base font-semibold text-blue-800 mb-2">Notes</label>
                        <div class="relative">
                            <div class="absolute top-3 left-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <textarea id="notes" name="notes" rows="4" class="pl-10 shadow-md focus:ring-blue-500 focus:border-blue-500 block w-full text-base border-blue-300 rounded-lg p-3 bg-white">{{ old('notes') }}</textarea>
                        </div>
                        @error('notes')
                            <p class="mt-2 text-sm font-medium text-white bg-red-500 p-2 rounded-lg">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-10">
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-red-600 to-red-700 rounded-xl font-bold text-base text-white uppercase tracking-wider hover:from-red-700 hover:to-red-600 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-lg transform hover:-translate-y-1">
                            Annuler
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-teal-500 rounded-xl font-bold text-base text-white uppercase tracking-wider hover:from-blue-700 hover:to-teal-600 focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-lg transform hover:-translate-y-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
