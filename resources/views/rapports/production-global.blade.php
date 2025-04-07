@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-r from-blue-50 to-blue-100 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-5xl">
        <!-- En-tête avec navigation et options -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-6">
            <div>
                <h1 class="text-3xl font-bold text-blue-800 tracking-tight">Rapport de Production Mensuel</h1>
                <p class="text-gray-600 mt-2">Période analysée: {{ $moisCourantNom }}</p>
            </div>
            <div class="flex space-x-4">
                <button id="printBtn" style="display: inline-flex; align-items: center; background-color: #1a56db; color: white; font-weight: 500; padding: 10px 16px; border-radius: 8px; border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="height: 20px; width: 20px; margin-right: 8px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span style="font-size: 16px;">Imprimer le rapport</span>
                  </button>
                <a href="{{ route('rapports.select') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg shadow-md transition-colors duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <!-- Contenu du rapport - Section imprimable -->
        <div id="printableArea" class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- En-tête du rapport -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-8 py-6 print:py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-white print:text-xl">Rapport de Production</h2>
                        <p class="text-blue-100">Exercice: {{ $moisCourantNom }}</p>
                    </div>
                    <div class="text-right text-white">
                        <p class="font-semibold">Rapport généré le</p>
                        <p>{{ date('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Contenu principal du rapport -->
            <div class="p-8 print:p-6 space-y-8">
                <!-- Résumé exécutif -->
                <section class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Résumé Exécutif</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        Au cours du mois de <span class="font-semibold">{{ $moisCourantNom }}</span>, la production totale a généré une valeur de
                        <span class="font-semibold text-blue-700">{{ number_format($valeurTotaleProduction, 0, ',', ' ') }} FCFA</span>.
                        Les coûts de matières premières se sont élevés à
                        <span class="font-semibold text-red-700">{{ number_format($coutMatierePremiere, 0, ',', ' ') }} FCFA</span>,
                        dégageant un bénéfice brut estimé à
                        <span class="font-semibold text-green-700">{{ number_format($beneficeBrut, 0, ',', ' ') }} FCFA</span>.
                    </p>

                    <p class="text-gray-700 leading-relaxed">
                        @if($pourcentageEvolution > 0)
                            Par rapport au mois de <span class="font-semibold">{{ $moisPrecedentNom }}</span>, le bénéfice brut a connu une
                            <span class="font-semibold text-green-700">hausse de {{ number_format($pourcentageEvolution, 1) }}%</span>.
                            Cette progression démontre une amélioration significative de la performance de production.
                        @elseif($pourcentageEvolution < 0)
                            Par rapport au mois de <span class="font-semibold">{{ $moisPrecedentNom }}</span>, le bénéfice brut a connu une
                            <span class="font-semibold text-red-700">baisse de {{ abs(number_format($pourcentageEvolution, 1)) }}%</span>.
                            Cette diminution nécessite une analyse approfondie des facteurs sous-jacents.
                        @else
                            Par rapport au mois de <span class="font-semibold">{{ $moisPrecedentNom }}</span>, le bénéfice brut est
                            <span class="font-semibold text-gray-700">resté stable</span>,
                            indiquant une constance dans les performances de production.
                        @endif
                    </p>
                </section>

                <!-- Analyse financière -->
                <section class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Analyse Financière</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-5 border-l-4 border-blue-500">
                            <p class="text-sm text-blue-800 font-medium mb-1">Valeur de production</p>
                            <p class="text-2xl font-bold text-blue-900">{{ number_format($valeurTotaleProduction, 0, ',', ' ') }} FCFA</p>
                        </div>

                        <div class="bg-red-50 rounded-lg p-5 border-l-4 border-red-500">
                            <p class="text-sm text-red-800 font-medium mb-1">Coût matières premières</p>
                            <p class="text-2xl font-bold text-red-900">{{ number_format($coutMatierePremiere, 0, ',', ' ') }} FCFA</p>
                        </div>

                        <div class="bg-green-50 rounded-lg p-5 border-l-4 border-green-500">
                            <p class="text-sm text-green-800 font-medium mb-1">Bénéfice brut</p>
                            <p class="text-2xl font-bold text-green-900">{{ number_format($beneficeBrut, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>

                    <p class="text-gray-700 leading-relaxed">
                        L'analyse financière révèle un taux de rentabilité brute de
                        <span class="font-semibold">{{ number_format(($beneficeBrut / $valeurTotaleProduction) * 100, 1) }}%</span>
                        pour le mois de {{ $moisCourantNom }}. Les coûts de matières premières représentent
                        <span class="font-semibold">{{ number_format(($coutMatierePremiere / $valeurTotaleProduction) * 100, 1) }}%</span>
                        de la valeur totale de production.
                    </p>
                </section>

                <!-- Analyse des produits -->
                <section class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Analyse des Produits</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                        <div>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                L'analyse de la rentabilité par produit montre que
                                @if(count($produitsLabels) > 0)
                                    <span class="font-semibold">{{ $produitsLabels[0] }}</span>
                                    est le produit le plus rentable ce mois-ci, représentant
                                    <span class="font-semibold">{{ number_format(($produitsBenefices[0] / array_sum($produitsBenefices)) * 100, 1) }}%</span>
                                    du bénéfice total.
                                @else
                                    nous n'avons pas de données suffisantes pour déterminer le produit le plus rentable.
                                @endif
                            </p>

                            <p class="text-gray-700 leading-relaxed">
                                Les données montrent que
                                @if(count($produitsLabels) > 1)
                                    {{ count($produitsLabels) }} produits contribuent significativement au bénéfice global,
                                    avec une diversification qui renforce la stabilité de notre production.
                                @else
                                    la diversification de notre production est limitée, ce qui pourrait présenter un risque pour la stabilité de nos revenus.
                                @endif
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="w-full aspect-square max-h-[350px]">
                                <canvas id="pieChart"></canvas>
                            </div>
                            <p class="text-center text-sm text-gray-500 mt-2">Répartition des bénéfices par produit</p>
                        </div>
                    </div>
                </section>

                <!-- Top Producteurs -->
                <section class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Performance des Producteurs</h3>

                    <p class="text-gray-700 leading-relaxed mb-6">
                        @if(count($topProducteurs) > 0)
                            Les performances des producteurs montrent que
                            <span class="font-semibold">{{ $topProducteurs[0]['name'] }}</span>
                            est le producteur le plus performant ce mois-ci, avec un bénéfice généré de
                            <span class="font-semibold text-blue-700">{{ number_format($topProducteurs[0]['benefice'], 0, ',', ' ') }} FCFA</span>,
                            principalement grâce à la production de
                            <span class="font-semibold">{{ $topProducteurs[0]['produit_phare'] }}</span>.
                        @else
                            Nous ne disposons pas de données suffisantes pour évaluer la performance des producteurs ce mois-ci.
                        @endif
                    </p>

                    <div class="space-y-4">
                        @foreach($topProducteurs as $index => $producteur)
                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex items-center gap-4">
                                <div class="
                                    @if($index == 0) text-yellow-800 bg-yellow-100
                                    @elseif($index == 1) text-gray-800 bg-gray-100
                                    @else text-amber-800 bg-amber-100 @endif
                                    h-8 w-8 rounded-full flex items-center justify-center font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-semibold text-gray-900">{{ $producteur['name'] }}</h4>
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                        <p class="text-blue-700 font-medium">{{ number_format($producteur['benefice'], 0, ',', ' ') }} FCFA</p>
                                        <p class="text-sm text-gray-600">Produit phare: {{ $producteur['produit_phare'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>

                <!-- Conclusion -->
                <section>
                    <h3 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Conclusion et Recommandations</h3>

                    <p class="text-gray-700 leading-relaxed mb-4">
                        @if($pourcentageEvolution > 0)
                            Le mois de {{ $moisCourantNom }} a montré une évolution positive de la production, avec une augmentation significative du bénéfice brut de {{ number_format($pourcentageEvolution, 1) }}% par rapport au mois précédent.
                        @elseif($pourcentageEvolution < 0)
                            Le mois de {{ $moisCourantNom }} a montré une évolution préoccupante de la production, avec une diminution du bénéfice brut de {{ abs(number_format($pourcentageEvolution, 1)) }}% par rapport au mois précédent.
                        @else
                            Le mois de {{ $moisCourantNom }} a montré une stabilité dans la production, avec un bénéfice brut équivalent à celui du mois précédent.
                        @endif
                    </p>

                    <p class="text-gray-700 leading-relaxed">
                        Il est recommandé de
                        @if($pourcentageEvolution > 0)
                            continuer sur cette lancée positive en renforçant la production des produits les plus rentables, tout en optimisant davantage les coûts des matières premières pour améliorer encore la marge bénéficiaire.
                        @elseif($pourcentageEvolution < 0)
                            analyser en profondeur les causes de cette baisse et de mettre en place des mesures correctives, notamment en révisant la stratégie de production et en optimisant l'utilisation des matières premières.
                        @else
                            maintenir cette stabilité tout en cherchant à diversifier la gamme de produits pour renforcer la résilience de la production face aux variations du marché.
                        @endif
                    </p>
                </section>
            </div>

            <!-- Pied de page du rapport -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200 print:border-t print:border-gray-300">
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <p>Rapport généré automatiquement - {{ date('d/m/Y H:i') }}</p>
                    <p>Page 1/1</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printableArea, #printableArea * {
        visibility: visible;
    }
    #printableArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .print\:py-4 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    .print\:p-6 {
        padding: 1.5rem;
    }
    .print\:border-t {
        border-top-width: 1px;
    }
    .print\:border-gray-300 {
        border-color: #e5e7eb;
    }
    .print\:text-xl {
        font-size: 1.25rem;
        line-height: 1.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des couleurs pour le diagramme
    const blueColors = [
        '#3B82F6', '#2563EB', '#1D4ED8', '#1E40AF', '#0EA5E9', '#0284C7', '#0369A1',
    ];

    const greenColors = [
        '#10B981', '#059669', '#047857', '#34D399', '#22D3EE', '#06B6D4', '#0891B2',
    ];

    // Création d'une palette combinant bleu et vert
    const colorPalette = [...blueColors, ...greenColors];

    // Récupération du canvas pour le graphique
    const canvas = document.getElementById('pieChart');
    if (!canvas) {
        console.error('Canvas element "pieChart" not found');
        return;
    }

    // Données pour le diagramme
    const data = {
        labels: {!! json_encode($produitsLabels) !!},
        datasets: [{
            data: {!! json_encode($produitsBenefices) !!},
            backgroundColor: colorPalette.slice(0, {!! count($produitsLabels) !!}),
            borderColor: '#FFFFFF',
            borderWidth: 2
        }]
    };

    // Fonctions utilitaires
    function formatNumber(number, decimals = 0, decPoint = ',', thousandsSep = ' ') {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        const n = !isFinite(+number) ? 0 : +number;
        const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
        const sep = (typeof thousandsSep === 'undefined') ? ' ' : thousandsSep;
        const dec = (typeof decPoint === 'undefined') ? '.' : decPoint;

        let s = '';
        const toFixedFix = function(n, prec) {
            const k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };

        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    // Options du graphique
    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    font: {
                        size: 12,
                        weight: 'bold'
                    },
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                titleColor: '#1E40AF',
                bodyColor: '#1F2937',
                bodyFont: {
                    size: 13
                },
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                borderColor: '#E5E7EB',
                borderWidth: 1,
                padding: 12,
                cornerRadius: 8,
                boxPadding: 4,
                callbacks: {
                    label: function(context) {
                        const value = context.raw;
                        const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${context.label}: ${formatNumber(value)} FCFA (${percentage}%)`;
                    }
                }
            }
        },
        animation: {
            animateScale: true,
            animateRotate: true,
            duration: 1500,
            easing: 'easeOutQuart'
        }
    };

    // Création du graphique en utilisant directement le canvas
    try {
        new Chart(canvas, {
            type: 'pie',
            data: data,
            options: options
        });
        console.log('Pie chart initialized successfully');
    } catch (error) {
        console.error('Error initializing chart:', error);
    }

    // Gestion du bouton d'impression
    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
    });
});
</script>
@endpush
@endsection
