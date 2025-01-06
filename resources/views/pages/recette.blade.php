@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Livre de Recettes</h1>
                <p class="mt-2 text-gray-600">{{ $secteur }} - {{ $nom }}</p>
            </div>
            <a href="{{ route('recettes.create') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                Ajouter une recette
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($produits as $produit)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $produit->nom }}</h2>

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
</script>
@endsection
