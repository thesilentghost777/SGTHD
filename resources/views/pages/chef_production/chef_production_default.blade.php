@vite(['resources/css/chef_production/chef_production_default.css','resources/js/chef_production/chef_production_default.js'])
<html><head><base href="/" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

<style>

</style>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo-container">
            <h1>TH MARKET</h1>
            <span>Chef de Production</span>
        </div>
        
        <div class="menu-section">
            <h3>Production</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-factory"></i>
                    Production du jour
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-clipboard-list"></i>
                    Plans de production
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-account-group"></i>
                    Équipes de production
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Gestion</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-package-variant"></i>
                    Stock matières premières
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-trending-down"></i>
                    Pertes/Gaspillage
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-chart-line"></i>
                    Rendement
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Personnel</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-account-group"></i>
                    Gestion employés
                    <span class="notification-badge">3</span>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-account-school"></i>
                    Gestion stagiaires
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-calendar"></i>
                    Planning & repos
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-star"></i>
                    Évaluation employés
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Administration</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-file-document"></i>
                    Rapports
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-cash-register"></i>
                    Versements
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-truck-delivery"></i>
                    Livraisons
                </li>
            </ul>
        </div>

        <div class="profile-section">
            <div class="profile-info">
                <div class="profile-avatar">
                    <i class="mdi mdi-account"></i>
                </div>
                <div class="user-details">
                    <div class="name">Jane Doe</div>
                    <div class="role">Chef de Production</div>
                </div>
            </div>
        </div>
    </aside>

  
</div>


</body></html>