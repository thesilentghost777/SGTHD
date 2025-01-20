<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
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


        </style>
</head>
<body>
    
<h1>Statistiques des Versements par Jour</h1>
<div id="line-chart-container">
        <h2>Évolution des Versements par Jour</h2>
        <canvas id="lineChart" width="400" height="200"></canvas>
    </div>


<script>

            const versementsParJour = @json($versementsParJour);
            const dates = versementsParJour.map(vente => vente.date);
            const versements = versementsParJour.map(vente => vente.versements);
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Versements',
                            data: versements,
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false,
                            pointBackgroundColor: 'rgba(34, 197, 94, 1)'
                        },
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
        
        </script>
</body>
</html>