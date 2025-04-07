@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-3xl mx-auto" id="fiche-paie">
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
            {{-- Déductions --}}
            @foreach($fichePaie['deductions'] as $label => $montant)
                @if($montant > 0)
                <div class="flex justify-between py-2 border-b text-red-600">
                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $label)) }}</span>
                    <span>- {{ number_format($montant, 2) }} FCFA</span>
                </div>
                @endif
            @endforeach
            @if($fichePaie['primes'] > 0)
            <div class="flex justify-between py-2 border-b text-green-600">
                <span class="font-medium">Primes</span>
                <span>+ {{ number_format($fichePaie['primes'], 2) }} FCFA</span>
            </div>
            @endif
            <div class="flex justify-between py-4 border-t-2 border-gray-800 text-xl font-bold">
                <span>Salaire net à payer</span>
                <span>{{ number_format($fichePaie['salaire_net'], 2) }} FCFA</span>
            </div>
        </div>
        {{-- Actions --}}
        <div class="mt-8 flex justify-center space-x-4 no-print">

            @if(!$salaire->retrait_demande && !$salaire->flag && !$salaire->retrait_valide)
            <form action="{{ route('salaires.demande-retrait2', $salaire->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Demander le retrait
                </button>
            </form>
            @elseif ($salaire->retrait_demande && !$salaire->flag)
            <div class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">
                Demande de retrait en cours
            </div>
            @else
            <div class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">
                indisponible
            </div>
            @endif
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                Imprimer
            </button>
        </div>
    </div>
</div>
<style>
    @media print {
        /* Cacher tous les éléments sauf la fiche de paie */
        body * {
            visibility: hidden;
        }

        #fiche-paie, #fiche-paie * {
            visibility: visible;
        }

        #fiche-paie {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            margin: 0;
            box-shadow: none;
        }

        /* Cacher les boutons et les éléments avec classe no-print */
        .no-print {
            display: none !important;
        }

        /* Format d'impression */
        @page {
            size: A4;
            margin: 2cm;
        }

        /* Assurer que les couleurs s'impriment correctement */
        .text-red-600 {
            color: #dc2626 !important;
        }

        .text-green-600 {
            color: #059669 !important;
        }

        /* Améliorer la lisibilité lors de l'impression */
        .bg-gray-50 {
            background-color: #f9fafb !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Assurer que les bordures s'impriment */
        .border-b, .border-t-2 {
            border-color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endsection