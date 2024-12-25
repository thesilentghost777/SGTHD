document.addEventListener('DOMContentLoaded', function() {
    @foreach($statistiques as $stat)
    new Chart(document.getElementById('chart-{{ $stat['code_produit'] }}').getContext('2d'), {
        type: 'line',
        data: {
            labels: Object.keys({{ json_encode($stat['productions_journalieres']) }}).map(date => {
                return new Date(date).toLocaleDateString()
            }),
            datasets: [{
                label: 'Production Journalière',
                data: Object.values({{ json_encode($stat['productions_journalieres']) }}),
                borderColor: '#1e88e5',
                backgroundColor: 'rgba(30, 136, 229, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    @endforeach
});
