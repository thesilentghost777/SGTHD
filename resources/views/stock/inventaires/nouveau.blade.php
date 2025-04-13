@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('inventaires.index') }}" class="mr-4 text-blue-600 hover:text-blue-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold">Nouvel inventaire</h1>
    </div>

    <div class="bg-white shadow-md rounded-md p-6 mb-6">
        <p class="mb-4 text-gray-600">
            Pour effectuer un inventaire, entrez la quantité physique pour chaque produit.
            Le système calculera automatiquement les écarts par rapport au stock théorique.
        </p>

        <form id="inventaireForm">
            @csrf
            <div class="mb-4">
                <h3 class="font-medium text-lg mb-2">Liste des produits (boissons)</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock théorique</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock physique réel</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($produits as $produit)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $produit->nom }}</div>
                                        <div class="text-xs text-gray-500">{{ $produit->reference }}</div>
                                        <input type="hidden" name="inventaire[{{ $loop->index }}][produit_id]" value="{{ $produit->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $produit->quantite }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($produit->prix_unitaire, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number"
                                            name="inventaire[{{ $loop->index }}][quantite_physique]"
                                            min="0"
                                            class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" id="submitInventaire" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Calculer les manquants
                </button>
            </div>
        </form>
    </div>

    <!-- Résultats de l'inventaire -->
    <div id="resultatInventaire" class="hidden bg-white shadow-md rounded-md p-6">
        <h2 class="text-xl font-semibold mb-4">Résultat de l'inventaire</h2>

        <div id="messageResultat" class="mb-4 p-4 rounded-md"></div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manquant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur</th>
                    </tr>
                </thead>
                <tbody id="resultatsBody" class="bg-white divide-y divide-gray-200">
                    <!-- Résultats injectés par JavaScript -->
                </tbody>
                <tfoot id="resultatsFooter" class="bg-gray-50">
                    <tr>
                        <td class="px-6 py-4 font-medium">Total</td>
                        <td class="px-6 py-4"></td>
                        <td class="px-6 py-4 font-medium" id="totalValeur">0 FCFA</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <a href="{{ route('inventaires.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Terminer
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('inventaireForm');
    const resultatDiv = document.getElementById('resultatInventaire');
    const messageResultat = document.getElementById('messageResultat');
    const resultatsBody = document.getElementById('resultatsBody');
    const totalValeur = document.getElementById('totalValeur');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Récupérer les données du formulaire
        const formData = new FormData(form);

        // Envoyer les données au serveur
        fetch('{{ route("inventaires.calculer") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Afficher les résultats
            resultatsBody.innerHTML = '';
            let totalMontant = 0;

            if (data.length === 0) {
                messageResultat.classList.add('bg-green-100', 'text-green-800');
                messageResultat.textContent = 'Aucun manquant détecté. Tous les stocks sont corrects.';
            } else {
                messageResultat.classList.add('bg-red-100', 'text-red-800');
                messageResultat.textContent = 'Des manquants ont été détectés et enregistrés.';

                // Afficher chaque ligne de résultat
                data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${item.produit}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">${item.manquant}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">${(item.valeur).toLocaleString('fr-FR')} FCFA</td>
                    `;
                    resultatsBody.appendChild(row);
                    totalMontant += parseFloat(item.valeur);
                });

                // Afficher le total
                totalValeur.textContent = totalMontant.toLocaleString('fr-FR') + ' FCFA';
            }

            // Afficher la section de résultats
            resultatDiv.classList.remove('hidden');

            // Faire défiler la page vers les résultats
            resultatDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors du traitement de l\'inventaire.');
        });
    });
});
</script>
@endsection
