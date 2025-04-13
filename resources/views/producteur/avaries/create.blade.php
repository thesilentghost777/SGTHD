@extends('pages.producteur.pdefault')

@section('page-content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Enregistrer une Avarie de Production</h1>
                <p class="mt-2 text-gray-600">Indiquez les informations sur le produit avarié et les matières utilisées</p>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erreurs de validation</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @if(session('error'))
                                        <li>{{ session('error') }}</li>
                                    @endif
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ route('producteur.avaries.store') }}" method="POST" id="avarieForm" class="divide-y divide-gray-200">
                    @csrf

                    <!-- Product Section -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="produit">
                                    Produit Avarié
                                </label>
                                <select name="produit" id="produit"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md"
                                    required>
                                    <option value="">Sélectionnez un produit</option>
                                    @foreach($produits as $produit)
                                        <option value="{{ $produit->code_produit }}">
                                            {{ $produit->nom }} - {{ $produit->prix }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700" for="quantite_produit">
                                    Quantité avariée
                                </label>
                                <input type="number" step="1" min="1" name="quantite_produit" id="quantite_produit"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required value="1">
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="flex items-center">
                                <input id="avarie_reutilisee" name="avarie_reutilisee" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="avarie_reutilisee" class="ml-2 block text-sm text-gray-900">
                                    Cette avarie sera réutilisée pour une nouvelle production (ne pas déduire les matières)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Materials Section -->
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Matières Premières Utilisées</h3>
                            <p class="mt-1 text-sm text-gray-500">Ajoutez les matières premières qui ont été utilisées pour cette production avariée</p>
                        </div>

                        <div id="matieres-container" class="space-y-4">
                            <div class="matiere-item p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Matière Première</label>
                                        <select name="matieres[0][matiere_id]"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md"
                                            required>
                                            <option value="">Sélectionner</option>
                                            @foreach($matieres as $matiere)
                                                <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Quantité</label>
                                        <input type="number" step="0.001" name="matieres[0][quantite]"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Unité</label>
                                        <select name="matieres[0][unite]"
                                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md"
                                            required>
                                            <option value="">Sélectionner</option>
                                            <option value="g">Gramme (g)</option>
                                            <option value="kg">Kilogramme (kg)</option>
                                            <option value="ml">Millilitre (ml)</option>
                                            <option value="cl">Centilitre (cl)</option>
                                            <option value="dl">Décilitre (dl)</option>
                                            <option value="l">Litre (l)</option>
                                            <option value="cc">Cuillère à café (cc)</option>
                                            <option value="cs">Cuillère à soupe (cs)</option>
                                            <option value="pincee">Pincée</option>
                                            <option value="unite">Unité</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="button" onclick="ajouterMatiere()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Ajouter une matière première
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="px-6 py-4 bg-gray-50">
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Enregistrer l'avarie
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let matiereCount = 1;

function ajouterMatiere() {
    const container = document.getElementById('matieres-container');
    const template = document.querySelector('.matiere-item').cloneNode(true);

    // Reset all input values
    template.querySelectorAll('input, select').forEach(input => {
        const name = input.name.replace('[0]', `[\${matiereCount}]s`);
        input.name = name;
        input.value = '';
    });

    // Add remove button for additional items
    const removeButton = document.createElement('button');
    removeButton.type = 'button';
    removeButton.className = 'mt-2 inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500';
    removeButton.innerHTML =`
        <svg class="-ml-1 mr-1 h-4 w-4 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
        Supprimer
    `;
    removeButton.onclick = function() {
        template.remove();
    };
    template.appendChild(removeButton);

    container.appendChild(template);
    matiereCount++;
}
</script>
@endsection