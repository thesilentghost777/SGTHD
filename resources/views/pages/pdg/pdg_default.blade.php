@vite(['resources/css/pdg/pdg_default.css','resources/js/pdg/pdg_default.js'])
<html><head><base href="/" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo-container">
            <h1>TH MARKET</h1>
            <span>PDG</span>
        </div>
        
        <div class="menu-section">
            <h3>Supervision Générale</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-monitor-dashboard"></i>
                    Tableau de bord global
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-account-group"></i>
                    Gestion des employés
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-account-tie"></i>
                    Chefs de production
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Finances</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-cash"></i>
                    Solde entreprise
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-cash-multiple"></i>
                    Salaires & Primes
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-receipt"></i>
                    Dépenses
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Gestion</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-file-document"></i>
                    Rapports
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-chart-box"></i>
                    Statistiques
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-calendar-check"></i>
                    Événements
                    <span class="notification-badge">2</span>
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Administration Stratégique</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-bank"></i>
                    Vision Globale
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-flag"></i>
                    Objectifs Stratégiques
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-store-plus"></i>
                    Expansion & Développement
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
                    <div class="role">PDG</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        
    </main>
</div>
</body></html>