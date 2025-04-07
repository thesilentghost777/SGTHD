@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Assigner des matières premières</h1>
        <p class="text-gray-600">Attribuez des matières premières à un producteur</p>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('assignations.store') }}" method="POST" id="assignationForm">
            @csrf

            <div class="mb-6">
                <label for="producteur_id" class="block text-sm font-medium text-gray-700 mb-1">Producteur</label>
                <select id="producteur_id" name="producteur_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Sélectionnez un producteur</option>
                    @foreach($producteurs as $producteur)
                        <option value="{{ $producteur->id }}">{{ $producteur->name }} ({{ ucfirst($producteur->role) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="date_limite" class="block text-sm font-medium text-gray-700 mb-1">Date limite d'utilisation (optionnel)</label>
                <input type="date" id="date_limite" name="date_limite" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="border-t border-gray-200 pt-4 mb-4">
                <h2 class="text-lg font-medium text-gray-800 mb-4">Matières premières à assigner</h2>
            </div>

            <div id="matieres-container">
                <div class="matiere-item bg-gray-50 p-4 rounded-md mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Matière première</label>
                            <select name="matieres[0][id]" class="matiere-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Sélectionnez une matière</option>
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}"
                                        data-unite-minimale="{{ $matiere->unite_minimale }}"
                                        data-provient-complexe="{{ $matiere->provient_du_complexe ? 'oui' : 'non' }}">
                                        {{ $matiere->nom }} ({{ $matiere->unite_classique }})
                                        @if($matiere->provient_du_complexe) - [Complexe] @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                            <input type="number" name="matieres[0][quantite]" step="0.001" min="0.001" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                            <select name="matieres[0][unite]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                @foreach(array_keys($unites) as $unite)
                                    <option value="{{ $unite }}">{{ strtoupper($unite) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-2 complexe-indicator hidden">
                        <div class="bg-blue-50 p-2 rounded border border-blue-200">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i> Cette matière provient du complexe et sera incluse dans une facture.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between mb-6">
                <button type="button" id="add-matiere" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded">
                    + Ajouter une matière
                </button>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('assignations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Annuler
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let matiereIndex = 0;

        // Fonction pour mettre à jour l'indicateur de complexe
        function updateComplexeIndicator(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const provientComplexe = selectedOption.getAttribute('data-provient-complexe');
            const item = selectElement.closest('.matiere-item');
            const indicator = item.querySelector('.complexe-indicator');

            if (provientComplexe === 'oui') {
                indicator.classList.remove('hidden');
            } else {
                indicator.classList.add('hidden');
            }
        }

        // Appliquer aux matières existantes
        document.querySelectorAll('.matiere-select').forEach(select => {
            select.addEventListener('change', function() {
                updateComplexeIndicator(this);
            });
        });

        document.getElementById('add-matiere').addEventListener('click', function() {
            matiereIndex++;

            const container = document.getElementById('matieres-container');
            const newItem = document.createElement('div');
            newItem.className = 'matiere-item bg-gray-50 p-4 rounded-md mb-4';

            newItem.innerHTML = `
                <div class="flex justify-between mb-2">
                    <h3 class="text-md font-medium">Matière supplémentaire</h3>
                    <button type="button" class="remove-matiere text-red-500 hover:text-red-700">
                        Supprimer
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Matière première</label>
                        <select name="matieres[${matiereIndex}][id]" class="matiere-select w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Sélectionnez une matière</option>
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}"
                                    data-unite-minimale="{{ $matiere->unite_minimale }}"
                                    data-provient-complexe="{{ $matiere->provient_du_complexe ? 'oui' : 'non' }}">
                                    {{ $matiere->nom }} ({{ $matiere->unite_classique }})
                                    @if($matiere->provient_du_complexe) - [Complexe] @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                        <input type="number" name="matieres[${matiereIndex}][quantite]" step="0.001" min="0.001" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unité</label>
                        <select name="matieres[${matiereIndex}][unite]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                            @foreach(array_keys($unites) as $unite)
                                <option value="{{ $unite }}">{{ strtoupper($unite) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-2 complexe-indicator hidden">
                    <div class="bg-blue-50 p-2 rounded border border-blue-200">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-1"></i> Cette matière provient du complexe et sera incluse dans une facture.
                        </p>
                    </div>
                </div>
            `;

            container.appendChild(newItem);

            // Ajouter les gestionnaires d'événements pour la nouvelle matière
            const newSelect = newItem.querySelector('.matiere-select');
            newSelect.addEventListener('change', function() {
                updateComplexeIndicator(this);
            });

            // Ajouter les gestionnaires d'événements pour les nouveaux boutons de suppression
            newItem.querySelector('.remove-matiere').addEventListener('click', function() {
                container.removeChild(newItem);
            });
        });

        // Délégation d'événement pour les boutons de suppression
        document.getElementById('matieres-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-matiere')) {
                const matiereItem = e.target.closest('.matiere-item');
                matiereItem.parentNode.removeChild(matiereItem);
            }
        });
    });
</script>
@endsection
