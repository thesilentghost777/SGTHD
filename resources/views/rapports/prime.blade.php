@extends('rapports.layout.rapport')

@section('content')
    <x-slot name="reportTitle">
        Rapport des Primes
    </x-slot>

    <x-slot name="description">
        Ce rapport présente une analyse détaillée des primes accordées pour le mois de {{ $currentMonthName }}.
    </x-slot>

    <div class="space-y-8">
        <!-- Résumé -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé des primes</h3>

            <p class="text-gray-700 leading-relaxed">
                Au cours du mois de {{ $currentMonthName }}, un total de <strong>{{ number_format($totalPrimes, 0, ',', ' ') }} XAF</strong>
                a été distribué en primes à {{ $nombrePrimes }} employé(s). Ces primes représentent une
                {{ $evolution >= 0 ? 'augmentation' : 'diminution' }} de <strong>{{ abs($evolution) }}%</strong> par rapport au mois précédent.
            </p>

            <p class="text-gray-700 leading-relaxed">
                Le montant moyen des primes accordées s'élève à <strong>{{ number_format($montantMoyen, 0, ',', ' ') }} XAF</strong> par employé concerné.
                La répartition des primes par type est la suivante :
            </p>

            <ul class="list-disc ml-6 text-gray-700">
                @foreach($primesParLibelle as $prime)
                    <li>
                        <strong>{{ ucfirst($prime->libelle) }}</strong> :
                        {{ $prime->nombre }} prime(s) pour un total de {{ number_format($prime->total, 0, ',', ' ') }} XAF
                        ({{ $totalPrimes > 0 ? round(($prime->total / $totalPrimes) * 100, 1) : 0 }}% du montant total)
                    </li>
                @endforeach
            </ul>
        </section>

        <!-- Analyse -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Analyse et recommandations</h3>

            <p class="text-gray-700 leading-relaxed">
                @if($evolution > 20)
                    L'augmentation significative des primes ce mois-ci ({{ $evolution }}%) témoigne d'une reconnaissance
                    accrue des performances des employés. Cette politique de motivation par les primes contribue au maintien
                    d'un climat social positif et à l'encouragement de l'excellence.
                @elseif($evolution < -20)
                    La diminution notable des primes ce mois-ci ({{ abs($evolution) }}%) peut être liée à une évolution
                    des critères d'attribution ou à des résultats moins favorables. Il est important de communiquer clairement
                    sur les raisons de cette baisse pour maintenir la motivation des équipes.
                @else
                    Le niveau des primes est resté relativement stable par rapport au mois précédent,
                    avec une variation de {{ $evolution }}%. Cette continuité dans la politique de reconnaissance
                    contribue à la prévisibilité des rémunérations variables pour les employés.
                @endif
            </p>

            <p class="text-gray-700 leading-relaxed">
                @if($primesParLibelle->count() > 0)
                    Les primes de type <strong>{{ ucfirst($primesParLibelle->sortByDesc('total')->first()->libelle) }}</strong>
                    représentent la plus grande part du budget des primes ce mois-ci
                    ({{ $totalPrimes > 0 ? round(($primesParLibelle->sortByDesc('total')->first()->total / $totalPrimes) * 100, 1) : 0 }}%).
                    Cette répartition reflète les priorités actuelles de l'entreprise en matière de reconnaissance des performances.
                @endif

                @if($montantMoyen > 0)
                    Avec un montant moyen de {{ number_format($montantMoyen, 0, ',', ' ') }} XAF par prime,
                    ces gratifications constituent un complément de rémunération significatif pour les employés concernés.
                    Il est recommandé de maintenir cette politique d'incitation pour continuer à stimuler les performances
                    individuelles et collectives.
                @endif
            </p>
        </section>

        <!-- Détails des primes -->
        <section>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Détail des primes</h3>

            @if($primes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($primes as $prime)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $prime->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ optional($prime->employe)->name ?? 'Employé inconnu' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ ucfirst($prime->libelle) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($prime->montant, 0, ',', ' ') }} XAF
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="mt-4 text-gray-700 text-sm">
                    Le tableau ci-dessus présente la liste complète des primes accordées au cours du mois de {{ $currentMonthName }},
                    avec la date d'attribution, le bénéficiaire, le type de prime et le montant.
                </p>
            @else
                <p class="text-gray-700">
                    Aucune prime n'a été accordée pendant le mois de {{ $currentMonthName }}.
                </p>
            @endif
        </section>
    </div>
@endsection
