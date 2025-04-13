@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Définir un nouveau salaire</h1>

        <form action="{{ route('salaires.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="id_employe" class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
                    <select name="id_employe" id="id_employe" class="w-full rounded-md border-gray-300" required>
                        <option value="">Sélectionner un employé</option>
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}">{{ $employe->name }} - {{ $employe->secteur }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="somme" class="block text-sm font-medium text-gray-700 mb-1">Montant du salaire</label>
                    <input type="number"
                           name="somme"
                           id="somme"
                           step="0.01"
                           class="w-full rounded-md border-gray-300"
                           required>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('salaires.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
