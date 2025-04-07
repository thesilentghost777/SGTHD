@extends('rapports.layout.rapport')

@section('content')
    <x-slot name="reportTitle">
        Rapport des Déductions Salariales
    </x-slot>

    <x-slot name="description">
        Ce rapport présente une analyse détaillée des déductions salariales pour le mois de {{ $currentMonthName }}.
    </x-slot>

    <div class="space-y-8">
        <!-- Résumé -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé des déductions</h3>

            <p class="text-gray-700 leading-relaxed">
                Au cours du mois de {{ $currentMonthName }}, un total de <strong>{{ number_format($totalDeductions, 0, ',', ' ') }} XAF</strong>
                a été comptabilisé en déductions salariales. Ces déductions représentent une
                {{ $evolution >= 0 ? 'augmentation' : 'diminution' }} de <strong>{{ abs($evolution) }}%</strong> par rapport au mois précédent.
            </p>

            <p class="text-gray-700 leading-relaxed">
                La répartition des déductions par catégorie est la suivante :
            </p>

            <ul class="list-disc ml-6 text-gray-700">
                <li>
                    <strong>Manquants</strong> : {{ number_format($totalManquants, 0, ',', ' ') }} XAF
                </li>
                <li>
                    <strong>Remboursements</strong> : {{ number_format($totalRemboursements, 0, ',', ' ') }} XAF
                </li>

                <li>
                    <strong>Caisse sociale</strong> : {{ number_format($totalCaisseSociale, 0, ',', ' ') }} XAF
                </li>
            </ul>
        </section>

        <!-- Analyse -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Analyse et recommandations</h3>

            <p class="text-gray-700 leading-relaxed">
                @if($totalManquants > 0 && $totalDeductions > 0 && ($totalManquants / $totalDeductions) > 0.3)
                    Les manquants représentent une part significative ({{ round(($totalManquants / $totalDeductions) * 100, 1) }}%)
                    des déductions totales ce mois-ci. Cette situation mérite une attention particulière et pourrait nécessiter
                    la mise en place de mesures de contrôle et de prévention plus efficaces pour réduire ces pertes à l'avenir.
                @endif

                @if($totalRemboursements > 0 && $totalDeductions > 0)
                    Les remboursements constituent {{ round(($totalRemboursements / $totalDeductions) * 100, 1) }}% des déductions,
                    ce qui témoigne d'un processus actif de régularisation des avances et prêts accordés précédemment aux employés.
                @endif

                @if($totalCaisseSociale > 0)
                    Les contributions à la caisse sociale s'élèvent à {{ number_format($totalCaisseSociale, 0, ',', ' ') }} XAF
                    ce mois-ci. Ces fonds sont essentiels pour assurer la protection sociale des employés et renforcer
                    la solidarité au sein de l'entreprise.
                @endif
            </p>

            <p class="text-gray-700 leading-relaxed">
                @if($evolution > 20)
                    L'augmentation significative des déductions ce mois-ci ({{ $evolution }}%) peut indiquer
                    soit une hausse des incidents nécessitant des remboursements, soit une intensification des efforts
                    de recouvrement des créances. Une analyse plus détaillée par catégorie de déduction est recommandée
                    pour identifier les causes spécifiques de cette tendance.
                @elseif($evolution < -20)
                    La diminution notable des déductions ce mois-ci ({{ abs($evolution) }}%) peut être interprétée
                    comme un signe positif, reflétant potentiellement une réduction des incidents et manquements,
                    ou l'achèvement de cycles de remboursement.
                @endif
            </p>
        </section>

        <!-- Détails des déductions -->
        <section>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Détail des déductions par employé</h3>

            @if($deductions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manquants</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remboursements</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prêts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Caisse sociale</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($deductions as $deduction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $deduction->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ optional($deduction->employe)->name ?? 'Employé inconnu' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($deduction->manquants, 0, ',', ' ') }} XAF
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($deduction->remboursement, 0, ',', ' ') }} XAF
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($deduction->pret, 0, ',', ' ') }} XAF
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($deduction->caisse_sociale, 0, ',', ' ') }} XAF
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-gray-700 text-sm">
                    Le tableau ci-dessus présente le détail des déductions salariales par employé au cours du mois de {{ $currentMonthName }},
                    avec la répartition par catégorie et le montant total pour chaque employé.
                </p>
            @else
                <p class="text-gray-700">
                    Aucune déduction salariale n'a été enregistrée pendant le mois de {{ $currentMonthName }}.
                </p>
            @endif
        </section>
    </div>
@endsection
