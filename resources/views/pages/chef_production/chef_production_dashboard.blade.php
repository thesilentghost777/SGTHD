@include('pages/chef_production/chef_production_default')
@vite(['resources/css/chef_production/chef_production_dashboard.css','resources/js/chef_production/chef_production_dashboard.js'])
<main class="main-content">
        <div class="header">
            <h1>Tableau de bord Chef de Production</h1>
            <button class="action-button" id="addProductionBtn">
                <i class="mdi mdi-plus"></i> Nouvelle production
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
                <h3>Production aujourd'hui</h3>
                <p>2,500 unités</p>
            </div>
            <div class="stat-card">
                <h3>Objectif journalier</h3>
                <p>3,000 unités</p>
            </div>
            <div class="stat-card">
                <h3>Rendement</h3>
                <p>83%</p>
            </div>
            <div class="stat-card">
                <h3>Pertes</h3>
                <p>2%</p>
            </div>
        </div>

        <div class="production-overview">
            <div class="chart-container">
                <canvas id="productionChart"></canvas>
            </div>
        </div>

        <div class="product-list">
            <div class="task-header">
                <h2>Production en cours</h2>
                <span>Aujourd'hui</span>
            </div>
            <div class="product-item">
                <span>Pain traditionnel - En cours (1500/2000 unités)</span>
                <div class="progress-bar">
                    <div class="progress" style="width: 75%"></div>
                </div>
            </div>
            <div class="product-item">
                <span>Croissants - En attente (0/500 unités)</span>
                <div class="progress-bar">
                    <div class="progress" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </main>