@extends('layouts.app')
@section('content')
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

        @if(session('error'))
        <div class="bg-red-100 border border-red-300 text-red-800 rounded p-4 mb-4">
            {{ session('error') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-800 rounded p-4 mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('manquant.store') }}" method="POST">
            @csrf
            <!-- Employé -->
            <div class="mb-4">
                <label for="employe_id" class="block text-gray-700 font-medium mb-2">Employé</label>
                <select name="employe_id" id="employe_id" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Sélectionnez un employé</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employe_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                    @endforeach
                </select>
                @error('employe_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Montant du Manquant -->
            <div class="mb-4">
                <label for="montant" class="block text-gray-700 font-medium mb-2">Montant du Manquant</label>
                <input type="number" name="montant" id="montant" step="0.01" min="1" value="{{ old('montant') }}" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                @error('montant')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Explication -->
            <div class="mb-4">
                <label for="explication" class="block text-gray-700 font-medium mb-2">Explication</label>
                <textarea name="explication" id="explication" rows="4" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>{{ old('explication') }}</textarea>
                @error('explication')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
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
@endsection