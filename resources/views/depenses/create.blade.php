@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-blue-600 mb-8">Nouvelle Dépense</h1>

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

        <form action="{{ route('depenses.store') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nom">
                    Nom de la dépense
                </label>
                <input type="text" name="nom" id="nom" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                    Type de dépense
                </label>
                <select name="type" id="type" required onchange="toggleFields()"
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Sélectionner un type</option>
                    <option value="achat_matiere">Achat de matière</option>
                    <option value="livraison_matiere">Livraison de matière</option>
                    <option value="reparation">Réparation</option>
                    <option value="depense_fiscale">Depense fiscale</option>
                    <option value="autre">Autre</option>
                </select>
            </div>

            <div id="matiere-fields" class="hidden">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="idm">
                        Matière première
                    </label>
                    <select name="idm" id="idm"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Sélectionner une matière</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="quantite">
                        Quantité
                    </label>
                    <input type="number" step="0.01" name="quantite" id="quantite"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div id="reparation-fields" class="hidden">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="prix">
                        Prix
                    </label>
                    <input type="number" step="0.01" name="prix" id="prix"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date">
                    Date
                </label>
                <div class="flex space-x-2">
                    <input type="date" name="date" id="date" required
                        class="shadow appearance-none border rounded flex-grow py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <button type="button" id="todayButton"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        onclick="setToday()">
                        Aujourd'hui
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>

function setToday() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${day}`;
        document.getElementById('date').value = formattedDate;
    }


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
    } else if (type === 'reparation' || type === 'depense_fiscale' || type === 'autre') {
        reparationFields.classList.remove('hidden');
        prixField.required = true;
    }
}
</script>
@endsection
