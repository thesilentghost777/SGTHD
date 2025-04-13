@extends('rapports.layout.rapport')

@section('content')
    <x-slot name="reportTitle">
        Rapport des Dépenses
    </x-slot>

    <x-slot name="description">
        Ce rapport présente une analyse détaillée des dépenses effectuées pour le mois de {{ $currentMonthName }}.
    </x-slot>

    <div class="space-y-8">
        <!-- Résumé -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé des dépenses</h3>

            <p class="text-gray-700 leading-relaxed">
                Au cours du mois de {{ $currentMonthName }}, l'entreprise a enregistré un total de
                <strong>{{ number_format($totalDepenses, 0, ',', ' ') }} XAF</strong> en dépenses, réparties sur
                {{ $nombreDepenses }} opérations. Ces dépenses représentent une
                {{ $evolution >= 0 ? 'augmentation' : 'diminution' }} de <strong>{{ abs($evolution) }}%</strong>
                par rapport au mois précédent.
            </p>

            <p class="text-gray-700 leading-relaxed">
                La répartition des dépenses par type montre que :
            </p>

            <ul class="list-disc ml-6 text-gray-700">
                @foreach($depensesParType as $type)
                    <li>
                        <strong>{{ ucfirst(str_replace('_', ' ', $type->type)) }}</strong> :
                        {{ number_format($type->total, 0, ',', ' ') }} XAF
                        ({{ round(($type->total / $totalDepenses) * 100, 1) }}% du total)
                    </li>
                @endforeach
            </ul>
        </section>

        <!-- Analyse -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Analyse et recommandations</h3>

            <p class="text-gray-700 leading-relaxed">
                @if($evolution > 20)
                    L'augmentation significative des dépenses ce mois-ci ({{ $evolution }}%) mérite une attention particulière.
                    Il est recommandé d'examiner en détail les postes de dépenses ayant connu les plus fortes hausses afin
                    d'identifier les causes de cette augmentation et d'évaluer si des mesures d'optimisation sont nécessaires.
                @elseif($evolution < -20)
                    La diminution notable des dépenses ce mois-ci ({{ abs($evolution) }}%) témoigne d'une gestion efficace
                    des ressources financières de l'entreprise. Cette tendance positive contribue à l'amélioration de la
                    rentabilité globale et devrait être maintenue dans la mesure du possible.
                @else
                    Les dépenses sont restées relativement stables par rapport au mois précédent, avec une variation de
                    {{ $evolution }}%. Cette stabilité témoigne d'une gestion maîtrisée des ressources financières de l'entreprise.
                @endif
            </p>

            <p class="text-gray-700 leading-relaxed">
                @if($depensesParType->count() > 0)
                    Le principal poste de dépense ce mois-ci concerne
                    <strong>{{ ucfirst(str_replace('_', ' ', $depensesParType->sortByDesc('total')->first()->type)) }}</strong>,
                    représentant {{ round(($depensesParType->sortByDesc('total')->first()->total / $totalDepenses) * 100, 1) }}%
                    du total des dépenses. Ce constat doit orienter les efforts d'optimisation des coûts vers ce poste spécifique.
                @endif
            </p>
        </section>

        <!-- Détails des dépenses -->
        <section>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Détail des dépenses</h3>

            @if($depenses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auteur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($depenses as $depense)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $depense->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $depense->nom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $depense->type)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ optional($depense->auteurRelation)->name ?? 'Utilisateur inconnu' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($depense->prix, 0, ',', ' ') }} XAF
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-gray-700 text-sm">
                    Le tableau ci-dessus présente la liste complète des dépenses effectuées au cours du mois de {{ $currentMonthName }},
                    avec la date, la description, le type, l'auteur et le montant de chaque opération.
                </p>
            @else
                <p class="text-gray-700">
                    Aucune dépense n'a été enregistrée pendant le mois de {{ $currentMonthName }}.
                </p>
            @endif
        </section>
    </div>
@endsection
