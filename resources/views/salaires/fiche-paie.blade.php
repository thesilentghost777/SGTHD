@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold">Fiche de Paie</h1>
            <p class="text-gray-600">{{ $mois->locale('fr')->format('F Y') }}</p>
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
                    <p class="font-medium">{{ $employe->secteur ?? 'Non spécifié' }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Date d'entrée en service</p>
                    <p class="font-medium">{{ $employe->annee_debut_service ?? 'Non spécifiée' }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Poste</p>
                    <p class="font-medium">{{ ucfirst($employe->role ?? 'Non spécifié') }}</p>
                </div>
            </div>
        </div>

        {{-- Détails du salaire --}}
        <div class="space-y-4">
            <div class="flex justify-between py-2 border-b">
                <span class="font-medium">Salaire de base</span>
                <span>{{ number_format($fichePaie['salaire_base'], 0, ',', ' ') }} FCFA</span>
            </div>

            @if($fichePaie['avance_salaire'] > 0)
            <div class="flex justify-between py-2 border-b text-red-600">
                <span class="font-medium">Avance sur salaire</span>
                <span>- {{ number_format($fichePaie['avance_salaire'], 0, ',', ' ') }} FCFA</span>
            </div>
            @endif

            {{-- Déductions --}}
            @foreach($fichePaie['deductions'] as $label => $montant)
                @if($montant > 0)
                <div class="flex justify-between py-2 border-b text-red-600">
                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $label)) }}</span>
                    <span>- {{ number_format($montant, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
            @endforeach

            @if($fichePaie['primes'] > 0)
            <div class="flex justify-between py-2 border-b text-green-600">
                <span class="font-medium">Primes</span>
                <span>+ {{ number_format($fichePaie['primes'], 0, ',', ' ') }} FCFA</span>
            </div>
            @endif

            <div class="flex justify-between py-4 border-t-2 border-gray-800 text-xl font-bold">
                <span>Salaire net à payer</span>
                <span>{{ number_format($fichePaie['salaire_net'], 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        {{-- Détails des incidents si présents --}}
        @if(isset($listeIncidents) && count($listeIncidents) > 0)
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-3">Détail des incidents</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">Description</th>
                            <th class="px-4 py-2 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listeIncidents as $incident)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $incident['date'] }}</td>
                            <td class="px-4 py-2">{{ $incident['description'] }}</td>
                            <td class="px-4 py-2 text-right text-red-600">{{ number_format($incident['montant'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Bouton de validation de retrait --}}
        <div class="mt-10 text-center">
            @if($salaire->retrait_demande && !$salaire->retrait_valide)
                <form action="{{ route('salaires.valider-retrait', $employe->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-colors">
                        <i class="fas fa-check-circle mr-2"></i>Valider le retrait
                    </button>
                </form>
            @elseif($salaire->retrait_valide)
                <div class="bg-gray-100 text-gray-800 py-3 px-6 rounded-lg inline-block">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>Retrait déjà validé
                </div>
            @else
                <div class="bg-gray-100 text-gray-800 py-3 px-6 rounded-lg inline-block">
                    <i class="fas fa-clock text-yellow-600 mr-2"></i>En attente d'initiation de la demande de retrait
                </div>
            @endif
        </div>

        {{-- Informations légales --}}
        <div class="mt-10 text-xs text-gray-500 text-center">
            <p>Ce document tient lieu de reçu officiel. Une copie est conservée dans les archives de l'entreprise.</p>
            <p>Document généré le {{ \Carbon\Carbon::now()->locale('fr')->format('d F Y à H:i') }}</p>
        </div>
    </div>
</div>
@endsection
