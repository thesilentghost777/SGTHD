
<html><head><base href="/" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{asset('css/pointeur/pointeur_default.css')}}">
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="logo-container">
            <h1>TH MARKET</h1>
            <span>Powered by SGc</span>
        </div>
        
        

        <div class="menu-section">
            <h3>Général</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-clock-check"></i>
                    <a href="{{route('horaire.index')}}"  data-url="{{route('horaire.index')}}"style=color:white;>Horaires</a>
                </li>
                    <li class="menu-item">
                <a href="{{ route('primes.index') }}"style=color:white; ><i class="mdi mdi-gift mr-2"></i>Primes</a></li>
                <li class="menu-item">
                    <i class="mdi mdi-file-document-multiple"></i>
                    <a href="{{route('fiche-paie.show')}}" class="load-content" data-url="{{route('fiche-paie.show')}}"style=color:white;>Fiche de Paie</a>
                </li>
            </ul>
        </div>
       
        <div class="menu-section">
            <h3>Communications</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-message-alert"></i>
                    <a href="{{route('reclamer-as')}}" class="load-content" data-url="{{route('reclamer-as')}}"style=color:white;>Réclamer AS</a>
                </li>
                <li class="menu-item">
                    
                    <a href="{{ route('validation-retrait') }}"style=color:white; ><i class="mdi mdi-currency-usd mr-2"></i>Retirer Avance Salaire</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-message-text"></i>
                    <a href="{{route('message')}}" class="load-content" data-url="{{route('message')}}"style=color:white;>Plainte privée</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-lightbulb-on"></i>
                    <a href="{{route('message')}}" class="load-content" data-url="{{route('message')}}"style=color:white;>Suggestions</a>
                    
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-alert"></i>
                    <a href="{{route('message')}}" class="load-content" data-url="{{route('message')}}"style=color:white;>Signalements</a>
                    
                </li>
            </ul>
        </div>

        <div class="profile-section">
            <div class="profile-info">
                <div class="profile-avatar">
                    <i class="mdi mdi-account"></i>
                </div>
                <div class="user-details">
                    <div class="name">{{$user->name}}</div>
                    <div class="role">{{$user->secteur}}</div>
                </div>
            </div>
        </div>
        <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="mdi mdi-logout"></i> Déconnexion
        </button>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </aside>
</div>
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