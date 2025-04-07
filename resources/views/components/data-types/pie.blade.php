<div class="bg-white p-4 rounded-lg shadow">
    <div class="h-96">
        <canvas id="pieChart"></canvas>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Convert Laravel collection to array and encode as JSON
    const results = @json($data);

    // Format the data
    const chartData = results.map(item => ({
        label: item.label,
        value: item.value
    }));

    // Extract labels and values
    const labels = chartData.map(item => item.label);
    const values = chartData.map(item => item.value);

    // Define colors array
    const colors = [
        '#3B82F6', // blue
        '#10B981', // green
        '#8B5CF6', // purple
        '#F59E0B', // yellow
        '#EF4444', // red
        '#EC4899', // pink
        '#14B8A6', // teal
        '#6366F1', // indigo
        '#F97316', // orange
        '#06B6D4'  // cyan
    ];

    // Generate colors function
    const generateColors = (count) => {
        const result = [];
        for (let i = 0; i < count; i++) {
            result.push(colors[i % colors.length]);
        }
        return result;
    };

    // Chart configuration
    const config = {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: generateColors(values.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const sum = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / sum) * 100).toFixed(2); // Correction ici
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    };

    // Create the chart
    const ctx = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx, config);
});
</script>
