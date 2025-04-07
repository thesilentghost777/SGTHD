
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('inventory.groups.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i> Retour aux Groupes
        </a>
        <h1 class="text-2xl font-bold text-gray-800">{{ $group->name }}</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Informations du Groupe</h2>
                <a href="{{ route('inventory.groups.edit', $group) }}" class="text-amber-600 hover:text-amber-800">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600">Description:</p>
                <p class="text-gray-800">{{ $group->description ?? 'Aucune description' }}</p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600">Créé le:</p>
                <p class="text-gray-800">{{ $group->created_at->format('d/m/Y à H:i') }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600">Dernière mise à jour:</p>
                <p class="text-gray-800">{{ $group->updated_at->format('d/m/Y à H:i') }}</p>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Statistiques</h2>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-xl font-bold text-blue-800">{{ $products->count() }}</p>
                    <p class="text-sm text-blue-600">Produits dans ce groupe</p>
                </div>

                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-xl font-bold text-green-800">{{ $calculations->count() }}</p>
                    <p class="text-sm text-green-600">Sessions de calcul</p>
                </div>

                <div class="bg-amber-50 rounded-lg p-4">
                    <p class="text-xl font-bold text-amber-800">{{ $calculations->where('status', 'open')->count() }}</p>
                    <p class="text-sm text-amber-600">Sessions ouvertes</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xl font-bold text-gray-800">{{ $calculations->where('status', 'closed')->count() }}</p>
                    <p class="text-sm text-gray-600">Sessions fermées</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Produits du groupe -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <div class="flex justify-between items-center p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-800">Produits du Groupe</h2>
            <a href="{{ route('inventory.products.create', $group) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Ajouter un Produit
            </a>
        </div>

        @if($products->isEmpty())
            <div class="p-8 text-center">
                <p class="text-gray-500 mb-4">Aucun produit n'a encore été ajouté à ce groupe.</p>
                <a href="{{ route('inventory.products.create', $group) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Ajouter votre premier produit
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Nom</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Prix</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-700">{{ $product->name }}</td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ $product->type }}</td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ number_format($product->price, 0, ',', ' ') }} XAF</td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('inventory.products.edit', $product) }}" class="text-amber-600 hover:text-amber-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('inventory.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Sessions de calcul -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-800">Sessions de Calcul de Manquants</h2>
            <a href="{{ route('inventory.calculations.create', $group) }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded inline-flex items-center">
                <i class="fas fa-calculator mr-2"></i> Nouvelle Session
            </a>
        </div>

        @if($calculations->isEmpty())
            <div class="p-8 text-center">
                <p class="text-gray-500 mb-4">Aucune session de calcul n'a encore été créée pour ce groupe.</p>
                <a href="{{ route('inventory.calculations.create', $group) }}" class="text-green-600 hover:text-green-800 font-medium">
                    Créer votre première session de calcul
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Titre</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Montant Total</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($calculations as $calculation)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <a href="{{ route('inventory.calculations.show', $calculation) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $calculation->title }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ $calculation->date->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $calculation->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $calculation->status === 'open' ? 'Ouvert' : 'Fermé' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ number_format($calculation->total_amount, 0, ',', ' ') }} XAF</td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('inventory.calculations.show', $calculation) }}" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($calculation->status === 'open')
                                            <form action="{{ route('inventory.calculations.close', $calculation) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir fermer cette session de calcul ?');">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
