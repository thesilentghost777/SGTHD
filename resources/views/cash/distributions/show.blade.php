@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détail de la Distribution') }} - {{ $distribution->date->format('d/m/Y') }}
            </h2>
            <div class="mt-3 md:mt-0 flex space-x-2">
                @if($distribution->status === 'en_cours')
                    <a href="{{ route('cash.distributions.edit', $distribution) }}" class="inline-flex items-center px-4 py-2 bg-appgreen-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-appgreen-700 focus:bg-appgreen-700 active:bg-appgreen-800 focus:outline-none focus:ring-2 focus:ring-appgreen-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Modifier
                    </a>
                    <a href="{{ route('cash.distributions.close.form', $distribution) }}" class="inline-flex items-center px-4 py-2 bg-appblue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-appblue-700 focus:bg-appblue-700 active:bg-appblue-800 focus:outline-none focus:ring-2 focus:ring-appblue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10H12V2Z"/><path d="M12 2a10 10 0 0 1 10 10"/><path d="M12 12h10"/></svg>
                        Clôturer
                    </a>
                @endif
                <a href="{{ route('cash.distributions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="bg-gradient-to-r from-appblue-100 to-appgreen-100 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-appblue-800 text-sm">Vendeuse</p>
                                <p class="text-lg font-bold text-appblue-900">{{ $distribution->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-appblue-800 text-sm">Date</p>
                                <p class="text-lg font-bold text-appblue-900">{{ $distribution->date->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-appblue-800 text-sm">Statut</p>
                                @if($distribution->status === 'en_cours')
                                    <p class="text-lg font-bold text-yellow-600">En cours</p>
                                @else
                                    <p class="text-lg font-bold text-green-600">Clôturé</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Informations principales -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations Principales</h3>

                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-500 text-sm">Montant obtenu pour les ventes</p>
                                        <p class="text-xl font-bold text-appblue-800">{{ number_format($distribution->bill_amount, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm">Monnaie Initiale</p>
                                        <p class="text-xl font-bold text-appblue-800">{{ number_format($distribution->initial_coin_amount, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-gray-500 text-sm">Montant des Ventes
                                        <form action="{{ route('cash.distributions.update-sales', $distribution) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-appblue-600 hover:text-appblue-800 ml-2" title="Actualiser le montant des ventes">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/></svg>
                                            </button>
                                        </form>
                                    </p>
                                    <p class="text-xl font-bold text-appgreen-800">{{ number_format($distribution->sales_amount, 0, ',', ' ') }} FCFA</p>
                                </div>

                                @if($distribution->status === 'cloture')
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-500 text-sm">Monnaie Finale</p>
                                        <p class="text-xl font-bold text-appblue-800">{{ number_format($distribution->final_coin_amount, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm">Montant Versé</p>
                                        <p class="text-xl font-bold text-appgreen-800">{{ number_format($distribution->deposited_amount, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-gray-500 text-sm">Montant Manquant
                                        <form action="{{ route('cash.distributions.update-missing', $distribution) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-appblue-600 hover:text-appblue-800 ml-2" title="Recalculer le manquant">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/></svg>
                                            </button>
                                        </form>
                                    </p>
                                    @if($distribution->missing_amount > 0)
                                        <p class="text-xl font-bold text-red-600">{{ number_format($distribution->missing_amount, 0, ',', ' ') }} FCFA</p>
                                    @else
                                        <p class="text-xl font-bold text-green-600">0 FCFA</p>
                                    @endif
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <p class="text-gray-500 text-sm">Clôturé par</p>
                                    <p class="text-gray-800">{{ $distribution->closedByUser->name ?? 'N/A' }} le {{ $distribution->closed_at ? $distribution->closed_at->format('d/m/Y à H:i') : 'N/A' }}</p>
                                </div>
                                @endif

                                @if($distribution->notes)
                                <div class="border-t border-gray-200 pt-4">
                                    <p class="text-gray-500 text-sm">Notes</p>
                                    <p class="text-gray-800">{{ $distribution->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Calcul du manquant -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Calcul du Manquant</h3>

                            @if($distribution->status === 'cloture')
                                <div class="space-y-6">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-gray-500 text-sm font-medium mb-2">Formule appliquée</p>
                                        <p class="text-gray-900">
                                            (Ventes Produits + Montant obtenu pour les ventes + (Monnaie initiale - Monnaie finale)) - Versement = Manquant
                                        </p>
                                    </div>

                                    <div class="bg-appblue-50 p-4 rounded-lg space-y-2">
                                        <div class="flex justify-between items-center">
                                            <p class="text-appblue-800">Montant des ventes</p>
                                            <p class="text-appblue-900 font-medium">{{ number_format($distribution->sales_amount, 0, ',', ' ') }} FCFA</p>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <p class="text-appblue-800">Montant obtenu pour les ventes</p>
                                            <p class="text-appblue-900 font-medium">{{ number_format($distribution->bill_amount, 0, ',', ' ') }} FCFA</p>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <p class="text-appblue-800">Monnaie initiale</p>
                                            <p class="text-appblue-900 font-medium">{{ number_format($distribution->initial_coin_amount, 0, ',', ' ') }} FCFA</p>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <p class="text-appblue-800">Monnaie finale</p>
                                            <p class="text-appblue-900 font-medium">{{ number_format($distribution->final_coin_amount, 0, ',', ' ') }} FCFA</p>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <p class="text-appblue-800">Différence monnaie</p>
                                            <p class="text-appblue-900 font-medium">{{ number_format($distribution->initial_coin_amount - $distribution->final_coin_amount, 0, ',', ' ') }} FCFA</p>
                                        </div>
                                        <div class="border-t border-appblue-200 pt-2">
                                            <div class="flex justify-between items-center">
                                                <p class="text-appblue-800 font-medium">Montant total attendu</p>
                                                <p class="text-appblue-900 font-bold">
                                                    {{ number_format($distribution->sales_amount+ $distribution->bill_amount + ($distribution->initial_coin_amount - $distribution->final_coin_amount), 0, ',', ' ') }} FCFA
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center px-4">
                                        <p class="text-gray-800 font-medium">Montant versé</p>
                                        <p class="text-gray-900 font-bold">{{ number_format($distribution->deposited_amount, 0, ',', ' ') }} FCFA</p>
                                    </div>

                                    <div class="border-t border-gray-200 pt-4 px-4">
                                        <div class="flex justify-between items-center">
                                            <p class="text-lg text-gray-800 font-medium">Montant manquant</p>
                                            @if($distribution->missing_amount > 0)
                                                <p class="text-xl text-red-600 font-bold">{{ number_format($distribution->missing_amount, 0, ',', ' ') }} FCFA</p>
                                            @else
                                                <p class="text-xl text-green-600 font-bold">0 FCFA</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center h-60 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-appblue-300 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    <p class="text-gray-500 mb-2">Le calcul du manquant sera disponible après la clôture de la distribution</p>
                                    <a href="{{ route('cash.distributions.close.form', $distribution) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-appblue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-appblue-700 focus:bg-appblue-700 active:bg-appblue-800 focus:outline-none focus:ring-2 focus:ring-appblue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10H12V2Z"/><path d="M12 2a10 10 0 0 1 10 10"/><path d="M12 12h10"/></svg>
                                        Clôturer maintenant
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Détail des ventes -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow overflow-hidden">
                        <div class="p-6 pb-0">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Détail des Ventes</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Unitaire</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($sales as $sale)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $sale->produit_nom ?? 'Produit inconnu' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $sale->quantite }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($sale->prix, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($sale->quantite * $sale->prix, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $sale->type }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                Aucune vente trouvée pour cette date.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                                @if(count($sales) > 0)
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                            Total
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-appblue-800">
                                            {{ number_format($distribution->sales_amount, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
