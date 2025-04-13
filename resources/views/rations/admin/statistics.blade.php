@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Statistiques des Rations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Résumé -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Résumé des rations</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <div class="text-indigo-700 text-sm font-semibold mb-1">Rations prises aujourd'hui</div>
                        <div class="flex items-end">
                            <div class="text-2xl font-bold">{{ number_format($rationsJour, 0, ',', ' ') }}</div>
                            <div class="text-sm ml-1 mb-1">FCFA</div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-green-700 text-sm font-semibold mb-1">Rations prises ce mois</div>
                        <div class="flex items-end">
                            <div class="text-2xl font-bold">{{ number_format($rationsMois, 0, ',', ' ') }}</div>
                            <div class="text-sm ml-1 mb-1">FCFA</div>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-lg p-4">
                        <div class="text-red-700 text-sm font-semibold mb-1">Rations non réclamées aujourd'hui</div>
                        <div class="flex items-end">
                            <div class="text-2xl font-bold">{{ number_format($rationPerdue, 0, ',', ' ') }}</div>
                            <div class="text-sm ml-1 mb-1">FCFA</div>
                        </div>
                        <div class="text-xs text-red-600 mt-1">(Économie potentielle)</div>
                    </div>
                </div>
            </div>

            <!-- Statistiques générales -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques générales</h3>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-gray-500 text-sm">Montant total des rations</div>
                        <div class="text-xl font-semibold">{{ number_format($statistiques['total_rations'], 0, ',', ' ') }} FCFA</div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-sm">Nombre d'employés avec ration</div>
                        <div class="text-xl font-semibold">{{ $statistiques['nb_employes_avec_ration'] }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-sm">Ration moyenne</div>
                        <div class="text-xl font-semibold">{{ number_format($statistiques['ration_moyenne'], 0, ',', ' ') }} FCFA</div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-sm">Ration minimum</div>
                        <div class="text-xl font-semibold">{{ number_format($statistiques['ration_min'], 0, ',', ' ') }} FCFA</div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-sm">Ration maximum</div>
                        <div class="text-xl font-semibold">{{ number_format($statistiques['ration_max'], 0, ',', ' ') }} FCFA</div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-sm">Rations personnalisées</div>
                        <div class="text-xl font-semibold">{{ $statistiques['nb_rations_personnalisees'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Réclamations journalières du mois -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Réclamations de rations ce mois</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de rations</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($statistiquesJournalieres as $stat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($stat->date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stat->nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($stat->montant_total, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Employés qui prennent rarement leur ration -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Employés qui prennent peu leur ration ce mois</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de rations réclamées</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($employesRarement as $employe)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $employe->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $employe->ration_claims_count }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection
