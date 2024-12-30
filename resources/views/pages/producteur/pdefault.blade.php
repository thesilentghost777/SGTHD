@vite(['resources/css/producteur/pdefault.css', 'resources/js/producteur/pdefault.js'])
<html>
    <head>
        <base href="/" />
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
            <h3>Production</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-clipboard-text"></i>
                    <a href="{{ route('producteur_produit')}}">Produits du jour</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-file-document"></i>
                    <a href="{{ route('producteur-fiche_production')}}">Fiche de production</a> 
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-cart"></i>
                    <a href="{{ route('producteur-commande')}}">Commandes</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-archive"></i>
                   >Réservation MP 
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
                    Manquants
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
                    <i class="mdi mdi-message-text"></i>
                    Messages
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
                    <i class="mdi mdi-account-circle"></i>
                </div>
                <div class="user-details">
                    <div class="name">{{ $nom }}</div>
                    <div class="role">{{ $secteur }}</div>
                </div>
            </div>
        </div>
    </aside>



<div id="notification" class="notification"></div>
</body>
</html>
