@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mb-8">
            <h2 class="text-lg font-bold text-blue-900">Comment fonctionne le Calculateur ?</h2>
            <p class="mt-2 text-blue-700">
                Le calculateur vous permet de déterminer les quantités exactes des ingrédients nécessaires pour un nombre donné
                d'unités à produire. Voici comment l'utiliser :
            </p>
            <ol class="mt-4 list-decimal list-inside text-blue-700 space-y-2">
                <li>Dans le champ de saisie <strong>Quantité</strong>, entrez le nombre d'unités que vous souhaitez produire.</li>
                <li>Cliquez sur le bouton <strong>Calculer</strong>.</li>
                <li>Les ingrédients nécessaires, ainsi que leurs quantités respectives, s'afficheront immédiatement sous la section calculateur.</li>
            </ol>
            <p class="mt-2 text-blue-700">
                Ce calcul est basé sur les recettes optimales fournies pour chaque produit. Assurez-vous de saisir une quantité
                valide pour obtenir des résultats précis.
            </p>
        </div>

        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Livre de Recettes</h1>
                <p class="mt-2 text-gray-600">{{ $secteur }} - {{ $nom }}</p>
            </div>
            @if(auth()->user()->secteur != 'administration')
            <a href="{{ route('recipes.instructions') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                Recettes Detaillees
            </a>
            @endif

            @if(auth()->user()->secteur == 'administration')
            <a href="{{ route('recipes.admin') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                Recettes Avancées
            </a>
            <a href="{{ route('recettes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                Ajouter une recette
            </a>


        @endif
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
            <p class="text-yellow-700">
                Vous avez pour chaque produit disponible la recette optimale proposée par l'administration,
                certains employés et des sources fiables.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($produits as $produit)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ $produit->nom }}</h2>
                        <button onclick="confirmDelete({{ $produit->code_produit }})"
                                class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">
                                Recette pour {{ $produit->matiereRecommandee->first()?->quantitep ?? 0 }} unités
                            </h3>
                            <ul class="mt-2 space-y-2">
                                @foreach($produit->matiereRecommandee as $recette)
                                <li class="flex justify-between text-sm">
                                    <span class="text-gray-700">{{ $recette->matiere->nom }}</span>
                                    <span class="text-gray-600">
                                        {{ $recette->quantite }} {{ $recette->unite }}
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="pt-4 border-t">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Calculateur</h3>
                            <div class="flex gap-2">
                                <input type="number"
                                       class="quantity-input border rounded px-2 py-1 w-24"
                                       data-produit="{{ $produit->code_produit }}"
                                       placeholder="Quantité">
                                <button onclick="calculateIngredients(this)"
                                        class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">
                                    Calculer
                                </button>
                            </div>
                            <div class="mt-2 ingredients-result hidden"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>

function calculateIngredients(button) {
    const card = button.closest('.bg-white');
    const input = card.querySelector('.quantity-input');
    const resultDiv = card.querySelector('.ingredients-result');

    fetch('/recettes/calculate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            produit_id: input.dataset.produit,
            quantite_cible: input.value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }

        resultDiv.innerHTML = `
            <h4 class="text-sm font-medium text-gray-500 mt-2">Ingrédients nécessaires:</h4>
            <ul class="mt-1 space-y-1">
                ${data.ingredients.map(ing => `
                    <li class="text-sm flex justify-between">
                        <span class="text-gray-700">${ing.nom}</span>
                        <span class="text-gray-600">${ing.quantite.toFixed(2)} ${ing.unite}</span>
                    </li>
                `).join('')}
            </ul>
        `;
        resultDiv.classList.remove('hidden');
    })
    .catch(error => {
        alert('Erreur: ' + error.message);
    });
}
function confirmDelete(produitId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette recette ?')) {
        fetch(`/recettes/${produitId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Erreur lors de la suppression');
        });
    }
}
</script>
@endsection
