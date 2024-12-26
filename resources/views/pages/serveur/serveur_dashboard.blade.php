@include('pages/serveur/serveur_default')
@vite(['resources/css/serveur/serveur_dashboard.css','resources/js/serveur/serveur_dashboard.js'])
<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>
</head>
<body>
<main class="main-content">


<h1>Bienvenue </h1>
    <h2>Liste des produits</h2>
     
    <a href="{{route('serveur-ajouterProduit_recu')}}">Ajouter un produit</a> <br>
   <br> <table border=1>
     <thead>
      <tr>
       <th>Code_produit</th>
       <th>Pointeur</th>
       <th>Produit</th>
       <th>Nom</th>
       <th>Prix</th>
       <th>Quantite</th>
      </tr>
     </thead>
     <tbody>
     @foreach($produits as $produit)
      <tr>
      <td>{{$produit->code_produit}}</td>
      <td>{{$produit->pointeur}}</td>  
      <td>{{$produit->produit}}</td> 
      <td>{{$produit->nom}}</td> 
      <td>{{$produit->quantite}}</td> 
      <td>{{$produit->prix}}</td> 
     </tr>
     @endforeach
     </tbody> 
</main>
</body>
</html>
