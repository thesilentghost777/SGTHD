@extends('rapports.layout.rapport')

@section('content')
    <x-slot name="reportTitle">
        Rapport des Salaires
    </x-slot>

    <x-slot name="description">
        Ce rapport présente la liste des salaires des employés pour le mois de {{ $currentMonthName }}.
    </x-slot>

    <div class="space-y-8">
        <!-- Résumé -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé des salaires</h3>

            <p class="text-gray-700 leading-relaxed">
                Au cours du mois de {{ $currentMonthName }}, un total de <strong>{{ number_format($totalSalaires, 0, ',', ' ') }} XAF</strong>
                a été distribué en salaires à {{ $nombreEmployes }} employé(s). Le salaire moyen s'élève à
                <strong>{{ number_format($salaireMoyen, 0, ',', ' ') }} XAF</strong> par employé.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="bg-gradient-to-br from-blue-50 to-teal-50 rounded-lg p-4 border border-blue-100">
                    <h4 class="text-blue-800 font-medium mb-2">Total des Salaires</h4>
                    <p class="text-2xl font-bold text-blue-700">{{ number_format($totalSalaires, 0, ',', ' ') }} XAF</p>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-lg p-4 border border-green-100">
                    <h4 class="text-green-800 font-medium mb-2">Nombre d'Employés</h4>
                    <p class="text-2xl font-bold text-green-700">{{ $nombreEmployes }}</p>
                </div>

                <div class="bg-gradient-to-br from-teal-50 to-blue-50 rounded-lg p-4 border border-teal-100">
                    <h4 class="text-teal-800 font-medium mb-2">Salaire Moyen</h4>
                    <p class="text-2xl font-bold text-teal-700">{{ number_format($salaireMoyen, 0, ',', ' ') }} XAF</p>
                </div>
            </div>
        </section>

        <!-- Statut des paiements -->
        <section class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Statut des paiements</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">
                                Validés
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold inline-block text-green-600">
                                {{ $pourcentageValides }}%
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-green-200">
                        <div style="width:{{ $pourcentageValides }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div>
                    </div>
                </div>

                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-yellow-600 bg-yellow-200">
                                En attente
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold inline-block text-yellow-600">
                                {{ $pourcentageEnAttente }}%
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-yellow-200">
                        <div style="width:{{ $pourcentageEnAttente }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-yellow-500"></div>
                    </div>
                </div>

                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-red-600 bg-red-200">
                                Non traités
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold inline-block text-red-600">
                                {{ $pourcentageNonTraites }}%
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-red-200">
                        <div style="width:{{ $pourcentageNonTraites }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-500"></div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 flex flex-col justify-center items-center">
                    <p class="text-sm text-gray-600 mb-2">Montant total validé</p>
                    <p class="text-xl font-semibold text-blue-600">{{ number_format($montantValide, 0, ',', ' ') }} XAF</p>
                </div>
            </div>
        </section>

        <!-- Détails des salaires -->
        <section>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Détail des salaires par employé</h3>

            @if($salaires->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gradient-to-r from-blue-600 to-teal-500">
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Employé</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Salaire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Mois</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date de création</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($salaires as $salaire)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ optional($salaire->employe)->name ?? 'Employé inconnu' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($salaire->somme, 0, ',', ' ') }} XAF
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $salaire->mois_salaire }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($salaire->retrait_valide)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Validé
                                        </span>
                                    @elseif($salaire->retrait_demande)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            En attente
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Non traité
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $salaire->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-gray-700 text-sm">
                    Le tableau ci-dessus présente la liste complète des salaires pour le mois de {{ $currentMonthName }},
                    avec le nom de l'employé, le montant du salaire, le mois concerné et le statut actuel de validation.
                </p>
            @else
                <div class="bg-blue-50 rounded-lg p-6 text-center">
                    <svg class="w-12 h-12 text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-blue-600 text-lg font-medium">Aucun salaire n'a été enregistré pour le mois de {{ $currentMonthName }}.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
