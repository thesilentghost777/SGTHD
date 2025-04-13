@extends('employee.default3')

@section('page-content')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Pointeur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen">
    <!-- En-tête -->
    <header class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl font-bold text-white">Tableau de bord Pointeur</h1>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Messages de notification -->
        @if (session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Section Enregistrement des produits -->
            <div class="bg-white rounded-lg shadow-xl p-6 border border-blue-200">
                <h2 class="text-xl font-semibold text-blue-600 mb-4">Enregistrer un produit recu</h2>
                <form action="{{ route('pointeur.produits.enregistrer') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="produit_id" class="block text-sm font-medium text-gray-700">Produit</label>
                        <select name="produit_id" id="produit_id" required class="mt-1 block w-full rounded-md bg-white border border-gray-300 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            @foreach(App\Models\Produit_fixes::all() as $produit)
                                <option value="{{ $produit->code_produit }}">{{ $produit->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                        <input type="number" name="quantite" id="quantite" required min="1" class="mt-1 block w-full rounded-md bg-white border border-gray-300 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="producteur_id" class="block text-sm font-medium text-gray-700">Producteur</label>
                        <select name="producteur_id" id="producteur_id" required class="mt-1 block w-full rounded-md bg-white border border-gray-300 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            @foreach(App\Models\User::where('secteur', 'production')->get() as $producteur)
                                <option value="{{ $producteur->id }}">{{ $producteur->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="remarques" class="block text-sm font-medium text-gray-700">Remarques</label>
                        <textarea name="remarques" id="remarques" rows="3" class="mt-1 block w-full rounded-md bg-white border border-gray-300 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-200">
                        Enregistrer le produit
                    </button>
                </form>
            </div>

            <!-- Section Commandes en attente -->
            <div class="bg-blue-600 rounded-lg shadow-xl p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Commandes en attente de validation</h2>
                <div class="space-y-4">
                    @forelse($commandesEnAttente as $commande)
                        <div class="bg-white rounded-lg p-4 shadow-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-800">{{ $commande->libelle }}</h3>
                                    <p class="text-gray-600">Produit : {{ $commande->produit_fixe->nom }}</p>
                                    <p class="text-gray-600">Quantité : {{ $commande->quantite }}</p>
                                    <p class="text-gray-600">Date : {{ $commande->date_commande }}</p>
                                    <p class="text-gray-600">Catégorie : {{ $commande->categorie }}</p>
                                </div>
                                <form action="{{ route('pointeur.commandes.valider', $commande) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-200">
                                        Valider
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-white">Aucune commande en attente</p>
                    @endforelse
                </div>
            </div>

            <!-- Section Derniers produits reçus -->
            <div class="md:col-span-2 bg-white rounded-lg shadow-xl p-6 border border-blue-200">
                <h2 class="text-xl font-semibold text-blue-600 mb-4">Derniers produits reçus</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producteur</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($produitsRecus as $produitRecu)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $produitRecu->date_reception }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $produitRecu->produit->nom }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $produitRecu->quantite }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $produitRecu->producteur->name }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
@endsection
