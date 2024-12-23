@vite(['resources/css/pointeur/pointeur_default.css','resources/js/pointeur/pointeur_default.js'])
<html><head><base href="/" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo-container">
            <h1>TH MARKET</h1>
            <span>Powered by SGc</span>
        </div>
        
        <div class="menu-section">
            <h3>Pointage</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-clipboard-text"></i>
                    Produits reçus
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-file-document"></i>
                    Liste des produits
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
                    <i class="mdi mdi-clock-check"></i>
                    Pointage
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-file-document-multiple"></i>
                    Fiche de paie
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Communications</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-message-alert"></i>
                    Réclamer AS
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-message-text"></i>
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
                    <div class="name">John Doe</div>
                    <div class="role">Pointeur</div>
                </div>
            </div>
        </div>
    </aside>

    
</div>
</body>
</html>