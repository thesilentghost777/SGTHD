@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 bg-blue-600 text-white">
            <h1 class="text-2xl font-bold">Modifier les informations du complexe</h1>
            <p class="mt-2">Mettez à jour les informations de base de votre complexe.</p>
        </div>

        @if (session('success'))
        <div class="bg-blue-100 text-blue-700 p-4 border-l-4 border-blue-500">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 border-l-4 border-red-500">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('setup.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-gray-50 p-4 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Informations du complexe</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="nom" class="block text-gray-700 font-medium mb-2">Nom du complexe</label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $complexe->nom) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label for="localisation" class="block text-gray-700 font-medium mb-2">Localisation</label>
                        <input type="text" name="localisation" id="localisation" value="{{ old('localisation', $complexe->localisation) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="revenu_mensuel" class="block text-gray-700 font-medium mb-2">Revenu mensuel (FCFA)</label>
                        <input type="number" name="revenu_mensuel" id="revenu_mensuel" value="{{ old('revenu_mensuel', $complexe->revenu_mensuel) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="revenu_annuel" class="block text-gray-700 font-medium mb-2">Revenu annuel (FCFA)</label>
                        <input type="number" name="revenu_annuel" id="revenu_annuel" value="{{ old('revenu_annuel', $complexe->revenu_annuel) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="solde" class="block text-gray-700 font-medium mb-2">Solde actuel (FCFA)</label>
                        <input type="number" name="solde" id="solde" value="{{ old('solde', $complexe->solde) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="caisse_sociale" class="block text-gray-700 font-medium mb-2">Caisse sociale (FCFA)</label>
                        <input type="number" name="caisse_sociale" id="caisse_sociale" value="{{ old('caisse_sociale', $complexe->caisse_sociale) }}"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Retour au tableau de bord
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
