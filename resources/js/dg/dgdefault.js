// Add new DG-specific functions
function generateReport() {
    Swal.fire({
        title: 'Générer un rapport',
        html: `
            <div class="form-group">
                <label>Type de rapport</label>
                <select id="reportType" class="swal2-input">
                    <option value="financial">Rapport financier</option>
                    <option value="production">Rapport de production</option>
                    <option value="employee">Rapport des employés</option>
                    <option value="inventory">Rapport des stocks</option>
                </select>
            </div>
            <div class="form-group">
                <label>Période</label>
                <select id="reportPeriod" class="swal2-input">
                    <option value="day">Journalier</option>
                    <option value="week">Hebdomadaire</option>
                    <option value="month">Mensuel</option>
                    <option value="year">Annuel</option>
                </select>
            </div>
        `,
        confirmButtonText: 'Générer',
        showCancelButton: true,
        cancelButtonText: 'Annuler'
    });
}

function showEmployeeManagement() {
    Swal.fire({
        title: 'Gestion des employés',
        html: `
            <div class="employee-management-tabs">
                <button class="tab-btn active" onclick="switchTab('employees')">Employés</button>
                <button class="tab-btn" onclick="switchTab('production-chiefs')">Chefs production</button>
                <button class="tab-btn" onclick="switchTab('salaries')">Salaires</button>
            </div>
            <div class="employee-list">
                <!-- Employee list will be dynamically populated -->
            </div>
        `,
        width: '80%',
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