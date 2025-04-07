@extends('rapports.layout.rapport')

@section('content')
    <x-slot name="reportTitle">
        Rapport des Commandes
    </x-slot>

    <x-slot name="description">
        Ce rapport présente une analyse détaillée des commandes pour le mois de {{ $currentMonthName }}.
    </x-slot>

    <div class="space-y-8">
        <!-- Résumé -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé des commandes</h3>

            <p class="text-gray-700 leading-relaxed">
                Au cours du mois de {{ $currentMonthName }}, l'entreprise a enregistré un total de
                <strong>{{ $totalCommandes }}</strong> commandes. Sur ce total, {{ $commandesValidees }} commandes ont été validées
                ({{ $totalCommandes > 0 ? round(($commandesValidees / $totalCommandes) * 100, 1) : 0 }}%)
                et {{ $commandesEnAttente }} sont toujours en attente de validation.
            </p>

            <p class="text-gray-700 leading-relaxed">
                La répartition des commandes par catégorie montre que :
            </p>

            <ul class="list-disc ml-6 text-gray-700">
                @foreach($commandesParCategorie as $categorie)
                    <li>
                        <strong>{{ ucfirst($categorie->categorie) }}</strong> :
                        {{ $categorie->nombre }} commande(s)
                        ({{ $totalCommandes > 0 ? round(($categorie->nombre / $totalCommandes) * 100, 1) : 0 }}% du total)
                    </li>
                @endforeach
            </ul>
        </section>

        <!-- Analyse -->
        <section class="prose max-w-none">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Analyse et recommandations</h3>

            <p class="text-gray-700 leading-relaxed">
                @if($commandesValidees > 0 && $totalCommandes > 0)
                    Le taux de validation des commandes ce mois-ci s'élève à
                    {{ round(($commandesValidees / $totalCommandes) * 100, 1) }}%, ce qui témoigne d'une
                    @if(($commandesValidees / $totalCommandes) > 0.8)
                        excellente efficacité dans le traitement et la validation des commandes. Cette performance
                        contribue à la satisfaction des clients et à l'efficacité opérationnelle de l'entreprise.
                    @elseif(($commandesValidees / $totalCommandes) > 0.5)
                        bonne efficacité dans le traitement des commandes. Des améliorations sont encore possibles
                        pour augmenter ce taux et optimiser davantage le processus de validation.
                    @else
                        efficacité modérée dans le traitement des commandes. Il est recommandé d'examiner les
                        facteurs qui ralentissent le processus de validation et de mettre en place des mesures
                        pour améliorer ce taux dans les mois à venir.
                    @endif
                @endif
            </p>

            <p class="text-gray-700 leading-relaxed">
                @if($commandesParCategorie->count() > 0)
                    La catégorie <strong>{{ ucfirst($commandesParCategorie->sortByDesc('nombre')->first()->categorie) }}</strong>
                    représente la plus grande part des commandes ce mois-ci
                    ({{ $totalCommandes > 0 ? round(($commandesParCategorie->sortByDesc('nombre')->first()->nombre / $totalCommandes) * 100, 1) : 0 }}%).
                    Cette information est précieuse pour orienter les stratégies d'approvisionnement et d'optimisation des stocks.
                @endif

                @if($commandesEnAttente > 0)
                    Les {{ $commandesEnAttente }} commandes en attente de validation représentent un potentiel commercial
                    important qui nécessite une attention particulière. Une accélération du processus de validation
                    permettrait de concrétiser ces opportunités plus rapidement et d'améliorer la satisfaction des clients.
                @endif
            </p>
        </section>

        <!-- Détails des commandes -->
        <section>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Détail des commandes</h3>

            @if($commandes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($commandes as $commande)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $commande->libelle }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ optional($commande->produitRelation)->nom ?? 'Produit non spécifié' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($commande->categorie) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $commande->quantite }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($commande->valider)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Validée
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
                    Le tableau ci-dessus présente la liste complète des commandes au cours du mois de {{ $currentMonthName }},
                    avec la date, le libellé, le produit, la catégorie, la quantité et le statut de validation de chaque commande.
                </p>
            @else
                <p class="text-gray-700">
                    Aucune commande n'a été enregistrée pendant le mois de {{ $currentMonthName }}.
                </p>
            @endif
        </section>
    </div>
@endsection
