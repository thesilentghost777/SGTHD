<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des Produits</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        #chart-container, #line-chart-container,#salesChart-container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        canvas {
            margin-top: 20px;
        }
        #salesChart {
            max-width: 500px;
            margin: 20px auto;
            display: block;
        }
        @media screen and (max-width: 1024px) {
    #chart-container,
    #line-chart-container,
    #salesChart-container {
        width: 90%;
        padding: 15px;
    }

    #salesChart {
        max-width: 400px;
    }

    h1 {
        font-size: 2rem;
    }
}

/* Écrans petits (mobiles entre 480px et 768px) */
@media screen and (max-width: 768px) {
    #chart-container,
    #line-chart-container,
    #salesChart-container {
        width: 95%;
        padding: 10px;
    }

    #salesChart {
        max-width: 100%;
        margin: 10px auto;
    }

    h1 {
        font-size: 1.8rem;
    }

    canvas {
        margin-top: 10px;
    }
}

/* Écrans très petits (mobiles < 480px) */
@media screen and (max-width: 480px) {
    #chart-container,
    #line-chart-container,
    #salesChart-container {
        width: 100%;
        padding: 8px;
    }

    h1 {
        font-size: 1.6rem;
    }

    #salesChart {
        max-width: 100%;
    }

    canvas {
        margin-top: 8px;
    }
}
    </style>
</head>

<body>
    <h1>Statistiques des Produits Vendus et Invendus</h1>

    <!-- Graphique en courbe -->
    <div id="line-chart-container">
        <h2>Évolution des Ventes par Jour</h2>
        <canvas id="lineChart" width="400" height="200"></canvas>
    </div>

    <!-- Graphique circulaire -->
     <div id="salesChart-container">
    <h2>Diagramme Circulaire des Ventes Mensuelles</h2>
    <canvas id="salesChart" width="400" height="400"></canvas>
    </div>
    <!-- Graphique en barres -->
    <div id="chart-container">
        <h2>Produits Vendus et Invendus</h2>
        <canvas id="myChart" width="400" height="200"></canvas>
    </div>

    <script>
        // Graphique en barres
        function createBarChart() {
            const produits = @json($produits);
            const labels = produits.map(produit => produit.produit_nom);
            const produitsVendus = produits.map(produit => produit.total_quantite_vendu);
            const produitsInvendus = produits.map(produit => produit.total_quantite_invendu);

            const barCtx = document.getElementById('myChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Produits Vendus',
                            data: produitsVendus,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Produits Invendus',
                            data: produitsInvendus,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: { ticks: { color: '#333' } },
                        y: { beginAtZero: true, ticks: { color: '#333' } }
                    }
                }
            });
        }

        // Graphique en courbe
        function createLineChart() {
            const ventesParJour = @json($ventesParJour);
            const dates = ventesParJour.map(vente => vente.date);
            const ventes = ventesParJour.map(vente => vente.ventes);
            const invendus = ventesParJour.map(vente => vente.invendus);

            const lineCtx = document.getElementById('lineChart').getContext('2d');
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Ventes',
                            data: ventes,
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            pointBackgroundColor: 'rgba(34, 197, 94, 1)'
                        },
                        {
                            label: 'Invendus',
                            data: invendus,
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            pointBackgroundColor: 'rgba(239, 68, 68, 1)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: { ticks: { color: '#333' } },
                        y: { beginAtZero: true, ticks: { color: '#333' } }
                    }
                }
            });
        }

        // Graphique circulaire
        window.onload = function () {
    function createPieChart() {
        const productNames = @json($productNames);
        const productSales = @json($productSales);

        console.log("Product Names:", productNames);
        console.log("Product Sales:", productSales);

        // Calcul du total des ventes
        const totalSales = productSales.reduce((acc, val) => acc + Number(val), 0);
        const percentages = productSales.map(value => ((Number(value) / totalSales) * 100).toFixed(2));


        console.log("Percentages:", percentages);

        const ctx = document.getElementById('salesChart').getContext('2d');
        if (!ctx) {
            console.error("Canvas element not found!");
            return;
        }

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: productNames,
                datasets: [{
                    data: percentages,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.raw + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    createPieChart();
};


        // Initialiser les graphiques
        createBarChart();
        createLineChart();
    </script>
</body>

</html>
