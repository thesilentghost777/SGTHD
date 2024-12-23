@include('pages/glace/glace_default')
@vite(['resources/css/glace/glace_dashboard.css','resources/js/glace/glace_dashboard.js'])

<main class="main-content">
        <div class="header">
            <h1>Tableau de bord Vendeuse de Glace</h1>
            <button class="action-button" id="addSaleBtn">
                <i class="mdi mdi-plus"></i> Nouveau versement
            </button>
        </div>

        <div class="clock-widget">
            <div class="clock-time" id="currentTime">--:--:--</div>
            <div class="clock-buttons">
                <button class="clock-btn clock-in" onclick="clockIn()">Pointer arrivée</button>
                <button class="clock-btn clock-out" onclick="clockOut()">Pointer départ</button>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Ventes aujourd'hui</h3>
                <p>35,000 FCFA</p>
            </div>
            <div class="stat-card">
                <h3>Versements effectués</h3>
                <p>30,000 FCFA</p>
            </div>
            <div class="stat-card">
                <h3>Reste à verser</h3>
                <p>5,000 FCFA</p>
            </div>
        </div>

        <div class="product-list">
            <div class="task-header">
                <h2>Dernières ventes</h2>
                <span>Aujourd'hui</span>
            </div>
            <div class="product-item">
                <span>Pain traditionnel (20 unités) - 10,000 FCFA</span>
                <button class="btn-edit" onclick="editSale(1)">Modifier</button>
            </div>
            <div class="product-item">
                <span>Croissants (15 unités) - 7,500 FCFA</span>
                <button class="btn-edit" onclick="editSale(2)">Modifier</button>
            </div>
            <div class="product-item">
                <span>Gâteaux (5 unités) - 25,000 FCFA</span>
                <button class="btn-edit" onclick="editSale(3)">Modifier</button>
            </div>
        </div>
    </main>
    </body></html>