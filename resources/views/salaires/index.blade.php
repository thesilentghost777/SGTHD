@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestion des Salaires</h1>
        <a href="{{ route('salaires.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Nouveau Salaire
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($salaires as $salaire)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $salaire->employe->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($salaire->somme, 2) }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($salaire->retrait_valide)
                            <span class="px-2 py-1 text-sm text-green-800 bg-green-100 rounded-full">Payé</span>
                        @elseif($salaire->retrait_demande)
                            <span class="px-2 py-1 text-sm text-yellow-800 bg-yellow-100 rounded-full">En attente</span>
                        @else
                            <span class="px-2 py-1 text-sm text-gray-800 bg-gray-100 rounded-full">Non retiré</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap space-x-2">
                        <a href="{{ route('salaires.fiche-paie', $salaire->id_employe) }}" class="text-blue-600 hover:text-blue-900">Fiche de paie</a>
                        <a href="{{ route('salaires.edit', $salaire) }}" class="text-yellow-600 hover:text-yellow-900">Modifier</a>
                        @if(auth()->user()->role === 'admin')
                        <form action="{{ route('salaires.destroy', $salaire) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                        </form>
                        @endif
                         {{-- Ajout du bouton Valider pour les demandes en attente --}}
    @if($salaire->retrait_demande && !$salaire->retrait_valide)
    <form action="{{ route('salaires.valider-retrait', $salaire->id_employe) }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="text-green-600 hover:text-green-900 font-medium">
            Valider le retrait
        </button>
    </form>
@endif

@if(auth()->user()->role === 'admin')
    <form action="{{ route('salaires.destroy', $salaire) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
    </form>
@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
