<html>
<head>
<title>Valier Commandes sortantes</title>
<link rel="stylesheet" href="{{asset('css/pointeur/pointeur_commandes.css')}}">
</head>
<body>
<div class="container">
    <h1 class="title">📋 Valider Commandes</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="command-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Libellé</th>
                <th>Date de Commande</th>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commandes as $commande)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $commande->libelle }}</td>
                    <td>{{ $commande->date_commande }}</td>
                    <td>{{ $commande->nom }}</td>
                    <td>{{ $commande->quantite }}</td>
                    <td>{{$commande->prix}}</td>
                    <td>
                        <form action="{{ route('valider-commande', $commande->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-validate">Valider</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
