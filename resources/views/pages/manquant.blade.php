@extends('pages.chef_production.chef_production_default')

@section('page-content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <!-- Box principale -->
    <div class="max-w-xl bg-white p-6 rounded-lg shadow-lg relative">
        <!-- Conseil -->
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-md">
            <h3 class="text-lg font-semibold">Conseil important</h3>
            <p class="mt-2">
                Les manquants peuvent avoir un impact significatif sur la vie des employés, notamment en réduisant leur revenu en fin de mois.
                Cela peut entraîner des difficultés financières, un stress psychologique, et une baisse de motivation au travail.
                Nous recommandons d'évaluer avec empathie les situations individuelles et de considérer des alternatives équitables
                pour minimiser les impacts négatifs.
            </p>
        </div>

        <!-- Formulaire -->
        <h2 class="text-2xl font-semibold text-center text-blue-700 mb-4">Attribuer un Manquant</h2>
        <div class="border-t-2 border-blue-500 my-4"></div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 rounded p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('manquant.store') }}" method="POST" x-data>
            @csrf

            <!-- Employé -->
            <div class="mb-4">
                <label for="id_employe" class="block text-gray-700 font-medium mb-2">Employé</label>
                <select name="id_employe" id="id_employe" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Sélectionnez un employé</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Montant du Manquant -->
            <div class="mb-4">
                <label for="manquants" class="block text-gray-700 font-medium mb-2">Montant du Manquant</label>
                <input type="number" name="manquants" id="manquants" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <!-- Bouton de soumission -->
            <div class="text-center">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Attribuer
                </button>
            </div>
        </form>

        <!-- Reflection Effect -->
        <div class="absolute bottom-[-10px] left-0 right-0">
            <div class="h-6 bg-gradient-to-t from-gray-300 to-transparent opacity-50 blur-md rounded-b-lg"></div>
        </div>
    </div>
</div>

<!-- Tailwind CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.css" rel="stylesheet">

<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
