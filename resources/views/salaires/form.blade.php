@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card max-w-xl mx-auto">
        <h2 class="card-title">Gestion des salaires</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('store-salaire') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Employé
                    </label>
                    <select name="id_employe" class="form-input" required>
                        <option value="">Sélectionner un employé</option>
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}">{{ $employe->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Salaire mensuel
                    </label>
                    <input type="number" 
                           name="somme" 
                           class="form-input" 
                           required 
                           min="0" 
                           step="1000">
                </div>

                <button type="submit" class="btn-blue w-full">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection