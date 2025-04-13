@extends('rapports.layout.rapport')

@section('content')
    <!-- En-tête du rapport avec statistiques globales -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total des versements -->
            <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
                <h3 class="text-lg font-semibold text-purple-800 mb-2">Total des versements CSG</h3>
                <p class="text-3xl font-bold text-purple-700">{{ number_format($totalVersements, 2, ',', ' ') }} XAF</p>
                <p class="text-sm text-purple-600 mt-2">Pour le mois de {{ $currentMonthName }}</p>
            </div>

            <!-- Nombre de versements -->
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Nombre de versements</h3>
                <p class="text-3xl font-bold text-blue-700">{{ $nombreVersements }}</p>
                <p class="text-sm text-blue-600 mt-2">Transactions enregistrées</p>
            </div>

            <!-- Évolution -->
            <div class="bg-indigo-50 p-6 rounded-lg border border-indigo-200">
                <h3 class="text-lg font-semibold text-indigo-800 mb-2">Évolution mensuelle</h3>
                <p class="text-3xl font-bold {{ $evolution >= 0 ? 'text-green-700' : 'text-red-700' }}">
                    {{ $evolution >= 0 ? '+' : '' }}{{ number_format($evolution, 2, ',', ' ') }}%
                </p>
                <p class="text-sm text-indigo-600 mt-2">Par rapport au mois précédent</p>
            </div>
        </div>
    </div>

    <!-- Résumé narratif -->
    <div class="mb-8 bg-white p-6 rounded-lg border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé des versements CSG</h3>
        <p class="text-gray-700 leading-relaxed">
            Au cours du mois de <strong>{{ $currentMonthName }}</strong>, <strong>{{ $nombreVersements }}</strong> versements CSG
            ont été enregistrés pour un montant total de <strong>{{ number_format($totalVersements, 2, ',', ' ') }} XAF</strong>.

            @if($evolution > 0)
                Le volume des versements CSG a connu une augmentation de <strong>{{ number_format($evolution, 2, ',', ' ') }}%</strong>
                par rapport au mois précédent, ce qui témoigne d'une dynamique positive dans les opérations financières.
            @elseif($evolution == 0)
                Le volume des versements CSG est resté stable par rapport au mois précédent, démontrant une constance
                dans les flux financiers.
            @else
                Le volume des versements CSG a diminué de <strong>{{ number_format(abs($evolution), 2, ',', ' ') }}%</strong>
                par rapport au mois précédent, ce qui pourrait nécessiter une attention particulière de la direction.
            @endif

            @if(isset($versementsParStatut) && count($versementsParStatut) > 0)
                La répartition par statut montre
                @foreach($versementsParStatut as $index => $statut)
                    @if($index > 0)
                        {{ $index == count($versementsParStatut) - 1 ? ' et ' : ', ' }}
                    @endif
                    <strong>{{ $statut->nombre }}</strong> versements {{ $statut->status == 1 ? 'validés' : 'en attente' }}
                @endforeach.
            @endif
        </p>
    </div>

    <!-- Répartition par statut si disponible -->
    @if(isset($versementsParStatut) && count($versementsParStatut) > 0)
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Répartition par statut</h3>
        <div class="grid grid-cols-1 md:grid-cols-{{ count($versementsParStatut) }} gap-6">
            @foreach($versementsParStatut as $statut)
            <div class="bg-{{ $statut->status == 1 ? 'green' : 'amber' }}-50 p-6 rounded-lg border border-{{ $statut->status == 1 ? 'green' : 'amber' }}-200">
                <h3 class="text-lg font-semibold text-{{ $statut->status == 1 ? 'green' : 'amber' }}-800 mb-2">
                    {{ $statut->status == 1 ? 'Validés' : 'En attente' }}
                </h3>
                <p class="text-3xl font-bold text-{{ $statut->status == 1 ? 'green' : 'amber' }}-700">{{ $statut->nombre }}</p>
                <p class="text-sm text-{{ $statut->status == 1 ? 'green' : 'amber' }}-600 mt-2">
                    {{ number_format(($statut->nombre / $nombreVersements) * 100, 1) }}% du total
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Liste des versements -->
    <div>
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Détail des versements CSG</h3>
        <div class="overflow-x-auto print:text-xs">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Date</th>
                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Verseur</th>
                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Encaisseur</th>
                        <th class="py-3 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Montant</th>
                        <th class="py-3 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($versements as $versement)
                    <tr>
                        <td class="py-2 px-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($versement->date)->format('d/m/Y') }}
                        </td>
                        <td class="py-2 px-4 text-sm text-gray-900">
                            {{ $versement->verseur->name ?? 'Non spécifié' }}
                        </td>
                        <td class="py-2 px-4 text-sm text-gray-900">
                            {{ $versement->encaisseur->name ?? 'Non spécifié' }}
                        </td>
                        <td class="py-2 px-4 text-sm font-medium text-right text-gray-900">
                            {{ number_format($versement->somme, 2, ',', ' ') }} XAF
                        </td>
                        <td class="py-2 px-4 text-sm text-center">
                            @if($versement->status == 1)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Validé
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    En attente
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Conclusion et projections -->
    <div class="mt-8 bg-indigo-50 p-6 rounded-lg border border-indigo-200 print:break-before-auto">
        <h3 class="text-xl font-semibold text-indigo-800 mb-3">Conclusion et projections</h3>
        <div class="text-indigo-700 space-y-3">
            <p>
                Le bilan des versements CSG pour le mois de <strong>{{ $currentMonthName }}</strong> montre
                @if($evolution > 5)
                    une performance financière en forte progression.
                @elseif($evolution > 0)
                    une évolution positive stable.
                @elseif($evolution == 0)
                    une stabilité dans les flux financiers.
                @else
                    un ralentissement des activités financières qui mérite attention.
                @endif
            </p>

            <p>
                Pour le mois à venir, il est recommandé de
                @if(isset($versementsParStatut))
                    @foreach($versementsParStatut as $statut)
                        @if($statut->status == 0 && $statut->nombre > 0)
                            traiter prioritairement les {{ $statut->nombre }} versements en attente et
                        @endif
                    @endforeach
                @endif
                maintenir un suivi rigoureux des nouvelles transactions pour garantir la fluidité des opérations financières.
            </p>
        </div>
    </div>
@endsection
