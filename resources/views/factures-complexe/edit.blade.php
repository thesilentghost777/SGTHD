@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Modifier la facture #{{ $facture->reference }}</h1>
        <p class="text-gray-600">Mise à jour des matières premières de la facture</p>
    </div>

    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Erreur!</strong>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('factures-complexe.update', $facture->id) }}" method="POST" id="factureForm" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="producteur_id" class="block text-gray-700 font-semibold mb-2">Producteur</label>
            <select id="producteur_id" name="producteur_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                <option value="">Sélectionner un producteur</option>
                @foreach ($producteurs as $producteur)
                <option value="{{ $producteur->id }}" {{ $facture->producteur_id == $producteur->id ? 'selected' : '' }}>
                    {{ $producteur->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-gray-700 font-semibold mb-2">Notes</label>
            <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ $facture->notes }}</textarea>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Matières premières</h2>

            <div class="overflow-x-auto mb-4">
                <table class="min-w-full divide-y divide-gray-200" id="materialsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Matière
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantité
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unité
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prix unitaire
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Montant
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="materialsTableBody">
                        @foreach ($facture->details as $index => $detail)
                        <tr class="material-row">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <select name="matieres[{{ $index }}][id]" class="matiere-select w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="">Sélectionner une matière</option>
                                    @foreach ($matieres as $matiere)
                                    <option value="{{ $matiere->id }}"
                                        data-prix="{{ $matiere->complexe->prix_complexe ?? $matiere->prix_unitaire }}"
                                        data-unite="{{ $matiere->unite_classique }}"
                                        {{ $detail->matiere_id == $matiere->id ? 'selected' : '' }}>
                                        {{ $matiere->nom }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="number" name="matieres[{{ $index }}][quantite]" min="0.001" step="0.001" value="{{ $detail->quantite }}" class="quantite-input w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="text" name="matieres[{{ $index }}][unite]" value="{{ $detail->unite }}" class="unite-input w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" readonly>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="prix-unitaire">{{ number_format($detail->prix_unitaire, 2, ',', ' ') }} FCFA</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="montant">{{ number_format($detail->montant, 2, ',', ' ') }} FCFA</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button type="button" class="text-red-600 hover:text-red-900 delete-row-btn">Supprimer</button>
                            </td>
                        </tr>
                        @endforeach

                        @if($facture->details->isEmpty())
                        <tr class="material-row">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <select name="matieres[0][id]" class="matiere-select w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="">Sélectionner une matière</option>
                                    @foreach ($matieres as $matiere)
                                    <option value="{{ $matiere->id }}"
                                        data-prix="{{ $matiere->complexe->prix_complexe ?? $matiere->prix_unitaire }}"
                                        data-unite="{{ $matiere->unite_classique }}">
                                        {{ $matiere->nom }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="number" name="matieres[0][quantite]" min="0.001" step="0.001" class="quantite-input w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="text" name="matieres[0][unite]" class="unite-input w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" readonly>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="prix-unitaire">-</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="montant">-</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button type="button" class="text-red-600 hover:text-red-900 delete-row-btn">Supprimer</button>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <button type="button" id="addMaterialBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                Ajouter une matière
            </button>
        </div>

        <div class="mb-6 text-right">
            <div class="text-lg font-bold">Total: <span id="totalAmount">{{ number_format($facture->montant_total, 2, ',', ' ') }}</span> FCFA</div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('factures-complexe.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Annuler
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Mettre à jour la facture
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const materialsTableBody = document.getElementById('materialsTableBody');
    const addMaterialBtn = document.getElementById('addMaterialBtn');
    const totalAmountSpan = document.getElementById('totalAmount');

    // Fonction pour initialiser les écouteurs d'événements sur une ligne
    function initRowEvents(row) {
        const matiereSelect = row.querySelector('.matiere-select');
        const quantiteInput = row.querySelector('.quantite-input');
        const uniteInput = row.querySelector('.unite-input');
        const prixUnitaireSpan = row.querySelector('.prix-unitaire');
        const montantSpan = row.querySelector('.montant');
        const deleteBtn = row.querySelector('.delete-row-btn');

        // Mettre à jour l'unité lorsqu'une matière est sélectionnée
        matiereSelect.addEventListener('change', function() {
            const selectedOption = matiereSelect.options[matiereSelect.selectedIndex];
            if (selectedOption.value) {
                uniteInput.value = selectedOption.getAttribute('data-unite');
                prixUnitaireSpan.textContent = parseFloat(selectedOption.getAttribute('data-prix')).toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' FCFA';

                updateMontant(row);
            } else {
                uniteInput.value = '';
                prixUnitaireSpan.textContent = '-';
                montantSpan.textContent = '-';
            }
        });

        // Mettre à jour le montant lorsque la quantité change
        quantiteInput.addEventListener('input', function() {
            updateMontant(row);
        });

        // Supprimer la ligne
        deleteBtn.addEventListener('click', function() {
            if (document.querySelectorAll('.material-row').length > 1) {
                row.remove();
                updateRowIndices();
                updateTotalAmount();
            } else {
                alert('Vous devez conserver au moins une ligne.');
            }
        });
    }

    // Fonction pour mettre à jour le montant d'une ligne
    function updateMontant(row) {
        const matiereSelect = row.querySelector('.matiere-select');
        const quantiteInput = row.querySelector('.quantite-input');
        const montantSpan = row.querySelector('.montant');

        if (matiereSelect.value && quantiteInput.value > 0) {
            const selectedOption = matiereSelect.options[matiereSelect.selectedIndex];
            const prix = parseFloat(selectedOption.getAttribute('data-prix'));
            const quantite = parseFloat(quantiteInput.value);
            const montant = prix * quantite;

            montantSpan.textContent = montant.toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' FCFA';
        } else {
            montantSpan.textContent = '-';
        }

        updateTotalAmount();
    }

    // Fonction pour mettre à jour les indices des lignes
    function updateRowIndices() {
        const rows = document.querySelectorAll('.material-row');
        rows.forEach((row, index) => {
            row.querySelectorAll('[name^="matieres["]').forEach(input => {
                const name = input.getAttribute('name');
                const newName = name.replace(/matieres\[\d+\]/, `matieres[${index}]`);
                input.setAttribute('name', newName);
            });
        });
    }

    // Fonction pour calculer le montant total
    function updateTotalAmount() {
        let total = 0;
        const montantSpans = document.querySelectorAll('.montant');

        montantSpans.forEach(span => {
            if (span.textContent !== '-') {
                total += parseFloat(span.textContent.replace(/[^\d,.-]/g, '').replace(',', '.'));
            }
        });

        totalAmountSpan.textContent = total.toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' FCFA';
    }

    // Ajouter une nouvelle ligne
    function addNewRow() {
        const rowCount = document.querySelectorAll('.material-row').length;
        const newRow = document.querySelector('.material-row').cloneNode(true);

        // Réinitialiser les champs de la nouvelle ligne
        newRow.querySelector('.matiere-select').selectedIndex = 0;
        newRow.querySelector('.quantite-input').value = '';
        newRow.querySelector('.unite-input').value = '';
        newRow.querySelector('.prix-unitaire').textContent = '-';
        newRow.querySelector('.montant').textContent = '-';

        // Mettre à jour les noms des champs
        newRow.querySelectorAll('[name^="matieres["]').forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(/matieres\[\d+\]/, `matieres[${rowCount}]`);
            input.setAttribute('name', newName);
        });

        materialsTableBody.appendChild(newRow);
        initRowEvents(newRow);
    }

    // Initialiser les événements sur toutes les lignes existantes
    document.querySelectorAll('.material-row').forEach(row => {
        initRowEvents(row);
    });

    // Écouteur pour le bouton d'ajout de matière
    addMaterialBtn.addEventListener('click', addNewRow);

    // Mettre à jour le total au chargement
    updateTotalAmount();
});
</script>
@endsection
