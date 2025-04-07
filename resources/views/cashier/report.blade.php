@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Rapports de Caisse</h1>
        <a href="{{ route('cashier.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="mdi mdi-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <!-- Filtres de période -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Sélectionner la période</h2>
        <form action="{{ route('cashier.reports') }}" method="GET" class="flex flex-col sm:flex-row sm:items-end space-y-4 sm:space-y-0 sm:space-x-4">
            <div class="flex-1">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div class="flex-1">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                    <i class="mdi mdi-filter-outline mr-2"></i>Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Résumé des statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="rounded-full p-3 bg-blue-100 mr-4">
                        <i class="mdi mdi-store text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Sessions de caisse</p>
                        <p class="text-2xl font-bold">{{ $statistics['total_sessions'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="rounded-full p-3 bg-green-100 mr-4">
                        <i class="mdi mdi-cash-multiple text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total caisse manipulé</p>
                        <p class="text-2xl font-bold">{{ number_format($statistics['total_cash_handled'], 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="rounded-full p-3 bg-yellow-100 mr-4">
                        <i class="mdi mdi-cash-register text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total versé</p>
                        <p class="text-2xl font-bold">{{ number_format($statistics['total_remitted'], 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="rounded-full p-3 bg-red-100 mr-4">
                        <i class="mdi mdi-cash-remove text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total retraits</p>
                        <p class="text-2xl font-bold">{{ number_format($statistics['total_withdrawals'], 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Liste des sessions -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Sessions de la période</h2>

            @if(count($sessions) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 text-left border-b">Date</th>
                                <th class="py-3 px-4 text-left border-b">Durée</th>
                                <th class="py-3 px-4 text-right border-b">Caisse Initiale</th>
                                <th class="py-3 px-4 text-right border-b">Caisse Finale</th>
                                <th class="py-3 px-4 text-right border-b">Montant Versé</th>
                                <th class="py-3 px-4 text-right border-b">Retraits</th>
                                <th class="py-3 px-4 text-center border-b">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 border-b">
                                        {{ $session->start_time->format('d/m/Y') }}<br>
                                        <span class="text-xs text-gray-500">{{ $session->start_time->format('H:i') }} - {{ $session->end_time ? $session->end_time->format('H:i') : 'En cours' }}</span>
                                    </td>
                                    <td class="py-3 px-4 border-b">{{ $session->duration }}</td>
                                    <td class="py-3 px-4 text-right border-b">{{ number_format($session->initial_cash, 0, ',', ' ') }} FCFA</td>
                                    <td class="py-3 px-4 text-right border-b">
                                        @if($session->end_time)
                                            {{ number_format($session->final_cash, 0, ',', ' ') }} FCFA
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right border-b">
                                        @if($session->end_time)
                                            {{ number_format($session->cash_remitted, 0, ',', ' ') }} FCFA
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right border-b">
                                        {{ number_format($session->total_withdrawals, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="py-3 px-4 text-center border-b">
                                        <a href="{{ route('cashier.session', $session->id) }}" class="text-blue-500 hover:text-blue-700">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-gray-50 p-4 rounded text-center">
                    <p class="text-gray-500">Aucune session de caisse trouvée pour cette période.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
