@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Facture d'assignation</h1>
                        <p class="text-gray-600">Référence: ASS-{{ $assignation->id }}-{{ date('Ymd') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">Date: {{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-medium text-gray-800 mb-2">Gestionnaire</h2>
                        <p class="text-sm">Boulangerie-Pâtisserie</p>
                        <p class="text-sm">TH MARKET</p>
                        <p class="text-sm">Yaounde obili</p>
                        <p class="text-sm">Cameroun</p>
                    </div>
                    <div>
                        <h2 class="text-lg font-medium text-gray-800 mb-2">Producteur</h2>
                        <p class="font-medium">{{ $assignation->producteur->name }}</p>
                        <p class="text-sm">{{ ucfirst($assignation->producteur->role) }}</p>
                        <p class="text-sm">{{ $assignation->producteur->email }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-b border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $assignation->matiere->nom }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                {{ round($assignation->quantite_assignee,1) }} {{ $assignation->unite_assignee }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                {{ round((float)number_format($assignation->matiere->prix_par_unite_minimale, 4),1) }} XAF / {{ $assignation->matiere->unite_minimale }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                {{ number_format($prixTotal) }} XAF
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">Total</td>
                            <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-gray-900 text-right">{{ number_format($prixTotal) }} XAF</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-medium text-gray-800 mb-2">Informations complémentaires</h2>
                    <p class="text-sm text-gray-600">
                        Date d'assignation: {{ $assignation->created_at->format('d/m/Y') }}<br>
                        @if($assignation->date_limite_utilisation)
                            Date limite d'utilisation: {{ $assignation->date_limite_utilisation->format('d/m/Y') }}
                        @else
                            Aucune date limite d'utilisation définie.
                        @endif
                    </p>
                </div>

                <div class="mt-8 text-center text-sm text-gray-600">
                    <p>Merci de votre confiance.</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('assignations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Retour
            </a>
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                Imprimer
            </button>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .container, .container * {
            visibility: visible;
        }
        .container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        button, a {
            display: none !important;
        }
    }
</style>
@endsection
