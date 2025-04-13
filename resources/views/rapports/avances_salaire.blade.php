@extends('rapports.layout.rapport')

@section('content')
    <x-slot name="reportTitle">
        Rapport des Avances sur Salaires
    </x-slot>

    <x-slot name="description">
        Ce rapport présente une analyse détaillée des avances sur salaires pour le mois de {{ $currentMonthName }}.
    </x-slot>

    <div class="space-y-8">
        <!-- Résumé -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé des avances sur salaires</h3>

            <p class="text-gray-700 leading-relaxed">
                Au cours du mois de {{ $currentMonthName }}, un total de <strong>{{ number_format($totalAvances, 0, ',', ' ') }} XAF</strong>
                a été distribué en avances sur salaires à {{ $nombreAvances }} employé(s). Ces avances représentent une
                {{ $evolution >= 0 ? 'augmentation' : 'diminution' }} de <strong>{{ abs($evolution) }}%</strong> par rapport au mois précédent.
            </p>

            <p class="text-gray-700 leading-relaxed">
                Parmi les {{ $nombreAvances }} demandes d'avances, {{ $avancesValidees }} ont été validées et {{ $avancesEnAttente }}
                sont en attente de validation. Le montant moyen des avances accordées s'élève à
                <strong>{{ number_format($montantMoyen, 0, ',', ' ') }} XAF</strong>.
            </p>
        </section>

        <!-- Analyse -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Analyse et recommandations</h3>

            <p class="text-gray-700 leading-relaxed">
                @if($evolution > 20)
                    La forte augmentation des demandes d'avances sur salaires pour ce mois de {{ $currentMonthName }} pourrait
                    indiquer des besoins financiers accrus au sein de l'équipe. Il est recommandé d'examiner les causes possibles
                    de cette tendance et d'envisager des mesures pour soutenir les employés face à leurs besoins financiers.
                @elseif($evolution < -20)
                    La baisse significative des demandes d'avances sur salaires pour ce mois de {{ $currentMonthName }} témoigne
                    d'une amélioration potentielle de la situation financière des employés. Cette tendance positive pourrait être
                    le résultat des mesures prises précédemment ou d'une évolution favorable de la situation économique.
                @else
                    Les demandes d'avances sur salaires pour ce mois de {{ $currentMonthName }} restent relativement stables
                    par rapport au mois précédent. Cette stabilité indique une gestion équilibrée des besoins financiers des employés.
                @endif
            </p>

            <p class="text-gray-700 leading-relaxed">
                @if($nombreAvances > 0 && $avancesEnAttente > 0)
                    Il est important de traiter rapidement les {{ $avancesEnAttente }} demandes d'avances en attente afin de
                    répondre aux besoins urgents des employés concernés.
                @endif
            </p>
        </section>

        <!-- Détails des avances -->
        <section>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Détail des avances sur salaires</h3>

            @if($avances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($avances as $avance)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ optional($avance->employe)->name ?? 'Employé inconnu' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($avance->sommeAs, 0, ',', ' ') }} XAF
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $avance->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($avance->retrait_valide)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Validée
                                        </span>
                                    @elseif($avance->retrait_demande)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            En attente
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Non traitée
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-gray-700 text-sm">
                    Le tableau ci-dessus présente la liste complète des avances sur salaires demandées au cours du mois de {{ $currentMonthName }},
                    avec le nom de l'employé, le montant accordé, la date de la demande et le statut actuel de validation.
                </p>
            @else
                <p class="text-gray-700">
                    Aucune avance sur salaire n'a été demandée pendant le mois de {{ $currentMonthName }}.
                </p>
            @endif
        </section>
    </div>
@endsection
