<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{asset('css/pointeur/pointeur-stats.css')}}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistique du pointeur</title>
</head>
<body>
<div class="stat-container">
    <h2>Classement des manquants</h2>

    {{-- Affichage du produit avec le plus de manquants --}}
    @if($topProduit)
    <div class="top-product">
        @if($topProduit->manquants == 0.00)
            <p>Bravo ! Vous n'avez aucun manquant 🎉</p>
        @else
            <h3>Produit avec le plus de manquants :</h3>
            <p><strong>{{ $topProduit->produit }}</strong> : {{ $topProduit->manquants }} unités manquantes</p>
        @endif
    </div>
@else
    <p>Aucune donnée disponible pour le moment.</p>
@endif


    {{-- Diagramme circulaire --}}
    <div class="chart-container">
        <canvas id="manquantsChart"></canvas>
    </div>
</div>

{{-- Inclure la bibliothèque Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Données du diagramme
        const labels = {!! json_encode($stats->pluck('produit')) !!};
        const data = {!! json_encode($stats->pluck('manquants')) !!};

        // Création du diagramme circulaire
        const ctx = document.getElementById('manquantsChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Manquants',
                    data: data,
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#dc3545',
                        '#ffc107',
                        '#17a2b8'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = ((value / total) * 100).toFixed(2);
                                return `${context.label}: ${value} unités `;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
    
</body>
</html>