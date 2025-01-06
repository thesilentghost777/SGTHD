@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Gestion des Matières Premières</h1>
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold mb-3">Guide des champs du formulaire</h2>
            <div class="space-y-2 text-sm text-gray-600">
                <p><span class="font-medium">Nom de la matière :</span> Le nom identifiant la matière première (ex: Farine, Sucre, etc.)</p>
                <p><span class="font-medium">Unité minimale :</span> La plus petite unité de mesure utilisée pour cette matière (ex: grammes pour les solides, millilitres pour les liquides)</p>
                <p><span class="font-medium">Unité classique :</span> L'unité de mesure standard pour l'achat en gros (ex: kg pour les solides, litre pour les liquides)</p>
                <p><span class="font-medium">Quantité par unité :</span> Quantite de matieres en unites classique contenues dans une occurence de la matiere (ex: 50, donc dans 1 sac de farine il y'a 50 kg de farine)</p>
                <p><span class="font-medium">Quantité :</span> Quantité totale d'unités classiques en stock (ex: nombre de kg ou de litres)</p>
                <p><span class="font-medium">Prix unitaire :</span> Prix d'achat d'une unité classique en XAF (ex: prix d'un kg ou d'un litre)</p>
            </div>
            <div class="mt-6">
                <h3 class="font-semibold mb-2">Exemples concrets :</h3>
                <div class="bg-white p-4 rounded-lg space-y-4">
                    <div>
                        <h4 class="font-medium text-blue-600">Exemple 1 : Farine de blé</h4>
                        <ul class="mt-1 space-y-1 text-sm">
                            <li>• Nom : Farine de blé</li>
                            <li>• Unité minimale : g (gramme)</li>
                            <li>• Unité classique : kg (kilogramme)</li>
                            <li>• Quantité par unité : 50 (car 1 unite(sac de farine) = 50 kg)</li>
                            <li>• Nombre d'unités : 25 (stock de 25 sac)</li>
                            <li>• Prix unitaire : 20000 (20000 XAF par sac de farine)</li>
                        </ul>
                    </div>
                    <div>
        </div>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('chef.matieres.store') }}" method="POST" class="mb-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nom de la matière
                    </label>
                    <input type="text"
                           name="nom"
                           class="form-input w-full rounded-md border-gray-300"
                           value="{{ old('nom') }}"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Unité minimale
                    </label>
                    <select name="unite_minimale"
                            id="unite_minimale"
                            class="form-select w-full rounded-md border-gray-300"
                            required
                            onchange="updateUniteClassique()">
                        <option value="">Sélectionner</option>
                        @foreach($unites_minimales as $unite)
                            <option value="{{ $unite }}"
                                    {{ old('unite_minimale') == $unite ? 'selected' : '' }}>
                                @switch($unite)
                                    @case('g')
                                        Gramme (g)
                                        @break
                                    @case('kg')
                                        Kilogramme (kg)
                                        @break
                                    @case('ml')
                                        Millilitre (ml)
                                        @break
                                    @case('cl')
                                        Centilitre (cl)
                                        @break
                                    @case('dl')
                                        Décilitre (dl)
                                        @break
                                    @case('l')
                                        Litre (l)
                                        @break
                                    @case('cc')
                                        Cuillère à café
                                        @break
                                    @case('cs')
                                        Cuillère à soupe
                                        @break
                                    @case('pincee')
                                        Pincée
                                        @break
                                    @case('unite')
                                        Unité
                                        @break
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Unité classique
                    </label>
                    <select name="unite_classique"
                            id="unite_classique"
                            class="form-select w-full rounded-md border-gray-300"
                            required>
                        <option value="">Sélectionner</option>
                        @foreach($unites_classiques as $unite)
                            <option value="{{ $unite }}"
                                    {{ old('unite_classique') == $unite ? 'selected' : '' }}>
                                @switch($unite)
                                    @case('kg')
                                        Kilogramme (kg)
                                        @break
                                    @case('litre')
                                        Litre (L)
                                        @break
                                    @case('unite')
                                        Unité
                                        @break
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Quantité par unité(par sac , sachet , ...)
                        <span class="text-xs text-gray-500">(en unité classique)</span>
                    </label>
                    <input type="number"
                           name="quantite_par_unite"
                           step="0.001"
                           class="form-input w-full rounded-md border-gray-300"
                           value="{{ old('quantite_par_unite') }}"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Quantité
                    </label>
                    <input type="number"
                           name="quantite"
                           class="form-input w-full rounded-md border-gray-300"
                           value="{{ old('quantite') }}"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Prix unitaire (XAF)
                    </label>
                    <input type="number"
                           name="prix_unitaire"
                           step="0.01"
                           class="form-input w-full rounded-md border-gray-300"
                           value="{{ old('prix_unitaire') }}"
                           required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Ajouter la matière première
                </button>
            </div>
        </form>
    <!-- ... Le reste du code reste inchangé jusqu'à la table ... -->

    <!-- Table des matières -->
    <div class="overflow-x-auto mt-8">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unité min.</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unité class.</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qté/unité</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unit.</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix/unité min.</th>
                    <th class="px-6 py-3 border-b text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($matieres as $matiere)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $matiere->nom }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $matiere->unite_minimale }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $matiere->unite_classique }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $matiere->quantite_par_unite }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $matiere->quantite }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($matiere->prix_unitaire, 0, ',', ' ') }} XAF</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($matiere->prix_par_unite_minimale, 2, ',', ' ') }} XAF</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="editMatiere({{ $matiere->id }})"
                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                            Modifier
                        </button>
                        <form action="{{ route('chef.matieres.destroy', $matiere) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:text-red-900">
                                Supprimer
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $matieres->links() }}
    </div>
</div>

<!-- Modal de modification -->
<!-- Modal de modification -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Modifier la matière première</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nom de la matière
                        </label>
                        <input type="text" name="nom" id="edit_nom"
                               class="form-input w-full rounded-md border-gray-300" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Unité minimale
                        </label>
                        <select name="unite_minimale" id="edit_unite_minimale"
                                class="form-select w-full rounded-md border-gray-300" required>
                            @foreach($unites_minimales as $unite)
                                <option value="{{ $unite }}">
                                    @switch($unite)
                                        @case('g') Gramme (g) @break
                                        @case('kg') Kilogramme (kg) @break
                                        @case('ml') Millilitre (ml) @break
                                        @case('cl') Centilitre (cl) @break
                                        @case('dl') Décilitre (dl) @break
                                        @case('l') Litre (l) @break
                                        @case('cc') Cuillère à café @break
                                        @case('cs') Cuillère à soupe @break
                                        @case('pincee') Pincée @break
                                        @case('unite') Unité @break
                                    @endswitch
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Unité classique
                        </label>
                        <select name="unite_classique" id="edit_unite_classique"
                                class="form-select w-full rounded-md border-gray-300" required>
                            @foreach($unites_classiques as $unite)
                                <option value="{{ $unite }}">
                                    @switch($unite)
                                        @case('kg') Kilogramme (kg) @break
                                        @case('litre') Litre (L) @break
                                        @case('unite') Unité @break
                                    @endswitch
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Quantité par unité
                        </label>
                        <input type="number" name="quantite_par_unite" id="edit_quantite_par_unite"
                               step="0.001" class="form-input w-full rounded-md border-gray-300" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre d'unités
                        </label>
                        <input type="number" name="quantite" id="edit_quantite"
                               class="form-input w-full rounded-md border-gray-300" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Prix unitaire (XAF)
                        </label>
                        <input type="number" name="prix_unitaire" id="edit_prix_unitaire"
                               step="0.01" class="form-input w-full rounded-md border-gray-300" required>
                    </div>
                </div>

                <div class="mt-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editMatiere(id) {
    fetch(`/chef/matieres/${id}/edit`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Remplir tous les champs du formulaire
            document.getElementById('edit_nom').value = data.nom;
            document.getElementById('edit_unite_minimale').value = data.unite_minimale;
            document.getElementById('edit_unite_classique').value = data.unite_classique;
            document.getElementById('edit_quantite_par_unite').value = data.quantite_par_unite;
            document.getElementById('edit_quantite').value = data.quantite;
            document.getElementById('edit_prix_unitaire').value = data.prix_unitaire;

            // Mettre à jour l'action du formulaire
            document.getElementById('editForm').action = `/chef/matieres/${id}`;

            // Afficher la modal
            document.getElementById('editModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la récupération des données');
        });
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Mise à jour des unités classiques en fonction de l'unité minimale
document.getElementById('edit_unite_minimale').addEventListener('change', function() {
    const uniteMinimale = this.value;
    const uniteClassiqueSelect = document.getElementById('edit_unite_classique');

    // Récupérer les unités classiques permises
    const unitesPermises = getUnitesClassiquesPermises(uniteMinimale);

    // Mettre à jour les options
    uniteClassiqueSelect.innerHTML = unitesPermises.map(unite =>
        `<option value="${unite}">${getUniteClassiqueLabel(unite)}</option>`
    ).join('');
});

function getUnitesClassiquesPermises(uniteMinimale) {
    const mapping = {
        'g': ['kg'],
        'kg': ['kg'],
        'ml': ['litre'],
        'cl': ['litre'],
        'dl': ['litre'],
        'l': ['litre'],
        'cc': ['kg', 'litre'],
        'cs': ['kg', 'litre'],
        'pincee': ['kg'],
        'unite': ['unite']
    };
    return mapping[uniteMinimale] || ['unite'];
}

function getUniteClassiqueLabel(unite) {
    const labels = {
        'kg': 'Kilogramme (kg)',
        'litre': 'Litre (L)',
        'unite': 'Unité'
    };
    return labels[unite] || unite;
}
</script>
@endsection
