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

// Add sale form
document.getElementById('addSaleBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Enregistrer une vente',
        html: `
            <div class="form-group">
                <label>Produit</label>
                <select id="productSelect" class="swal2-input">
                    <option>Pain traditionnel</option>
                    <option>Croissant</option>
                    <option>Gâteau</option>
                </select>
            </div>
            <div class="form-group">
                <label>Quantité</label>
                <input type="number" id="quantityInput" class="swal2-input" min="1">
            </div>
            <div class="form-group">
                <label>Prix unitaire (FCFA)</label>
                <input type="number" id="priceInput" class="swal2-input" min="0">
            </div>
        `,
        confirmButtonText: 'Enregistrer',
        showCancelButton: true,
        cancelButtonText: 'Annuler',
        preConfirm: () => {
            return {
                product: document.getElementById('productSelect').value,
                quantity: document.getElementById('quantityInput').value,
                price: document.getElementById('priceInput').value
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Vente enregistrée!', '', 'success');
        }
    });
});

function editSale(saleId) {
    // Similar to add sale form but pre-filled
    Swal.fire({
        title: 'Modifier la vente',
        html: `
            <div class="form-group">
                <label>Quantité</label>
                <input type="number" id="editQuantity" class="swal2-input" value="20">
            </div>
            <div class="form-group">
                <label>Prix total (FCFA)</label>
                <input type="number" id="editPrice" class="swal2-input" value="10000">
            </div>
        `,
        confirmButtonText: 'Mettre à jour',
        showCancelButton: true,
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Vente mise à jour!', '', 'success');
        }
    });
}

// Initialize any other features
document.addEventListener('DOMContentLoaded', function() {
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
