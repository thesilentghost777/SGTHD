@extends('pages.serveur.serveur_default')

@section('page-content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <h1 class="text-3xl font-bold text-center text-blue-800 mb-8 flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Statistiques des Produits
        </h1>

        <!-- Sélecteur de période -->
        <div x-data="{ period: '{{ $period }}' }" class="mb-8">
            <div class="max-w-xs mx-auto">
                <label for="period" class="block text-sm font-medium text-gray-700 mb-2">Sélectionner la période</label>
                <select
                    id="period"
                    x-model="period"
                    @change="window.location.href = `/serveur/stats/${period}`"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all duration-200">
                    <option value="current" :selected="period === 'current'">Ce mois-ci</option>
                    <option value="last" :selected="period === 'last'">Le mois dernier</option>
                    <option value="3months" :selected="period === '3months'">Il y a 3 mois</option>
                </select>
            </div>
        </div>

        <!-- Résumé Global -->
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-blue-800 mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Résumé Global
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-blue-50 rounded-lg p-4 transition-all duration-200 hover:shadow-md">
                            <p class="text-sm text-blue-600 font-medium">Total Produits Reçus</p>
                            <p class="text-2xl font-bold text-blue-800">{{ number_format($totalProducts) }} unités</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 transition-all duration-200 hover:shadow-md">
                            <p class="text-sm text-green-600 font-medium">Total Produits Vendus</p>
                            <p class="text-2xl font-bold text-green-800">{{ number_format($totalSold) }} unités</p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4 transition-all duration-200 hover:shadow-md">
                            <p class="text-sm text-blue-600 font-medium">Total Coût</p>
                            <p class="text-2xl font-bold text-blue-800">{{ number_format($totalCost, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 transition-all duration-200 hover:shadow-md">
                            <p class="text-sm text-green-600 font-medium">Total Revenu</p>
                            <p class="text-2xl font-bold text-green-800">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 transition-all duration-200 hover:shadow-md">
                            <p class="text-sm text-red-600 font-medium">Total Pertes</p>
                            <p class="text-2xl font-bold text-red-800">{{ number_format($totalLosses, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables avec Tabs -->
        <div x-data="{ activeTab: 'products' }" class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button @click="activeTab = 'products'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'products',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'products'}"
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        Détails par Produit
                    </button>
                    <button @click="activeTab = 'daily'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'daily',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'daily'}"
                            class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        Détails Quotidiens
                    </button>
                </nav>
            </div>

            <!-- Table Produits -->
            <div x-show="activeTab === 'products'" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Produit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Qté Reçue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Qté Vendue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Qté Invendue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Total Reçu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Total Vendu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Manquants</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($stats as $stat)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stat['nom'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['quantite_recue'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['quantite_vendue'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $stat['quantite_invendu'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($stat['total_recu'], 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ number_format($stat['total_vendu'], 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($stat['ttavarie'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Table Détails Quotidiens -->
            <div x-show="activeTab === 'daily'" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Produits Reçus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Produits Vendus</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Produits Avariés</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-800 uppercase tracking-wider">Manquants</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($dailyStats as $date => $details)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $date }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ implode(', ', $details['recus']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ implode(', ', $details['vendus']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ implode(', ', $details['avarie']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">{{ number_format($details['manquants'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
