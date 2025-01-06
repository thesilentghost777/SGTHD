@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Ajouter une Recette</h1>
            <p class="mt-2 text-gray-600">{{ $secteur }} - {{ $nom }}</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('recettes.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Produit
                        </label>
                        <select name="produit" required class="w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($produits as $produit)
                                <option value="{{ $produit->code_produit }}">{{ $produit->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Quantité de produit
                        </label>
                        <input type="number" name="quantitep" required 
                               class="w-full border-gray-300 rounded-md shadow-sm"
                               placeholder="Quantité de base pour la recette">
                    </div>
                </div>

                <div id="matieres-container">
                    <div class="matiere-item mb-4">
                        <div class="grid grid-cols-4 gap-4">
                            <select name="matieres[0][matiere_id]" required 
                                    class="border-gray-300 rounded-md shadow-sm">
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}">{{ $matiere->nom }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="matieres[0][quantite]" 
                                   placeholder="Quantité" required 
                                   class="border-gray-300 rounded-md shadow-sm">
                            <select name="matieres[0][unite]" required 
                                    class="border-gray-300 rounded-md shadow-sm">
                                @foreach($unites as $unite)
                                    <option value="{{ $unite->value }}">{{ $unite->value }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="supprimerMatiere(this)" 
                                    class="text-red-500 hover:text-red-700">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="ajouterMatiere()" 
                        class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    Ajouter une matière
                </button>

                <button type="submit" 
                        class="mt-6 w-full px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    Enregistrer la recette
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let matiereCount = 1;

function ajouterMatiere() {
    const container = document.getElementById('matieres-container');
    const template = document.querySelector('.matiere-item').cloneNode(true);
    
    template.querySelectorAll('select, input').forEach(input => {
        input.name = input.name.replace('[0]', `[${matiereCount}]`);
        input.value = '';
    });
    
    container.appendChild(template);
    matiereCount++;
}

function supprimerMatiere(button) {
    const item = button.closest('.matiere-item');
    if (document.querySelectorAll('.matiere-item').length > 1) {
        item.remove();
    }
}
</script>
@endsection