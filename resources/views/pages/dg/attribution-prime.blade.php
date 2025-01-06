@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Attribution de Prime</h1>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('primes.store') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="id_employe" class="block text-sm font-medium text-gray-700 mb-2">
                        Employé
                    </label>
                    <select name="id_employe" id="id_employe"
                            class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            required>
                        <option value="">Sélectionner un employé</option>
                        @foreach($employes as $employe)
                        <option value="{{ $employe->id }}">
                            {{ $employe->name }} - {{ $employe->role }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label for="libelle" class="block text-sm font-medium text-gray-700 mb-2">
                        Catégorie de Prime
                    </label>
                    <input type="text" name="libelle" id="libelle"
                           class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           required>
                </div>

                <div class="mb-6">
                    <label for="montant" class="block text-sm font-medium text-gray-700 mb-2">
                        Montant (FCFA)
                    </label>
                    <input type="number" name="montant" id="montant"
                           class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           min="0" required>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition duration-200">
                        Attribuer la Prime
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
