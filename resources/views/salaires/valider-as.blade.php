@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête de la page -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                Validation des demandes d'avance
            </h1>
            <div class="h-1 w-32 bg-blue-600 rounded-full"></div>
        </div>

        <!-- Section des demandes -->
        <div class="bg-white rounded-xl shadow-sm mb-10 p-6">
            @if($demandes->isEmpty())
                <div class="flex flex-col items-center justify-center py-16">
                    <div class="bg-blue-50 rounded-full p-6 mb-4">
                        <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">
                        Aucune demande en attente
                    </h3>
                    <p class="text-gray-500 text-center max-w-md">
                        Il n'y a actuellement aucune demande d'avance sur salaire à valider.
                    </p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($demandes as $demande)
                        <div class="bg-gray-50 border border-gray-100 rounded-lg p-6 transition-all hover:shadow-md">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-800">
                                        {{ $demande->employe->name }}
                                    </h3>
                                    <p class="text-blue-600 font-medium mt-1">
                                        {{ number_format($demande->sommeAs, 0, ',', ' ') }} XAF
                                    </p>
                                </div>
                                <form action="{{ route('store-validation') }}" method="POST" class="flex gap-3">
                                    @csrf
                                    <input type="hidden" name="as_id" value="{{ $demande->id }}">
                                    <button type="submit" name="decision" value="1"
                                        class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approuver
                                    </button>
                                    <button type="submit" name="decision" value="0"
                                        class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Refuser
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            </div>
@endsection
