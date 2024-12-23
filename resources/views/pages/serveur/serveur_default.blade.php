@vite(['resources/css/serveur/serveur_default.css','resources/js/serveur/serveur_default.js'])
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
            <span>Powered by SGc</span>
        </div>
        
        <div class="menu-section">
            <h3>Ventes</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-basket"></i>
                    Produits reçus
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-cash-register"></i>
                    Ventes du jour
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-package-variant"></i>
                    Produits invendus
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-currency-usd"></i>
                    Versements
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Général</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-chart-bar"></i>
                    Statistiques
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-alert-circle"></i>
                    Manquants <span class="notification-badge">3</span>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-file-document"></i>
                    Fiche de paie
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Communications</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-help-circle"></i>
                    Réclamer AS
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-message-alert"></i>
                    Plainte privée
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-lightbulb-on"></i>
                    Suggestions
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-alert"></i>
                    Signalements
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
                    <div class="role">Serveuse</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content">
            </main>
</div>

