<!-- resources/views/delis/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-600">Liste des Incidents</h1>
            <a href="{{ route('delis.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                Nouvel incident
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">Nom</th>
                        <th class="py-3 px-4 text-left">Description</th>
                        <th class="py-3 px-4 text-left">Sanction(Montant)</th>
                        <th class="py-3 px-4 text-left">Employés concernés</th>
                        <th class="py-3 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($delis as $deli)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $deli->nom }}</td>
                        <td class="py-3 px-4">{{ $deli->description }}</td>
                        <td class="py-3 px-4">{{ number_format($deli->montant, 0, ',', ' ') }} F CFA</td>
                        <td class="py-3 px-4">
                            @foreach($deli->employes as $employe)
                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm mr-1 mb-1">
                                    {{ $employe->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('delis.edit', $deli) }}"
                                   class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded text-sm">
                                    Modifier
                                </a>
                                <form action="{{ route('delis.destroy', $deli) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded text-sm"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce deli ?')">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
