@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-blue-600 mb-8">Modifier la Dépense</h1>

        @if($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    html: '{!! implode("<br>", $errors->all()) !!}',
                    confirmButtonColor: '#3085d6'
                });
            </script>
        @endif

        <form action="{{ route('depenses.update', $depense) }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nom">
                    Nom de la dépense
                </label>
                <input type="text" name="nom" id="nom" required value="{{ $depense->nom }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                    Type de dépense
                </label>
                <select name="type" id="type" required onchange="toggleFields()"
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="achat_matiere" {{ $depense->type === 'achat_matiere' ? 'selected' : '' }}>
                        Achat de matière
                    </option>
                    <option value="livraison_matiere" {{ $depense->type === 'livraison_matiere' ? 'selected' : '' }}>
                        Livraison de matière
                    </option>
                    <option value="reparation" {{ $depense->type === 'reparation' ? 'selected' : '' }}>
                        Réparation
                    </option>
                </select>
            </div>

            <div id="matiere-fields" class="{{ in_array($depense->type, ['achat_matiere', 'livraison_matiere']) ? '' : 'hidden' }}">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="idm">
                        Matière première
                    </label>
                    <select name="idm" id="idm"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Sélectionner une matière</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}" {{ $depense->idm === $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="quantite">
                        Quantité
                    </label>
                    <input type="number" step="0.01" name="quantite" id="quantite"
                        value="{{ old('quantite', $depense->prix / ($depense->matiere->prix_unitaire ?? 1)) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div id="reparation-fields" class="{{ $depense->type === 'reparation' ? '' : 'hidden' }}">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="prix">
                        Prix
                    </label>
                    <input type="number" step="0.01" name="prix" id="prix"
                        value="{{ $depense->type === 'reparation' ? $depense->prix : '' }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date">
                    Date
                </label>
                <input type="date" name="date" id="date" required value="{{ $depense->date->format('Y-m-d') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center justify-end space-x-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Mettre à jour
                </button>
                <a href="{{ route('depenses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFields() {
    const type = document.getElementById('type').value;
    const matiereFields = document.getElementById('matiere-fields');
    const reparationFields = document.getElementById('reparation-fields');
    const idmField = document.getElementById('idm');
    const quantiteField = document.getElementById('quantite');
    const prixField = document.getElementById('prix');

    // Cacher tous les champs
    matiereFields.classList.add('hidden');
    reparationFields.classList.add('hidden');

    // Réinitialiser required
    idmField.required = false;
    quantiteField.required = false;
    prixField.required = false;

    if (type === 'achat_matiere' || type === 'livraison_matiere') {
        matiereFields.classList.remove('hidden');
        idmField.required = true;
        quantiteField.required = true;
    } else if (type === 'reparation') {
        reparationFields.classList.remove('hidden');
        prixField.required = true;
    }
}

// Initialiser les champs au chargement
document.addEventListener('DOMContentLoaded', function() {
    toggleFields();
});
</script>
@endsection
