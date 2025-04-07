@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de la page -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Validation du retrait
            </h1>
            <div class="h-1 w-32 bg-blue-600 rounded-full"></div>
        </div>

        <!-- Contenu principal -->
        <div class="bg-white rounded-xl shadow-sm p-8 max-w-2xl mx-auto">
            @if(!$as)
                <div class="flex flex-col items-center justify-center py-12">
                    <div class="bg-gray-50 rounded-full p-6 mb-4">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xl text-gray-600 text-center font-medium">
                        Aucune avance à retirer.
                    </p>
                </div>
            @else
                <div class="space-y-8">
                    <!-- Affichage du montant -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700 font-medium">Montant à retirer:</span>
                            <span class="text-2xl font-bold text-blue-600">
                                {{ number_format($as->sommeAs, 0, ',', ' ') }} XAF
                            </span>
                        </div>
                    </div>

                    <!-- Formulaire ou message de statut -->
                    @if(!$as->retrait_demande)
                        <form action="{{ route('recup-retrait') }}" method="POST" class="mt-6">
                            @csrf
                            <input type="hidden" name="as_id" value="{{ $as->id }}">
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Confirmer la demande de retrait
                            </button>
                        </form>
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-green-700 font-medium">
                                    Demande de retrait envoyée. En attente de validation par le CP.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
