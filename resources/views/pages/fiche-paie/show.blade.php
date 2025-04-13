@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold">Fiche de Paie</h1>
            <p class="text-gray-600">{{ $mois->format('F Y') }}</p>
        </div>

        {{-- Informations de l'employé --}}
        <div class="mb-8 p-4 bg-gray-50 rounded-lg">
            <h2 class="text-xl font-semibold mb-4">Informations de l'employé</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600">Nom</p>
                    <p class="font-medium">{{ $employe->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Secteur</p>
                    <p class="font-medium">{{ $employe->secteur }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Date d'entrée en service</p>
                    <p class="font-medium">{{ $employe->annee_debut_service }}</p>
                </div>
            </div>
        </div>

        {{-- Détails du salaire --}}
        <div class="space-y-4">
            <div class="flex justify-between py-2 border-b">
                <span class="font-medium">Salaire de base</span>
                <span>{{ number_format($fichePaie['salaire_base'], 2) }} FCFA</span>
            </div>

            @if($fichePaie['avance_salaire'] > 0)
            <div class="flex justify-between py-2 border-b text-red-600">
                <span class="font-medium">Avance sur salaire</span>
                <span>- {{ number_format($fichePaie['avance_salaire'], 2) }} FCFA</span>
            </div>
            @endif

            {{-- Inclure les déductions --}}
            @include('pages.fiche-paie._deductions')

            {{-- Section des délis --}}
            @if(!empty($fichePaie['delis']['details']))
            <div class="mt-4 space-y-2">
                <div class="flex justify-between py-2 border-b bg-red-50">
                    <span class="font-medium text-red-700">Délis du mois</span>
                    <span class="text-red-600">- {{ number_format($fichePaie['delis']['montant'], 2) }} FCFA</span>
                </div>
                <div class="pl-4 text-sm space-y-1">
                    @foreach($fichePaie['delis']['details'] as $deli)
                    <div class="flex justify-between text-gray-600">
                        <span>{{ $deli['nom'] }} ({{ \Carbon\Carbon::parse($deli['date'])->format('d/m/Y') }})</span>
                        <span class="text-red-600">- {{ number_format($deli['montant'], 2) }} FCFA</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex justify-between py-4 border-t-2 border-gray-800 text-xl font-bold">
                <span>Salaire net à payer</span>
                <span>{{ number_format($fichePaie['salaire_net'], 2) }} FCFA</span>
            </div>
        </div>

        {{-- Sélecteur de mois --}}
        <div class="mt-8 text-center">
            <form action="{{ route('fiche-paie.show') }}" method="GET" class="inline-flex items-center">
                <input type="month"
                       name="mois"
                       value="{{ $mois->format('Y-m') }}"
                       class="rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700">
                    Voir
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
