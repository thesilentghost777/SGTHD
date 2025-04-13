@extends('rapports.layout.rapport')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Statistiques des factures du complexe</h2>

        <form action="{{ route('factures-complexe.statistiques') }}" method="GET" class="flex gap-2 items-center">
            <div>
                <label for="mois" class="block text-sm font-medium text-gray-700">Mois</label>
                <select id="mois" name="mois" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $mois == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $i, 1)->locale('fr_FR')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="annee" class="block text-sm font-medium text-gray-700">Année</label>
                <select id="annee" name="annee" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $annee == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="self-end pb-1">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Filtrer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Résumé général -->
<div class="mb-8 bg-white p-6 rounded-lg border border-gray-200">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Résumé pour {{ $moisName }}</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h4 class="text-md font-semibold text-blue-800 mb-1">Total des factures</h4>
            <p class="text-2xl font-bold text-blue-700">{{ number_format($totalFacturesMois, 2, ',', ' ') }} FCFA</p>
        </div>

        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <h4 class="text-md font-semibold text-green-800 mb-1">Nombre de factures</h4>
            <p class="text-2xl font-bold text-green-700">{{ count($facturesParJour) }}</p>
        </div>

        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <h4 class="text-md font-semibold text-purple-800 mb-1">Moyenne par facture</h4>
            <p class="text-2xl font-bold text-purple-700">
                {{ count($facturesParJour) > 0 ? number_format($totalFacturesMois / count($facturesParJour), 2, ',', ' ') : 0 }} FCFA
            </p>
        </div>
    </div>
</div>

<!-- Graphique par jour -->
<div class="mb-8 bg-white p-6 rounded-lg border border-gray-200">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Montant des factures par jour</h3>

    <div class="h-64">
        <canvas id="facturesParJourChart"></canvas>
    </div>
</div>

<!-- Matières les plus demandées -->
<div class="mb-8 bg-white p-6 rounded-lg border border-gray-200">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Matières les plus demandées</h3>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Matière
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Quantité totale
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Montant total
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        % du total
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($matieresPlusDemandees as $matiere)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $matiere->nom }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm text-gray-900">{{ number_format($matiere->quantite_totale, 3, ',', ' ') }} {{ $matiere->unite }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm font-medium text-gray-900">{{ number_format($matiere->montant_total, 2, ',', ' ') }} FCFA</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm text-gray-900">
                            {{ $totalFacturesMois > 0 ? number_format(($matiere->montant_total / $totalFacturesMois) * 100, 2, ',', ' ') : 0 }}%
                        </div>
                    </td>
                </tr>
                @endforeach

                @if(count($matieresPlusDemandees) === 0)
                <tr>
                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                        Aucune donnée disponible
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Factures par producteur -->
<div class="mb-8 bg-white p-6 rounded-lg border border-gray-200">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Factures par producteur</h3>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Producteur
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nombre de factures
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Montant total
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        % du total
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($facturesParProducteur as $producteur)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $producteur->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm text-gray-900">{{ $producteur->nombre_factures }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm font-medium text-gray-900">{{ number_format($producteur->montant_total, 2, ',', ' ') }} FCFA</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="text-sm text-gray-900">
                            {{ $totalFacturesMois > 0 ? number_format(($producteur->montant_total / $totalFacturesMois) * 100, 2, ',', ' ') : 0 }}%
                        </div>
                    </td>
                </tr>
                @endforeach

                @if(count($facturesParProducteur) === 0)
                <tr>
                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                        Aucune donnée disponible
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique par jour
    const facturesParJour = @json($facturesParJour);
    const labels = facturesParJour.map(f => f.date);
    const data = facturesParJour.map(f => f.total);

    // Graphique des factures par jour
    const ctx = document.getElementById('facturesParJourChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Montant des factures (FCFA)',
                data: data,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
