@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Modifier la Commande</h1>

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

        <form action="{{ route('commande.update', $commande->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="libelle" class="block text-sm font-medium text-gray-700">Libellé</label>
                    <input type="text" name="libelle" id="libelle"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('libelle', $commande->libelle) }}" required maxlength="50">
                </div>

                <div>
                    <label for="produit" class="block text-sm font-medium text-gray-700">Produit</label>
                    <select name="produit" id="produit"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                        <option value="">Sélectionner un produit</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->code_produit }}"
                                {{ (old('produit', $commande->produit) == $produit->code_produit) ? 'selected' : '' }}>
                                {{ $produit->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                    <input type="number" name="quantite" id="quantite" min="1"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('quantite', $commande->quantite) }}" required>
                </div>

                <div>
                    <label for="date_commande" class="block text-sm font-medium text-gray-700">Date de commande</label>
                    <input type="datetime-local" name="date_commande" id="date_commande"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="{{ old('date_commande', $commande->date_commande->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div>
                    <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="categorie" id="categorie"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                        <option value="">Sélectionner une catégorie</option>
                        <option value="boulangerie" {{ old('categorie', $commande->categorie) == 'boulangerie' ? 'selected' : '' }}>boulangerie</option>
                        <option value="patisserie" {{ old('categorie', $commande->categorie) == 'patisserie' ? 'selected' : '' }}>patisserie</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('chef.commandes.create') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
