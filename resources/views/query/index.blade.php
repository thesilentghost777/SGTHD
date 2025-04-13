@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Explorateur de Tables') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('query.analyze') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="table" class="block text-sm font-medium text-gray-700">Sélectionnez une table</label>
                        <select name="table" id="table" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($tables as $table)
                                <option value="{{ $table }}">
                                    @switch($table)
                                        @case('Acouper')
                                            Manquants et frais a deduire
                                            @break
                                        @case('Commande')
                                            Commandes
                                            @break
                                        @case('Complexe')
                                            Complexes
                                            @break
                                        @case('Daily_assignments')
                                            Assignations quotidiennes
                                            @break
                                        @case('Evenement')
                                            Événements
                                            @break
                                        @case('Extra')
                                            Regles
                                            @break
                                        @case('Facture')
                                            Factures
                                            @break
                                        @case('Horaire')
                                            Horaires
                                            @break
                                        @case('Matiere')
                                            Matières
                                            @break
                                        @case('Matiere_recommander')
                                            Matières recommandées
                                            @break
                                        @case('Message')
                                            Messages
                                            @break
                                        @case('Porter')
                                            Porteurs
                                            @break
                                        @case('Prime')
                                            Primes
                                            @break
                                        @case('Production_suggerer_par_jour')
                                            Productions suggérées par jour
                                            @break
                                        @case('Produit_fixes')
                                            Produits fixes
                                            @break
                                        @case('Produit_recu')
                                            Produits reçus
                                            @break
                                        @case('Reservations_mp')
                                            Réservations de matières premières
                                            @break
                                        @case('Utilisation')
                                            Utilisations
                                            @break
                                        @case('Versement_chef')
                                            Versements aux chefs
                                            @break
                                        @case('Versement_csg')
                                            Versements CSG
                                            @break
                                        @case('announcements')
                                            Annonces
                                            @break
                                        @case('assignations_matiere')
                                            Assignations de matières
                                            @break
                                        @case('avance_salaires')
                                            Avances sur salaires
                                            @break
                                        @case('bag_transactions')
                                            Transactions de sacs
                                            @break
                                        @case('bags')
                                            Sacs
                                            @break

                                        @case('categories')
                                            Catégories
                                            @break
                                        @case('deli_user')
                                            Utilisateurs Deli
                                            @break
                                        @case('delis')
                                            Delis
                                            @break
                                        @case('depenses')
                                            Dépenses
                                            @break
                                        @case('evaluations')
                                            Évaluations Employes
                                            @break
                                        @case('plannings')
                                            Plannings
                                            @break
                                        @case('produit_stocks')
                                            Stocks de produits
                                            @break
                                        @case('reactions')
                                            Réactions
                                            @break
                                        @case('repos_conges')
                                            Repos et congés
                                            @break
                                        @case('salaires')
                                            Salaires
                                            @break
                                        @case('stagiaires')
                                            Stagiaires
                                            @break
                                        @case('transaction_ventes')
                                            Transactions de ventes
                                            @break
                                        @case('transactions')
                                            Transactions Financiere
                                            @break
                                        @case('users')
                                            Utilisateurs
                                            @break
                                        @default
                                            technical table
                                    @endswitch
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Analyser
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Section d'explication -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8 \p-6 bg-gray-100 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-800">Comment utiliser cette fonctionnalité ?</h3>
            <p class="mt-2 text-gray-600">
                Cette interface vous permet d'explorer les différentes tables disponibles dans votre base de données.
                Sélectionnez une table dans la liste déroulante et cliquez sur <strong>Analyser</strong> pour afficher son contenu de façon claire et structurée.
                Cela vous aidera à mieux comprendre les données stockées et leur organisation.
            </p>
            <p class="mt-2 text-gray-600">
                Cette analyse est particulièrement utile pour les administrateurs  souhaitant examiner
                les informations de fond en comble.
            </p>
        </div>
    </div>

@endsection
