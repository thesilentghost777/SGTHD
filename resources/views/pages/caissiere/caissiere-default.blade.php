
<html><head><base href="/" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{asset('css/glace/glace_default.css')}}">
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
            <span>Caissier(e)</span>
        </div>
       
        
        <div class="menu-section">
            <h3>Action</h3>
            <ul class="menu-items">
                <li class="menu-item">
                <i class="mdi mdi-cash-register"></i>
                   <a href="{{route('serveur-versement')}}" class="load-content"data-url="{{route('serveur-versement')}}" style=color:white;>Versements</a>
                </li>
                
                <li class="menu-item">
                <i class="mdi mdi-file-document"></i>
                    <a href="{{route('serveur-fiche_versement')}}"data-url="{{route('serveur-fiche_versement')}}"style=color:white;>Fiche de versement</a>
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <h3>Général</h3>
            <ul class="menu-items">
            <li class="menu-item">
                    <i class="mdi mdi-chart-bar"></i>
                    <a href="{{route('caisse-stat')}}" data-url="{{route('caisse-stat')}}" style=color:white;>Statistiques</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-clock-check"></i>
                    <a href="{{route('horaire.index')}}"  data-url="{{route('horaire.index')}}"style=color:white;>Horaire</a>
                </li>
                <li class="menu-item">
                    <i class="mdi mdi-alert-circle"></i>
                    <a href="{{route('manquant')}}" class="load-content" data-url="{{route('manquant')}}"style=color:white;>Manquant</a>
                </li>
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
                    <div class="role">Caissier(e)</div>
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


</body></html>