@extends('layouts.app')

@section('content')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier la Distribution') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('cash.distributions.update', $distribution) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Vendeuse</label>
                            <div class="text-lg font-medium text-gray-900">{{ $distribution->user->name }}</div>
                            <input type="hidden" name="user_id" value="{{ $distribution->user_id }}">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <div class="text-lg font-medium text-gray-900">{{ $distribution->date->format('d/m/Y') }}</div>
                            <input type="hidden" name="date" value="{{ $distribution->date->format('Y-m-d') }}">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="bill_amount" class="block text-sm font-medium text-gray-700 mb-1">Montant en Billets</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" name="bill_amount" id="bill_amount" min="0" step="1" value="{{ old('bill_amount', $distribution->bill_amount) }}" required
                                          class="focus:ring-appblue-500 focus:border-appblue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">FCFA</span>
                                    </div>
                                </div>
                                @error('bill_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="initial_coin_amount" class="block text-sm font-medium text-gray-700 mb-1">Montant en Monnaie</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" name="initial_coin_amount" id="initial_coin_amount" min="0" step="1" value="{{ old('initial_coin_amount', $distribution->initial_coin_amount) }}" required
                                          class="focus:ring-appblue-500 focus:border-appblue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">FCFA</span>
                                    </div>
                                </div>
                                @error('initial_coin_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-appblue-500 focus:border-appblue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('notes', $distribution->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('cash.distributions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                                Annuler
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-appblue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-appblue-700 focus:bg-appblue-700 active:bg-appblue-800 focus:outline-none focus:ring-2 focus:ring-appblue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
