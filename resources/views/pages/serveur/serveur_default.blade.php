@vite(['resources/css/serveur/serveur_default.css','resources/js/serveur/serveur_default.js'])
<html><head><base href="/" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<style>
    :root {
    --primary-color: #1e3c72;
    --secondary-color: #2a5298;
    --sidebar-width: 280px;
}

body {
    margin: 0;
    font-family: 'Roboto', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    background: #f5f6fa;
}

.dashboard-container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: var(--sidebar-width);
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 20px;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.logo-container {
    text-align: center;
    padding: 20px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.logo-container h1 {
    margin: 0;
    font-size: 24px;
}

.logo-container span {
    font-size: 12px;
    opacity: 0.7;
}

.menu-section {
    margin-top: 30px;
}

.menu-section h3 {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
    opacity: 0.7;
}

.menu-items {
    list-style: none;
    padding: 0;
}

.menu-item {
    padding: 12px 15px;
    margin: 5px 0;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
    display: flex;
    align-items: center;
}

.menu-item:hover {
    background: rgba(255,255,255,0.1);
}

.menu-item i {
    margin-right: 10px;
}

.main-content {
    flex: 1;
    padding: 30px;
    overflow-y: auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.task-list {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.task {
    padding: 15px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.action-button {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    background: var(--primary-color);
    color: white;
    cursor: pointer;
    transition: transform 0.3s;
}

.action-button:hover {
    transform: translateY(-2px);
}

.profile-section {
    position: relative;
    padding: 20px 15px;
    border-top: 1px solid rgba(255,255,255,0.1);
    margin-top: auto;
}

.profile-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-details {
    font-size: 14px;
}

.user-details .name {
    font-weight: 500;
}

.user-details .role {
    opacity: 0.7;
    font-size: 12px;
}

.product-form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.product-list {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.product-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.product-item:last-child {
    border-bottom: none;
}

.btn-edit {
    background: #4CAF50;
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}

.clock-in-out {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
    text-align: center;
}

.time-display {
    font-size: 2em;
    margin: 10px 0;
    color: var(--primary-color);
}

.notification-badge {
    background: #ff4444;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    margin-left: 5px;
}

.clock-widget {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
}

.clock-time {
    font-size: 2.5em;
    font-weight: bold;
    color: var(--primary-color);
}

.clock-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 15px;
}

.clock-btn {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.clock-in {
    background: #4CAF50;
    color: white;
}

.clock-out {
    background: #f44336;
    color: white;
}

.sales-form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}
</style>
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
                     <a href="{{route('serveur-dashboard')}} " style=color:white;> Produits reçus</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-cash-register"></i>
                    <a href="{{route('serveur-dashboard')}} " style=color:white;>Ventes du jour </a>
                    
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
                    <div class="name">{{$nom}}</div>
                    <div class="role">Serveur(se)</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content">
            </main>
</div>

