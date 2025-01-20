
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
            <h3>Pointage</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-clipboard-text"></i>
                    <a href="{{route('pointeur-ajouterProduit_recu')}}" data-url="{{route('pointeur-ajouterProduit_recu')}}" style=color:white;> Produits reçus</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-file-document"></i>
                    <a href="{{route('pointeur-afficheProduit_recu')}}" class="load-content" data-url="{{route('pointeur-afficheProduit_recu')}}"style=color:white;>Liste des Produits</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-clipboard-text"></i>
                    <a href="{{route('valider-commandes')}}" class="load-content" data-url="{{route('valider-commandes')}}"style=color:white;>Valider Commandes</a>
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Général</h3>
            <ul class="menu-items">
                <li class="menu-item">
                    <i class="mdi mdi-chart-bar"></i>
                   <a href="{{route('statistique')}}"style=color:white;> Statistiques</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-chart-bar"></i>
                    <a href="{{route('classement')}}" class="load-content" data-url="{{route('classement')}}"style=color:white;>Voir Classement</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-alert-circle"></i>
                    <a href="{{route('manquant')}}" class="load-content" data-url="{{route('manquant')}}"style=color:white;>Manquant</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-clock-check"></i>
                    <a href="{{route('horaire.index')}}"  data-url="{{route('horaire.index')}}"style=color:white;>Pointage</a>
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
                    <div class="role">Pointeur</div>
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
</body>
</html>