@include('pages/pointeur/pointeur_default')

<html>
    <head>
        <link rel="stylesheet" href="{{asset('css/pointeur/pointeur_dashboard.css')}}">
        <script src="{{asset('js/pointeur/pointeur_dashboard.js')}}"></script>
</head>
<body>
<main class="main-content">
@if(session('success'))
    <div class="alert alert-success" style=color:green;>{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div id="dynamic-content">
        <div class="header">
            <h1>Tableau de bord Pointeur</h1>
           
   </div>
        <div class="stats-container">
            
            <div class="stat-card">
                <h3>Produits livrés</h3>
                @if($commande->isEmpty())
                <p style="color: #ff4d4d; font-weight: bold;">Aucun Produit livré aujourd'hui</p>
                @else
                @foreach($commande as $commandes)
                <div class="stat-card">
                    <p>{{ $commandes->nom }} ({{ $commandes->quantite }} unités)</p>
                </div>
            @endforeach
            @endif
            </div>
            <div class="stat-card">
                <h3>Commande en attente</h3>
                @if($attente->isEmpty())
                <p style="color: #ff4d4d; font-weight: bold;">Aucune Commande Pour l'instant</p>
                @else
                @foreach($attente as $attentes)
                <div class="stat-card">
                    <p>{{ $attentes->nom }} ({{ $attentes->quantite }} unités)</p>
                </div>
            @endforeach
            @endif
            </div>
        </div>

        <div class="product-list">
            <div class="task-header">
                <h2>Produits récemment reçus</h2>
                <span>Aujourd'hui</span>
            </div>
            <div class="product-item">
            @if($produits->isEmpty())
                <p style="color: #ff4d4d; font-weight: bold;">Aucun Produit Reçu aujourd'hui</p>
                @else
                @foreach($produits->take(3) as $produit)
                <div class="stat-card">
                    <p>{{ $produit->nom }} ({{ $produit->quantite }} unités)</p>
                    <a href="{{ route('produit.edit', $produit->produit) }}" class="btn-edit">Modifier</a>
                </div>
            @endforeach
            @endif
           </div>
        </div>
</div>   
    </main>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.load-content');

    links.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Empêche la navigation

            const url = this.getAttribute('data-url'); // Récupère l'URL

            // Indicateur de chargement
            document.getElementById('dynamic-content').innerHTML = '<p>Chargement...</p>';

            // Requête AJAX
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('dynamic-content').innerHTML = html;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('dynamic-content').innerHTML = '<p>Erreur lors du chargement du contenu.</p>';
                });
        });
    });
});

    </script>
</body>
</html>