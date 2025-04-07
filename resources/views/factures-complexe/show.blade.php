@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Facture #{{ $facture->reference }}</h1>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                Imprimer
            </button>
            <a href="{{ route('factures-complexe.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold mb-2">Informations de la facture</h2>
                <div class="space-y-2">
                    <div><span class="font-medium">Référence:</span> {{ $facture->reference }}</div>
                    <div><span class="font-medium">Date de création:</span> {{ $facture->date_creation->format('d/m/Y') }}</div>
                    <div>
                        <span class="font-medium">Statut:</span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $facture->statut === 'validee' ? 'bg-green-100 text-green-800' :
                               ($facture->statut === 'annulee' ? 'bg-red-100 text-red-800' :
                               'bg-yellow-100 text-yellow-800') }}">
                            {{ $facture->statut === 'en_attente' ? 'En attente' :
                               ($facture->statut === 'validee' ? 'Validée' : 'Annulée') }}
                        </span>
                    </div>
                    @if($facture->date_validation)
                    <div><span class="font-medium">Date de validation:</span> {{ $facture->date_validation->format('d/m/Y') }}</div>
                    @endif
                </div>
            </div>
            <div>
                <h2 class="text-lg font-semibold mb-2">Producteur</h2>
                <div class="space-y-2">
                    <div><span class="font-medium">Nom:</span> {{ $facture->producteur->name }}</div>
                    <div><span class="font-medium">Email:</span> {{ $facture->producteur->email }}</div>
                    <div><span class="font-medium">Rôle:</span> {{ $facture->producteur->role }}</div>
                </div>
            </div>
        </div>

        @if($facture->notes)
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Notes</h2>
            <p class="text-gray-700">{{ $facture->notes }}</p>
        </div>
        @endif

        <div>
            <h2 class="text-lg font-semibold mb-2">Détails de la facture</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Matière
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantité
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantité unitaire
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prix unitaire
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Montant
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($facture->details as $detail)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $detail->matiere->nom }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm text-gray-900">{{ number_format($detail->quantite, 3, ',', ' ') }} {{ $detail->unite }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm text-gray-900">
                                    {{ number_format(round($detail->quantite / $detail->matiere->quantite_par_unite), 0, ',', ' ') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm text-gray-900">{{ number_format($detail->prix_unitaire, 2, ',', ' ') }} FCFA</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-medium text-gray-900">{{ number_format($detail->montant, 2, ',', ' ') }} FCFA</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-right font-bold">
                                Total:
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-bold">
                                {{ number_format($facture->montant_total, 2, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if($facture->statut === 'en_attente')
    <div class="flex justify-end gap-4">
        <form action="{{ route('factures-complexe.validate', $facture->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded" onclick="return confirm('Êtes-vous sûr de vouloir valider cette facture?')">
                Valider la facture
            </button>
        </form>
        <a href="{{ route('factures-complexe.edit', $facture->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Modifier la facture
        </a>
        <form action="{{ route('factures-complexe.destroy', $facture->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture?')">
                Supprimer la facture
            </button>
        </form>
    </div>
    @endif
</div>

<style>
@media print {
    body {
        margin: 0;
        padding: 0;
        font-size: 12pt;
    }

    .container {
        width: 100%;
        max-width: none;
        padding: 10mm;
    }

    button, a {
        display: none !important;
    }

    .shadow-md {
        box-shadow: none !important;
    }

    .rounded-lg {
        border-radius: 0 !important;
    }

    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    th, td {
        border: 1px solid #ddd !important;
    }

    thead {
        display: table-header-group !important;
    }

    tfoot {
        display: table-footer-group !important;
    }
}
</style>
@endsection
