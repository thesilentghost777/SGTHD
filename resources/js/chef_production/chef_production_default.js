// Update clock
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    document.getElementById('currentTime').textContent = timeString;
}

setInterval(updateClock, 1000);
updateClock();

// Clock in/out functions
function clockIn() {
    const now = new Date();
    Swal.fire({
        title: 'Pointage arrivée',
        text: `Heure d'arrivée enregistrée: ${now.toLocaleTimeString()}`,
        icon: 'success',
        confirmButtonColor: '#4CAF50'
    });
}

function clockOut() {
    const now = new Date();
    Swal.fire({
        title: 'Pointage départ',
        text: `Heure de départ enregistrée: ${now.toLocaleTimeString()}`,
        icon: 'success',
        confirmButtonColor: '#f44336'
    });
}

// Add production form
document.getElementById('addProductionBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Nouvelle production',
        html: `
            <div class="form-group">
                <label>Produit</label>
                <select id="productType" class="swal2-input">
                    <option value="pain">Pain traditionnel</option>
                    <option value="croissant">Croissants</option>
                    <option value="gateau">Gâteaux</option>
                </select>
            </div>
            <div class="form-group">
                <label>Quantité prévue</label>
                <input type="number" id="quantity" class="swal2-input">
            </div>
            <div class="form-group">
                <label>Équipe assignée</label>
                <select id="team" class="swal2-input">
                    <option value="1">Équipe A</option>
                    <option value="2">Équipe B</option>
                    <option value="3">Équipe C</option>
                </select>
            </div>
            <div class="form-group">
                <label>Notes de production</label>
                <textarea id="notes" class="swal2-textarea"></textarea>
            </div>
        `,
        confirmButtonText: 'Démarrer la production',
        showCancelButton: true,
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Production lancée!', '', 'success');
        }
    });
});

// Production chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('productionChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['6h', '8h', '10h', '12h', '14h', '16h', '18h'],
            datasets: [{
                label: 'Production horaire',
                data: [0, 300, 750, 1200, 1500, 1800, 2500],
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
                }
            }
        }
    });

    // Animation for stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 100 * index);
    });
});

// Add new functionality
function showEmployeeModal(action) {
    Swal.fire({
        title: action === 'add' ? 'Ajouter un employé' : 'Modifier un employé',
        html: `
            <div class="form-group">
                <label>Nom complet</label>
                <input type="text" id="employeeName" class="swal2-input">
            </div>
            <div class="form-group">
                <label>Poste</label>
                <select id="employeePosition" class="swal2-input">
                    <option value="production">Production</option>
                    <option value="service">Service</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label>Contact</label>
                <input type="tel" id="employeeContact" class="swal2-input">
            </div>
        `,
        confirmButtonText: action === 'add' ? 'Ajouter' : 'Modifier',
        showCancelButton: true,
        cancelButtonText: 'Annuler'
    });
}

function assignTask() {
    Swal.fire({
        title: 'Assigner une tâche',
        html: `
            <div class="form-group">
                <label>Employé</label>
                <select id="taskEmployee" class="swal2-input">
                    <option value="1">Jean Dupont</option>
                    <option value="2">Marie Martin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Description de la tâche</label>
                <textarea id="taskDescription" class="swal2-textarea"></textarea>
            </div>
            <div class="form-group">
                <label>Priorité</label>
                <select id="taskPriority" class="swal2-input">
                    <option value="high">Haute</option>
                    <option value="medium">Moyenne</option>
                    <option value="low">Basse</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date limite</label>
                <input type="datetime-local" id="taskDeadline" class="swal2-input">
            </div>
        `,
        confirmButtonText: 'Assigner',
        showCancelButton: true,
        cancelButtonText: 'Annuler'
    });
}

// Initialize task board drag and drop
document.addEventListener('DOMContentLoaded', function() {
    const taskColumns = document.querySelectorAll('.task-column');
    taskColumns.forEach(column => {
        new Sortable(column, {
            group: 'tasks',
            animation: 150,
            onEnd: function(evt) {
                // Handle task status update
                console.log('Task moved:', evt.item.dataset.taskId);
            }
        });
    });
});

// Add quick actions menu
document.getElementById('addProductionBtn').addEventListener('click', function(e) {
    const menu = document.querySelector('.action-menu-content');
    menu.classList.toggle('active');
    e.stopPropagation();
});

document.addEventListener('click', function() {
    const menu = document.querySelector('.action-menu-content');
    if(menu.classList.contains('active')) {
        menu.classList.remove('active');
    }
});
