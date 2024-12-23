// Add new PDG-specific functions
function generateStrategicReport() {
    Swal.fire({
        title: 'Générer un rapport stratégique',
        html: `
            <div class="form-group">
                <label>Type de rapport</label>
                <select id="reportType" class="swal2-input">
                    <option value="global">Performance Globale</option>
                    <option value="market">Analyse du Marché</option>
                    <option value="expansion">Opportunités d'Expansion</option>
                    <option value="competition">Analyse Concurrentielle</option>
                </select>
            </div>
            <div class="form-group">
                <label>Période</label>
                <select id="reportPeriod" class="swal2-input">
                    <option value="quarter">Trimestriel</option>
                    <option value="semester">Semestriel</option>
                    <option value="year">Annuel</option>
                    <option value="custom">Personnalisé</option>
                </select>
            </div>
        `,
        confirmButtonText: 'Générer',
        showCancelButton: true,
        cancelButtonText: 'Annuler'
    });
}

function showCompanyOverview() {
    Swal.fire({
        title: 'Vue d\'ensemble de l\'entreprise',
        html: `
            <div class="company-overview-tabs">
                <button class="tab-btn active" onclick="switchTab('performance')">Performance</button>
                <button class="tab-btn" onclick="switchTab('market-position')">Position Marché</button>
                <button class="tab-btn" onclick="switchTab('growth')">Croissance</button>
            </div>
            <div class="overview-content">
                <!-- Dynamic content based on selected tab -->
            </div>
        `,
        width: '90%',
        showConfirmButton: false
    });
}

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenus',
                data: [1200000, 1500000, 1800000, 1600000, 2200000, 2500000],
                borderColor: '#1e3c72',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Évolution des revenus'
                }
            }
        }
    });
});