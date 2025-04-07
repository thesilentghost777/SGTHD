@extends('pages.chef_production.chef_production_default')

@section('page-content')
<div class="container mx-auto px-4 py-8">
    <!-- Bouton "Nouvelle commande" pour afficher/masquer le formulaire -->
    <div class="text-center">
        <button id="toggleFormButton"
                class="px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
            Nouvelle Commande
        </button>
    </div>

    <!-- Formulaire d'ajout de commande caché initialement -->
    <div id="orderForm" class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6 mt-6 hidden">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Nouvelle Commande</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <form action="{{ route('chef.commandes.store2') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- Champs de formulaire -->
                <div>
                    <label for="libelle" class="block text-sm font-medium text-gray-700">Libellé</label>
                    <input type="text" name="libelle" id="libelle"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('libelle') }}" required maxlength="50">
                </div>

                <div>
                    <label for="produit" class="block text-sm font-medium text-gray-700">Produit</label>
                    <select name="produit" id="produit"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                        <option value="">Sélectionner un produit</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->code_produit }}" {{ old('produit') == $produit->code_produit ? 'selected' : '' }}>
                                {{ $produit->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                    <input type="number" name="quantite" id="quantite" min="1"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('quantite') }}" required>
                </div>

                <div class="flex items-end space-x-2">
                    <div class="flex-grow">
                      <label for="date_commande" class="block text-sm font-medium text-gray-700">Date de commande</label>
                      <input type="datetime-local" name="date_commande" id="date_commande"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                      value="{{ old('date_commande') }}" required>
                    </div>
                    <button type="button"
                      class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 h-10"
                      onclick="(function() {
                        const now = new Date();
                        const year = now.getFullYear();
                        const month = String(now.getMonth() + 1).padStart(2, '0');
                        const day = String(now.getDate()).padStart(2, '0');
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                        document.getElementById('date_commande').value = formattedDateTime;
                      })();">
                      Aujourd'hui
                    </button>
                  </div>

                <div>
                    <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="categorie" id="categorie"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                        <option value="">Sélectionner une catégorie</option>
                        <option value="patissier" {{ old('categorie') == 'patisserie' ? 'selected' : '' }}>Patisserie</option>
                        <option value="boulanger" {{ old('categorie') == 'boulangerie' ? 'selected' : '' }}>Boulangerie</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>

   <!-- Section Liste des commandes -->
   <div class="max-w-6xl mx-auto mt-8 bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Liste des Commandes</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($commandes->sortByDesc('created_at') as $commande)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $commande->libelle }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $commande->produit_fixe->nom ?? 'Produit non trouvé' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $commande->quantite }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $commande->date_commande }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $commande->categorie }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $commande->valider ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $commande->valider ? 'Effectuée' : 'Non effectuée' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('commande.edit', $commande->id) }}"
                           class="text-indigo-600 hover:text-indigo-900 mr-3">Modifier</a>

                        <button onclick="deleteCommande({{ $commande->id }})"
                                class="text-red-600 hover:text-red-900">
                            Supprimer
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
    document.getElementById('toggleFormButton').addEventListener('click', function() {
        var form = document.getElementById('orderForm');
        form.classList.toggle('hidden');
    });


    function deleteCommande(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')) {
            fetch(`/commandes/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Erreur lors de la suppression');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la suppression');
            });
        }
    }

</script>
@endsection
