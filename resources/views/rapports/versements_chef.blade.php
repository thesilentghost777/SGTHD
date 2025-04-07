@extends('rapports.layout.rapport')

@section('content')
    <x-slot name="reportTitle">
        Rapport des Versements Chefs de Production
    </x-slot>

    <x-slot name="description">
        Ce rapport présente une analyse détaillée des versements aux chefs de production pour le mois de {{ $currentMonthName }}.
    </x-slot>

    <div class="space-y-8">
        <!-- Résumé -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé des versements</h3>

            <p class="text-gray-700 leading-relaxed">
                Au cours du mois de {{ $currentMonthName }}, un total de <strong>{{ number_format($totalVersements, 0, ',', ' ') }} XAF</strong>
                a été versé aux chefs de production, réparti sur {{ $nombreVersements }} opérations. Ces versements représentent une
                {{ $evolution >= 0 ? 'augmentation' : 'diminution' }} de <strong>{{ abs($evolution) }}%</strong> par rapport au mois précédent.
            </p>

            <p class="text-gray-700 leading-relaxed">
                Sur les {{ $nombreVersements }} versements effectués, {{ $versementsValides }} ont été validés définitivement et
                {{ $versementsEnAttente }} sont toujours en attente de validation. Cette proportion de
                {{ $nombreVersements > 0 ? round(($versementsValides / $nombreVersements) * 100, 1) : 0 }}% de versements validés
                témoigne de la rigueur dans le processus de validation des paiements.
            </p>
        </section>

        <!-- Analyse -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Analyse et recommandations</h3>

            <p class="text-gray-700 leading-relaxed">
                @if($evolution > 20)
                    L'augmentation significative des versements aux chefs de production ce mois-ci ({{ $evolution }}%)
                    peut être liée à une intensification de l'activité de production ou à des primes exceptionnelles.
                    Il est recommandé d'analyser cette tendance en correlation avec les performances de production
                    pour s'assurer de sa cohérence et de sa durabilité.
                @elseif($evolution < -20)
                    La diminution notable des versements aux chefs de production ce mois-ci ({{ abs($evolution) }}%)
                    pourrait indiquer une réduction de l'activité de production ou une révision des modes de rémunération.
                    Il convient de vérifier que cette baisse n'impacte pas négativement la motivation des équipes
                    et les performances de production.
                @else
                    Les versements aux chefs de production sont restés relativement stables par rapport au mois précédent,
                    avec une variation de {{ $evolution }}%. Cette stabilité témoigne d'une continuité dans l'activité
                    et la gestion des équipes de production.
                @endif
            </p>

            <p class="text-gray-700 leading-relaxed">
                @if($versementsEnAttente > 0)
                    Il est important de traiter rapidement les {{ $versementsEnAttente }} versements en attente
                    afin d'éviter tout retard de paiement qui pourrait affecter la motivation des chefs de production.
                    Un suivi rigoureux des délais de validation est recommandé pour optimiser ce processus.
                @endif
            </p>
        </section>

        <!-- Détails des versements -->
        <section>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Détail des versements</h3>

            @if($versements->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chef de production</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($versements as $versement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $versement->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $versement->libelle }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ optional($versement->chefProduction)->name ?? 'Chef inconnu' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($versement->montant, 0, ',', ' ') }} XAF
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($versement->status)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Validé
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            En attente
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-gray-700 text-sm">
                    Le tableau ci-dessus présente la liste complète des versements aux chefs de production au cours du mois de {{ $currentMonthName }},
                    avec la date, le libellé, le destinataire, le montant et le statut de validation de chaque opération.
                </p>
            @else
                <p class="text-gray-700">
                    Aucun versement aux chefs de production n'a été enregistré pendant le mois de {{ $currentMonthName }}.
                </p>
            @endif
        </section>
    </div>
@endsection
