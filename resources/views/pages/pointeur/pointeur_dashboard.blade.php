@include('pages/pointeur/pointeur_default')
@vite(['resources/css/pointeur/pointeur_dashboard.css','resources/js/pointeur/pointeur_dashboard.js'])
<html>
    <head>
</head>
<body>
<main class="main-content">
        <div class="header">
            <h1>Tableau de bord Pointeur</h1>
            <button class="action-button" onclick="showProductForm()">
                <i class="mdi mdi-plus"></i> Nouveau produit
            </button>
        </div>

        <div class="clock-in-out">
            <h2>Pointage du jour</h2>
            <div class="time-display" id="currentTime">--:--:--</div>
            <div>
                <button class="action-button" onclick="clockIn()">Arrivée</button>
                <button class="action-button" onclick="clockOut()">Départ</button>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Produits reçus aujourd'hui</h3>
                <p>45 unités</p>
            </div>
            <div class="stat-card">
                <h3>Produits livrés</h3>
                <p>38 unités</p>
            </div>
            <div class="stat-card">
                <h3>En attente</h3>
                <p>7 unités</p>
            </div>
        </div>

        <div class="product-list">
            <div class="task-header">
                <h2>Produits récemment reçus</h2>
                <span>Aujourd'hui</span>
            </div>
            <div class="product-item">
                <span>Baguettes (200 unités)</span>
                <button class="btn-edit">Modifier</button>
            </div>
            <div class="product-item">
                <span>Croissants (50 unités)</span>
                <button class="btn-edit">Modifier</button>
            </div>
            <div class="product-item">
                <span>Gâteaux (15 unités)</span>
                <button class="btn-edit">Modifier</button>
            </div>
        </div>
    </main>
</body>
</html>