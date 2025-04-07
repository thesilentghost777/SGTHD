@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-r from-blue-50 to-blue-100">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6">
        <h1 class="text-3xl font-bold text-white">Rapport de {{ $employee->name }}</h1>
        <p class="text-blue-100 mt-2">{{ $month }}</p>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="mb-4 flex justify-between items-center print:hidden">
            <a href="{{ route('rapports.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Imprimer
            </button>
        </div>

        <div id="rapport-content" class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- En-tête du rapport -->
            <div class="p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $employee->name }}</h2>
                        <p class="text-gray-600">{{ ucfirst($employee->role ?? 'Non défini') }} {{ $employee->secteur ? '- ' . $employee->secteur : '' }}</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <p class="text-gray-600"><span class="font-medium">Date du rapport:</span> {{ now()->format('d/m/Y') }}</p>
                        <p class="text-gray-600"><span class="font-medium">Période:</span> {{ $month }}</p>
                    </div>
                </div>
            </div>

            <!-- Informations générales -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Informations générales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Date de naissance:</span> {{ $dateNaissance }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Âge:</span> {{ $age }} ans</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Numéro de téléphone:</span> {{ $employee->num_tel ?? 'Non spécifié' }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Email:</span> {{ $employee->email ?? 'Non spécifié' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Année de début de service:</span> {{ $employee->annee_debut_service ?? 'Non spécifiée' }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Années de service:</span> {{ $anneeService }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Jours de présence ce mois:</span> {{ $joursPresence }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Total d'heures travaillées:</span> {{ $totalHeuresTravail }}</p>
                    </div>
                </div>
            </div>

            <!-- Salaire et finances -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Salaire et finances</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Salaire mensuel:</span> {{ number_format($salaire, 0, ',', ' ') }} FCFA</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Avance sur salaire:</span> {{ number_format($avanceSalaire, 0, ',', ' ') }} FCFA</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Total des primes:</span> {{ number_format($totalPrimes, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div>
                        @if($acouper)
                        <p class="text-gray-700 mb-2"><span class="font-medium">Montants à déduire:</span></p>
                        <ul class="list-disc list-inside pl-4 text-gray-700">
                            @if($acouper->manquants > 0)
                            <li>Manquants: {{ number_format($acouper->manquants, 0, ',', ' ') }} FCFA</li>
                            @endif
                            @if($acouper->remboursement > 0)
                            <li>Remboursement: {{ number_format($acouper->remboursement, 0, ',', ' ') }} FCFA</li>
                            @endif
                            @if($acouper->pret > 0)
                            <li>Prêt: {{ number_format($acouper->pret, 0, ',', ' ') }} FCFA</li>
                            @endif
                            @if($acouper->caisse_sociale > 0)
                            <li>Caisse sociale: {{ number_format($acouper->caisse_sociale, 0, ',', ' ') }} FCFA</li>
                            @endif
                        </ul>
                        @else
                        <p class="text-gray-700 mb-2"><span class="font-medium">Aucun montant à déduire pour ce mois.</span></p>
                        @endif
                    </div>
                </div>

                @if(count($primes) > 0)
                <div class="mt-4">
                    <p class="font-medium text-gray-700 mb-2">Détail des primes:</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Libellé</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($primes as $prime)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $prime->libelle }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($prime->montant, 0, ',', ' ') }} FCFA</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $prime->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Évaluation -->
            @if($evaluation)
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Évaluation</h3>
                <div>
                    <p class="text-gray-700 mb-2"><span class="font-medium">Note:</span> {{ $evaluation->note }}/10</p>
                    <p class="text-gray-700 mb-2"><span class="font-medium">Appréciation:</span></p>
                    <p class="text-gray-700 bg-gray-50 p-3 rounded-md">{{ $evaluation->appreciation }}</p>
                </div>
            </div>
            @endif

            <!-- Congés et repos -->
            @if($reposConge)
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Congés et repos</h3>
                <div>
                    <p class="text-gray-700 mb-2"><span class="font-medium">Jour de repos hebdomadaire:</span> {{ ucfirst($reposConge->jour) }}</p>
                    @if($reposConge->conges)
                    <p class="text-gray-700 mb-2"><span class="font-medium">Jours de congés disponibles:</span> {{ $reposConge->conges }}</p>
                    @endif
                    @if($reposConge->debut_c)
                    <p class="text-gray-700 mb-2"><span class="font-medium">Début du dernier congé:</span> {{ \Carbon\Carbon::parse($reposConge->debut_c)->format('d/m/Y') }}</p>
                    <p class="text-gray-700 mb-2"><span class="font-medium">Raison:</span> {{ ucfirst($reposConge->raison_c ?? 'Non spécifiée') }}</p>
                    @if($reposConge->autre_raison)
                    <p class="text-gray-700 mb-2"><span class="font-medium">Détail:</span> {{ $reposConge->autre_raison }}</p>
                    @endif
                    @endif
                </div>
            </div>
            @endif

            <!-- Délits et incidents -->
            @if(count($delits) > 0)
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Délits et incidents</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Délit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($delits as $delit)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $delit->deli->nom }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $delit->deli->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($delit->deli->montant, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($delit->date_incident)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-4 text-gray-700"><span class="font-medium">Montant total des délits:</span> {{ number_format($totalDelits, 0, ',', ' ') }} FCFA</p>
            </div>
            @endif

            <!-- Données spécifiques au rôle -->
            @if($employee->role == 'vendeur')
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Performance de vente</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Chiffre d'affaires du mois:</span> {{ number_format($chiffreAffaires, 0, ',', ' ') }} FCFA</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Nombre de transactions:</span> {{ $nbTransactions }}</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Moyenne journalière:</span> {{ number_format($moyenneVentesParJour, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
            @elseif($employee->role == 'boulanger' || $employee->role == 'patissier')
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Performance de production</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Valeur totale de production:</span> {{ number_format($valeurTotaleProduction, 0, ',', ' ') }} FCFA</p>
                        <p class="text-gray-700 mb-2"><span class="font-medium">Coût des matières premières:</span> {{ number_format($coutMatieresPremieres, 0, ',', ' ') }} FCFA</p>
                        <p class="text-gray-700 mb-2">
                            <span class="font-medium">Ratio dépense/gain:</span>
                            <span class="{{ $ratioDepenseGain >= 1 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($ratioDepenseGain, 2, ',', ' ') }}
                                ({{ $ratioDepenseGain >= 1 ? 'Rentable' : 'Non rentable' }})
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Conclusion -->
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Conclusion</h3>
                <div class="bg-gray-50 p-4 rounded-md">
                    <p class="text-gray-700">
                        @if($employee->role == 'vendeur_boulangerie' || $employee->role == 'vendeur_boulangerie')
                            <span class="font-medium">{{ $employee->name }}</span> a effectué
                            <span class="font-medium">{{ $nbTransactions }}</span> transactions ce mois-ci,
                            générant un chiffre d'affaires de
                            <span class="font-medium">{{ number_format($chiffreAffaires, 0, ',', ' ') }} FCFA</span>.
                            @if($joursPresence > 0)
                                Sa performance quotidienne moyenne est de
                                <span class="font-medium">{{ number_format($moyenneVentesParJour, 0, ',', ' ') }} FCFA</span>.
                            @endif

                            @if($acouper && ($acouper->manquants > 0 || $acouper->remboursement > 0 || $acouper->pret > 0 || $acouper->caisse_sociale > 0))
                                Des déductions d'un montant total de
                                <span class="font-medium">{{ number_format($acouper->manquants + $acouper->remboursement + $acouper->pret + $acouper->caisse_sociale, 0, ',', ' ') }} FCFA</span>
                                seront appliquées à son salaire.
                            @endif

                            @if($totalPrimes > 0)
                                L'employé a reçu des primes d'un montant total de
                                <span class="font-medium">{{ number_format($totalPrimes, 0, ',', ' ') }} FCFA</span> ce mois-ci.
                            @endif
                        @elseif($employee->role == 'boulanger' || $employee->role == 'patissier')
                            <span class="font-medium">{{ $employee->name }}</span> a produit des articles d'une valeur totale de
                            <span class="font-medium">{{ number_format($valeurTotaleProduction, 0, ',', ' ') }} FCFA</span> ce mois-ci,
                            utilisant des matières premières d'un coût de
                            <span class="font-medium">{{ number_format($coutMatieresPremieres, 0, ',', ' ') }} FCFA</span>.

                            @if($ratioDepenseGain >= 1)
                                Avec un ratio dépense/gain de <span class="font-medium text-green-600">{{ number_format($ratioDepenseGain, 2, ',', ' ') }}</span>,
                                sa production est rentable pour l'entreprise.
                            @else
                                Avec un ratio dépense/gain de <span class="font-medium text-red-600">{{ number_format($ratioDepenseGain, 2, ',', ' ') }}</span>,
                                sa production n'est actuellement pas rentable pour l'entreprise.
                            @endif

                            @if($acouper && ($acouper->manquants > 0 || $acouper->remboursement > 0 || $acouper->pret > 0 || $acouper->caisse_sociale > 0))
                                Des déductions d'un montant total de
                                <span class="font-medium">{{ number_format($acouper->manquants + $acouper->remboursement + $acouper->pret + $acouper->caisse_sociale, 0, ',', ' ') }} FCFA</span>
                                seront appliquées à son salaire.
                            @endif

                            @if($totalPrimes > 0)
                                L'employé a reçu des primes d'un montant total de
                                <span class="font-medium">{{ number_format($totalPrimes, 0, ',', ' ') }} FCFA</span> ce mois-ci.
                            @endif
                        @else
                            <span class="font-medium">{{ $employee->name }}</span> a été présent
                            <span class="font-medium">{{ $joursPresence }}</span> jours ce mois-ci,
                            cumulant un total de <span class="font-medium">{{ $totalHeuresTravail }}</span> heures de travail.

                            @if($acouper && ($acouper->manquants > 0 || $acouper->remboursement > 0 || $acouper->pret > 0 || $acouper->caisse_sociale > 0))
                                Des déductions d'un montant total de
                                <span class="font-medium">{{ number_format($acouper->manquants + $acouper->remboursement + $acouper->pret + $acouper->caisse_sociale, 0, ',', ' ') }} FCFA</span>
                                seront appliquées à son salaire.
                            @endif

                            @if($totalPrimes > 0)
                                L'employé a reçu des primes d'un montant total de
                                <span class="font-medium">{{ number_format($totalPrimes, 0, ',', ' ') }} FCFA</span> ce mois-ci.
                            @endif
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        /* Masquer tous les éléments en dehors du rapport */
        body * {
            visibility: hidden;
        }

        /* Afficher uniquement le contenu du rapport */
        #rapport-content, #rapport-content * {
            visibility: visible;
        }

        /* Positionner le rapport en haut de la page */
        #rapport-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
            background-color: white;
        }

        /* Éliminer les ombres qui peuvent causer des problèmes à l'impression */
        .shadow-md {
            box-shadow: none !important;
        }

        /* Assurer que toutes les couleurs s'impriment correctement */
        .text-gray-700, .text-gray-800, .text-gray-900 {
            color: #000 !important;
        }

        /* Ajuster les marges pour l'impression */
        @page {
            margin: 1cm;
        }

        /* Gérer les sauts de page */
        .p-6 {
            page-break-inside: avoid;
        }
    }
</style>
@endsection
