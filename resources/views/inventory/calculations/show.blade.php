
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('inventory.groups.show', $group) }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i> Retour au Groupe
        </a>
        <h1 class="text-2xl font-bold text-gray-800">{{ $calculation->title }}</h1>
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

    @if(session('info'))
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
            <p>{{ session('info') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 md:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Informations de la Session</h2>
                @if($calculation->status === 'open')
                    <form action="{{ route('inventory.calculations.close', $calculation) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir fermer cette session de calcul ?');">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded inline-flex items-center">
                            <i class="fas fa-lock mr-2"></i> Fermer la Session
                        </button>
                    </form>
                @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">Session fermée</span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">Groupe:</p>
                    <p class="text-gray-800 font-medium">{{ $group->name }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Date:</p>
                    <p class="text-gray-800 font-medium">{{ $calculation->date->format('d/m/Y') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Statut:</p>
                    <p class="text-gray-800 font-medium">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $calculation->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $calculation->status === 'open' ? 'Ouvert' : 'Fermé' }}
                        </span>
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Créé le:</p>
                    <p class="text-gray-800 font-medium">{{ $calculation->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Résumé</h2>

            <div class="mb-4">
                <p class="text-sm text-gray-600">Nombre d'articles:</p>
                <p class="text-2xl font-bold text-gray-800">{{ $missingItems->count() }}</p>
            </div>

            <div class="mb-4">
                <p class="text-sm text-gray-600">Montant total des manquants:</p>
                <p class="text-2xl font-bold {{ $calculation->total_amount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                    {{ number_format($calculation->total_amount, 0, ',', ' ') }} XAF
                </p>
            </div>

            <div>
                <button onclick="printReport()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded inline-flex items-center justify-center">
                    <i class="fas fa-print mr-2"></i> Imprimer le Rapport
                </button>
            </div>
        </div>
    </div>

    <div id="report-content">
        <!-- Formulaire d'ajout d'articles manquants -->
        @if($calculation->status === 'open')
            <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Ajouter un Article Manquant</h2>

                @if($products->isEmpty())
                    <div class="text-center p-4 bg-yellow-50 rounded-md">
                        <p class="text-yellow-700">Tous les produits ont déjà été ajoutés à cette session.</p>
                    </div>
                @else
                    <form action="{{ route('inventory.calculations.add-item', $calculation) }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Produit</label>
                                <select name="product_id" id="product_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                    <option value="">Sélectionner un produit</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ number_format($product->price, 0, ',', ' ') }} XAF)</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="expected_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantité Attendue</label>
                                <input type="number" name="expected_quantity" id="expected_quantity" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="actual_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantité Réelle</label>
                                <input type="number" name="actual_quantity" id="actual_quantity" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                                Ajouter l'Article
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @endif

        <!-- Liste des articles manquants -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Articles Manquants</h2>
            </div>

            @if($missingItems->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-gray-500">Aucun article manquant n'a encore été enregistré.</p>
                    @if($calculation->status === 'open')
                        <p class="text-gray-500 mt-2">Utilisez le formulaire ci-dessus pour ajouter des articles.</p>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Produit</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Type</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Prix Unitaire</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Qté Attendue</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Qté Réelle</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Qté Manquante</th>
                                <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Montant</th>
                                @if($calculation->status === 'open')
                                    <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($missingItems as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $item->product->name }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $item->product->type }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ number_format($item->product->price, 0, ',', ' ') }} XAF</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $item->expected_quantity }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $item->actual_quantity }}</td>
                                    <td class="py-3 px-4 text-sm font-medium {{ $item->missing_quantity > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ $item->missing_quantity }}
                                    </td>
                                    <td class="py-3 px-4 text-sm font-medium {{ $item->amount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ number_format($item->amount, 0, ',', ' ') }} XAF
                                    </td>
                                    @if($calculation->status === 'open')
                                        <td class="py-3 px-4 text-sm text-gray-700">
                                            <div class="flex space-x-2 no-print">
                                                <button type="button" onclick="openEditModal({{ $item->id }}, {{ $item->expected_quantity }}, {{ $item->actual_quantity }})" class="text-amber-600 hover:text-amber-800">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('inventory.calculations.delete-item', $item) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 font-bold">
                                <td colspan="{{ $calculation->status === 'open' ? '6' : '5' }}" class="py-3 px-4 text-right text-sm text-gray-800">Total:</td>
                                <td class="py-3 px-4 text-sm font-bold {{ $calculation->total_amount > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                    {{ number_format($calculation->total_amount, 0, ',', ' ') }} XAF
                                </td>
                                @if($calculation->status === 'open')
                                    <td></td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal pour modifier un article -->
<div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center z-50 no-print">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Modifier l'Article</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="edit_expected_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantité Attendue</label>
                <input type="number" name="expected_quantity" id="edit_expected_quantity" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
            </div>

            <div class="mb-6">
                <label for="edit_actual_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantité Réelle</label>
                <input type="number" name="actual_quantity" id="edit_actual_quantity" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeEditModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded mr-2">
                    Annuler
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(itemId, expectedQuantity, actualQuantity) {
        document.getElementById('editForm').action = '/inventory/calculations/items/' + itemId;
        document.getElementById('edit_expected_quantity').value = expectedQuantity;
        document.getElementById('edit_actual_quantity').value = actualQuantity;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function printReport() {
        const printContents = document.getElementById('report-content').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = `
            <style>
                @media print {
                    body {
                        font-family: 'Helvetica', 'Arial', sans-serif;
                        color: #333;
                    }
                    @page {
                        size: A4;
                        margin: 1cm;
                    }
                    button, .no-print {
                        display: none !important;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th, td {
                        padding: 8px;
                        text-align: left;
                        border-bottom: 1px solid #ddd;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                }
            </style>
            <div class="print-container">
                <h1 style="text-align: center; margin-bottom: 20px;">{{ $calculation->title }}</h1>
                <p style="text-align: center; margin-bottom: 20px;">Date: {{ $calculation->date->format('d/m/Y') }}</p>
                <p style="text-align: center; margin-bottom: 20px;">Groupe: {{ $group->name }}</p>
                ${printContents}
            </div>
        `;

        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
@endpush

@endsection
