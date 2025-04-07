@extends('pages.dg.dg_default')
@section('page-content')
<div class="py-8 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Chiffre d'affaires -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Chiffre d'affaires</h3>
                    <div class="p-2 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-2" style="font-family: 'Inter', sans-serif;">{{ number_format($revenue['current'], 2, ',', ' ') }} XAF</p>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $revenue['growth'] >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                    <span class="mr-1">{{ $revenue['growth'] }}%</span>
                    <span class="text-xs">vs mois dernier</span>
                </div>
            </div>
        </div>

        <!-- Bénéfice net -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Bénéfice net</h3>
                    <div class="p-2 bg-emerald-50 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-extrabold bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent mb-2" style="font-family: 'Inter', sans-serif;">{{ number_format($profit['current'], 2, ',', ' ') }} XAF</p>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $profit['growth'] >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                    <span class="mr-1">{{ $profit['growth'] }}%</span>
                    <span class="text-xs">vs mois dernier</span>
                </div>
            </div>
        </div>

        <!-- Dépenses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Dépenses</h3>
                    <div class="p-2 bg-indigo-50 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9a2 2 0 10-4 0v5a2 2 0 01-2 2h6m-6-4h4m8 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-extrabold bg-gradient-to-r from-red-600 to-red-800 bg-clip-text text-transparent mb-2" style="font-family: 'Inter', sans-serif;">{{ number_format($expenses['current'], 2, ',', ' ') }} XAF</p>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $expenses['growth'] <= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                    <span class="mr-1">{{ $expenses['growth'] }}%</span>
                    <span class="text-xs">vs mois dernier</span>
                </div>
            </div>
        </div>

        <!-- Effectif total -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Effectif total</h3>
                    <div class="p-2 bg-cyan-50 rounded-lg">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-extrabold bg-gradient-to-r from-cyan-600 to-cyan-800 bg-clip-text text-transparent mb-2" style="font-family: 'Inter', sans-serif;">{{ $staff['total'] }}</p>
                <div class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $staff['stability'] === 'Stable' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                    {{ $staff['stability'] }}
                </div>
            </div>
        </div>
    </div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">Évolution du chiffre d'affaires</h3>
            <div class="h-96">
                <canvas id="revenueChart" class="w-full"></canvas>
            </div>
        </div>

        <!-- Pending AS Requests -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">Demandes AS en attente</h3>
            @if($pendingRequests->count() > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($pendingRequests as $request)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-blue-100"></span>
                                    @endif
                                    <div class="relative flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <span class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 flex items-center justify-center ring-4 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 flex justify-between items-center">
                                            <div>
                                                <p class="text-sm text-gray-600">Demande AS de <span class="font-medium text-gray-900">{{ $request->employe->name }}</span></p>
                                            </div>
                                            <div class="text-right text-sm text-gray-500">
                                                <time datetime="{{ $request->created_at }}" class="bg-gray-50 px-3 py-1 rounded-full">{{ $request->created_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucune demande en attente</p>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueData = @json($revenueChart);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: revenueData.map(item => {
            const date = new Date(2024, item.month - 1);
            return date.toLocaleDateString('fr-FR', { month: 'long' });
        }),
        datasets: [{
            label: 'Chiffre d\'affaires',
            data: revenueData.map(item => item.total),
            borderColor: '#3b82f6',
            backgroundColor: '#93c5fd',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: '#f3f4f6'
                },
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('fr-FR', {
                            style: 'currency',
                            currency: 'XAF'
                        }).format(value);
                    }
                }
            },
            x: {
                grid: {
                    color: '#f3f4f6'
                }
            }
        }
    }
});
</script>
@endsection
