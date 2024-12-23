@include('pages/pdg/pdg_default')
@vite(['resources/css/pdg/pdg_dashboard.css','resources/js/pdg/pdg_dashboard.js'])

<html><head><base href="/" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
</head>
<body>
        <div class="main-content">
        <div class="header">
            <h1>Tableau de bord PDG</h1>
            <div class="header-actions">
                <button class="action-button" onclick="generateStrategicReport()">
                    <i class="mdi mdi-file-chart"></i> Rapport Stratégique
                </button>
                <button class="action-button" onclick="showCompanyOverview()">
                    <i class="mdi mdi-view-dashboard"></i> Vue d'ensemble
                </button>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Performance Globale</h3>
                <p class="stat-value">95.2%</p>
                <span class="stat-trend positive">+5.2% vs année précédente</span>
            </div>
            <div class="stat-card">
                <h3>Part de Marché</h3>
                <p class="stat-value">32%</p>
                <span class="stat-trend positive">+3% vs trimestre précédent</span>
            </div>
            <div class="stat-card">
                <h3>Valeur Entreprise</h3>
                <p class="stat-value">125M FCFA</p>
                <span class="stat-trend positive">+18% croissance annuelle</span>
            </div>
            <div class="stat-card">
                <h3>ROI Global</h3>
                <p class="stat-value">28%</p>
                <span class="stat-trend positive">+2.5% vs objectif</span>
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
        </body></html>
