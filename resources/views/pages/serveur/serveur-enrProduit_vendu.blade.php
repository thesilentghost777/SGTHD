@extends('pages.serveur.serveur_default')

@section('page-content')
    <div class="bg-gradient-to-r from-blue-50 via-green-50 to-blue-100 min-h-screen flex flex-col">
        <div class="container mx-auto px-4 py-8 flex-grow">
            <h1 class="text-center text-3xl font-bold text-blue-800 mb-8">Enregistrer une Vente</h1>

            <!-- Error Messages -->
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc ml-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('saveProduit_vendu') }}" method="POST" class="space-y-6 bg-white p-8 rounded-lg shadow-lg border border-gray-300">
                @csrf
                @method('POST')

                <!-- Produit -->
                <div>
                    <label for="produit" class="block text-sm font-semibold text-gray-700 mb-2">Produit :</label>
                    <select name="produit" id="produit" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un produit</option>
                        @foreach($produitR as $all_product)
                            <option value="{{ $all_product->code_produit }}">{{ $all_product->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantité -->
                <div>
                    <label for="quantite" class="block text-sm font-semibold text-gray-700 mb-2">Quantité :</label>
                    <input type="number" name="quantite" id="quantite" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Prix -->
                <div>
                    <label for="prix" class="block text-sm font-semibold text-gray-700 mb-2">Prix :</label>
                    <input type="number" name="prix" id="prix" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Type de l'Opération -->
                <div>
                    <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Type de l'Opération :</label>
                    <select name="type" id="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Sélectionner une opération</option>
                        <option value="Vente">Vente</option>
                        <option value="Invendu">Produit Invendu</option>
                    </select>
                </div>

                <!-- Monnaie Reçue -->
                <div>
                    <label for="monnaie" class="block text-sm font-semibold text-gray-700 mb-2">Monnaie Reçue :</label>
                    <select name="monnaie" id="monnaie" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner une monnaie</option>
                        <option value="Virement">Virement</option>
                        <option value="Espèce">Espèce</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-green-600 text-white font-semibold py-3 rounded-lg shadow-lg transition duration-200 transform hover:-translate-y-1">
                    Enregistrer la vente
                </button>
            </form>

            <!-- Footer -->
            <footer class="text-center text-sm text-gray-500 mt-8">
                Date : {{ $heure_actuelle->format('d:m:Y') }} | Heure : {{ $heure_actuelle->format('H:i:s') }}
            </footer>
        </div>

        <!-- Product Table Section -->
        <div class="container mx-auto px-4 mt-12">
            <input type="text" id="searchBar" placeholder="Rechercher un produit..." onkeyup="filterTable()"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 mb-6">

            <h2 class="text-center text-2xl font-bold text-blue-800 mb-6">Liste des produits Vendus</h2>

            <div class="overflow-x-auto bg-white shadow-lg rounded-lg border border-gray-300">
                <table class="table-auto w-full border-collapse">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-4 py-3 border-b border-gray-300 text-left">Nom du Produit</th>
                            <th class="px-4 py-3 border-b border-gray-300 text-left">Quantité</th>
                            <th class="px-4 py-3 border-b border-gray-300 text-left">Prix</th>
                            <th class="px-4 py-3 border-b border-gray-300 text-left">Date de la vente</th>
                            <th class="px-4 py-3 border-b border-gray-300 text-left">Monnaie Reçue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proV as $produit)
                            <tr class="hover:bg-green-50">
                                <td class="px-4 py-3 border-b border-gray-300">{{ $produit->produit }}</td>
                                <td class="px-4 py-3 border-b border-gray-300">{{ $produit->quantite }}</td>
                                <td class="px-4 py-3 border-b border-gray-300">{{ $produit->prix }}</td>
                                <td class="px-4 py-3 border-b border-gray-300">{{ $produit->date_vente }}</td>
                                <td class="px-4 py-3 border-b border-gray-300">{{ $produit->monnaie }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>

    <script>
        function updatePrice(prices) {
            const selectProduit = document.querySelector('select[name="produit"]');
            const inputPrix = document.querySelector('input[name="prix"]');

            selectProduit.addEventListener('change', function () {
                const selectedCode = this.value;
                inputPrix.value = prices[selectedCode] || '';
            });
        }

        const productPrices = @json($produitR->pluck('prix', 'code_produit'));
        document.addEventListener("DOMContentLoaded", () => updatePrice(productPrices));
    </script>
@endsection
