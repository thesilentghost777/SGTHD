@extends('layouts.app')

@section('content')
<div class="flex min-h-screen" x-data="{ sidebarOpen: false, isChefMode: true }">
    <!-- Mobile Menu Button -->
    <button
        class="lg:hidden p-4 text-white bg-blue-600 fixed z-50 top-4 left-4 rounded-md shadow-md"
        @click="sidebarOpen = !sidebarOpen"
        aria-label="Open menu">
        <i class="mdi mdi-menu text-2xl"></i>
    </button>


    <!-- Content Area -->
    <main class="flex-1 p-3 lg:ml-72">
        <div class="mb-6">
            <h1 class="text-2xl font-bold mb-2">Solde du Chef de Production</h1>
            <p class="text-gray-600">Suivi de l'évolution du solde et des opérations</p>
        </div>

        <!-- Solde Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Solde actuel</h2>
                    <p class="text-4xl font-bold text-green-600 my-2">{{ number_format($solde->montant, 0, ',', ' ') }} FCFA</p>
                    @if ($solde->description)
                        <p class="text-sm mt-2">{{ $solde->description }}</p>
                    @endif
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('solde-cp.ajuster') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="mdi mdi-pencil-outline mr-2"></i> Ajuster le solde
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm">Dépenses totales</h3>
                        <p class="text-2xl font-semibold">
                            {{ number_format($historique->where('type_operation', 'depense')->sum('montant'), 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="mdi mdi-cash-minus text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm">Ajustements</h3>
                        <p class="text-2xl font-semibold">
                            {{ number_format($historique->where('type_operation', 'ajustement')->sum('montant'), 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="mdi mdi-cash-sync text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-gray-500 text-sm">Opérations totales</h3>
                        <p class="text-2xl font-semibold">{{ $historique->count() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="mdi mdi-database text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historique du solde -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold">Historique des opérations</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solde avant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solde après</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($historique as $h)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $h->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($h->type_operation === 'versement')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Versement
                                        </span>
                                    @elseif ($h->type_operation === 'depense')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Dépense
                                        </span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Ajustement
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if ($h->type_operation === 'versement')
                                        <span class="text-green-600">+{{ number_format($h->montant, 0, ',', ' ') }}</span>
                                    @elseif ($h->type_operation === 'depense')
                                        <span class="text-red-600">-{{ number_format($h->montant, 0, ',', ' ') }}</span>
                                    @else
                                        <span class="text-yellow-600">{{ $h->montant > 0 ? '+' : '' }}{{ number_format($h->montant, 0, ',', ' ') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($h->solde_avant, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($h->solde_apres, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $h->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $h->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Aucune opération dans l'historique
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                {{ $historique->links() }}
            </div>
        </div>
    </main>
</div>
@endsection
