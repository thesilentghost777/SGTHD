<html><head><base href="/" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/serveur/serveur_default.css') }}">
<style>
  /* ===== Variables Globales ===== */
:root {
    --primary-color: #1e3c72;
    --secondary-color: #2a5298;
    --sidebar-width: 280px;
}

/* ===== Styles Globaux ===== */
body {
    margin: 0;
    font-family: 'Roboto', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    background: #f5f6fa;
}

.dashboard-container {
    display: flex;
    min-height: 100vh;
    flex-direction: row;
}

.sidebar {
    width: var(--sidebar-width);
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 20px;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.main-content {
    flex: 1;
    padding: 30px;
    overflow-y: auto;
}

/* ========== Responsive Design ========== */

/* 📱 Écrans de taille intermédiaire (tablettes) */
@media screen and (max-width: 1024px) {
    .sidebar {
        width: 220px;
    }

    .dashboard-container {
        flex-direction: column;
    }

    .main-content {
        padding: 20px;
    }

    .stats-container {
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
    }
}

/* 📱 Écrans de petite taille (mobiles) */
@media screen and (max-width: 1024px) {
    .sidebar {
        position: absolute;
        width: 100%;
        height: 500%;
        left: -100%;
        transition: left 0.3s ease;
    }

    .sidebar.active {
        left: 0;
    }

    .menu-item {
        font-size: 14px;
        padding: 10px;
    }

    .main-content {
        padding: 15px;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .task {
        flex-direction: column;
        align-items: flex-start;
    }

    .task-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .product-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .clock-widget {
        font-size: 1.5em;
    }
}

/* 📱 Écrans très petits (mobiles < 480px) */
@media screen and (max-width: 480px) {
    .sidebar {
        padding: 15px;
    }

    .logo-container h1 {
        font-size: 20px;
    }

    .menu-item {
        font-size: 12px;
        padding: 8px;
    }

    .form-group input, .form-group select {
        padding: 6px;
    }

    .clock-time {
        font-size: 2em;
    }

    .action-button {
        padding: 6px 12px;
    }

   
}

.logout-btn {
    background-color: #FF6347;
    color: #fff;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}

.logout-btn:hover {
    background-color: #FF4500;
}
.menu-toggle {
    display: none;
    position: absolute;
    top: 20px;
    left: 20px;
    font-size: 24px;
    cursor: pointer;
    color: var(--primary-color);
}

@media screen and (max-width: 768px) {
    .menu-toggle {
        display: block;
    }
}

</style>
</head>
<body>
<div class="menu-toggle" onclick="toggleSidebar()">☰</div>

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
                     <a href="{{route('serveur-ajouterProduit_recu')}}" data-url="{{route('serveur-ajouterProduit_recu')}}" style=color:white;> Produits reçus</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-cash-register"></i>
                    <a href="{{route('serveur-enrProduit_vendu')}}"class="load-content" data-url="{{route('serveur-enrProduit_vendu')}}" style=color:white;>Ventes du jour </a>
                    
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-package-variant"></i>
                  <a href="{{route('serveur-produit_invendu')}}"class="load-content"data-url="{{route('serveur-produit_invendu')}}" style=color:white;> Produits invendus</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-currency-usd"></i>
                   <a href="{{route('serveur-versement')}}" class="load-content"data-url="{{route('serveur-versement')}}" style=color:white;>Versements en Caisse</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-currency-usd"></i>
                   <a href="{{route('serveur-versement_cp')}}" class="load-content"data-url="{{route('serveur-versement_cp')}}" style=color:white;>Versements au CP</a>
                </li>
                <li class="menu-item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 2L18 2L20 6L4 6L6 2Z" fill="#007BFF"/>
                    <path d="M4 6H20V22H4V6Z" fill="#E0E0E0"/> 
                    </svg>
                   <a href="{{route('serveur-nbre_sacs_vente')}}" class="load-content"data-url="{{route('serveur-nbre_sacs_vente')}}" style=color:white;>Sacs Reçu </a>
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Général</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-chart-bar"></i>
                    <a href="{{route('statistiques')}}" data-url="{{route('statistiques')}}" style=color:white;>Statistiques</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-alert-circle"></i>
                    Manquants <span class="notification-badge">2</span>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-currency-usd"></i>
                    Prime
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
            <a href="{{route('aide')}}" class="load-content" data-url="{{route('aide')}}"style=color:white;>Aide</a>
            </li>
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
                    <div class="name">{{$nom}}</div>
                    <div class="role">Serveur(se)</div>
                </div>
            </div>
            <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="mdi mdi-logout"></i> Déconnexion
            </button>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </aside>

    <main class="main-content">
            </main>
</div>
<script>
    function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
}

</script>
