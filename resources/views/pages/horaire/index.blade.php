@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Contrôle des Horaires</h2>

        {{-- Horloge --}}
        @include('components.clock')

        {{-- Messages de notification --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Boutons Arrivée/Départ --}}
        <div class="flex space-x-4 mb-8">
            <form action="{{ route('horaire.arrivee') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Marquer l'arrivée
                </button>
            </form>

            <form action="{{ route('horaire.depart') }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                    Marquer le départ
                </button>
            </form>
        </div>

        {{-- Formulaire horaires manuels --}}
        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <h3 class="text-lg font-semibold mb-4">Saisie manuelle des horaires</h3>
            <form action="{{ route('horaire.enregistrer') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="arrive" class="block text-sm font-medium text-gray-700">Heure d'arrivée</label>
                        <input type="time" name="arrive" id="arrive" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    </div>
                    <div>
                        <label for="depart" class="block text-sm font-medium text-gray-700">Heure de départ</label>
                        <input type="time" name="depart" id="depart" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded">
                        Valider
                    </button>
                </div>
            </form>
        </div>

        {{-- Tableau des horaires --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrivée</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Départ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($horaires as $horaire)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $horaire->arrive->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $horaire->arrive->format('H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $horaire->depart ? $horaire->depart->format('H:i') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
