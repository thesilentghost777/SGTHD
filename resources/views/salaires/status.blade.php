@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Statut de votre demande d'avance</h2>
        </div>

        <div class="p-6">
            @if(!$as)
                <div class="flex flex-col items-center justify-center py-8 space-y-4">
                    <div class="p-4 bg-blue-50 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-600 text-center">Aucune demande d'avance en cours.</p>
                    <a href="#" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Faire une demande
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    <div class="flex flex-col space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="font-medium text-gray-700">Montant demandé:</span>
                            <span class="text-lg font-semibold text-gray-900">{{ number_format($as->sommeAs, 0, ',', ' ') }} XAF</span>
                        </div>

                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="font-medium text-gray-700">Statut:</span>
                            <span class="px-4 py-1.5 rounded-full text-sm font-medium {{ $as->flag ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $as->flag ? 'Approuvée' : 'En attente' }}
                            </span>
                        </div>
                    </div>

                    @if($as->flag && !$as->retrait_valide)
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <form action="{{ route('recup-retrait') }}" method="POST">
                                @csrf
                                <input type="hidden" name="as_id" value="{{ $as->id }}">
                                <button type="submit" class="w-full flex justify-center items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-500/30 text-white text-base font-medium rounded-lg transition duration-150 ease-in-out shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                    Demander le retrait
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($as->flag && $as->retrait_valide)
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200 mt-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Retrait validé</h3>
                                    <p class="mt-2 text-sm text-green-700">Votre demande de retrait a été validée. Vous pouvez maintenant récupérer votre avance.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
