@include('pages/dg/dgdefault')

@vite(['resources/css/dg/dg-dashboard.css','resources/js/dg/dg-dashboard.js'])
<!DOCTYPE html>
<html><head><base href="/" />
<title>
    Formulaire avec Affichage Dynamique
</title>
</head>
<body>
<div class="main-content">
<div class="stats-container">
            <div class="stat-card">
                <h3>Chiffre d'affaires</h3>
                <p class="stat-value">2,500,000 FCFA</p>
                <span class="stat-trend positive">+15% vs mois dernier</span>
            </div>
            <div class="stat-card">
                <h3>Bénéfice net</h3>
                <p class="stat-value">850,000 FCFA</p>
                <span class="stat-trend positive">+8% vs mois dernier</span>
            </div>
            <div class="stat-card">
                <h3>Dépenses</h3>
                <p class="stat-value">450,000 FCFA</p>
                <span class="stat-trend negative">+12% vs mois dernier</span>
            </div>
            <div class="stat-card">
                <h3>Effectif total</h3>
                <p class="stat-value">45</p>
                <span class="stat-trend neutral">Stable</span>
            </div>
        </div>

        <div class="global-charts">
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Demandes AS en attente</h3>
                <div class="as-requests">
                    <!-- AS requests list -->
                </div>
            </div>
            
            <div class="dashboard-card">
                <h3>Performance chefs de production</h3>
                <div class="performance-ratings">
                    <!-- Performance ratings -->
                </div>
            </div>
</div>
</div>
</body>
</html>