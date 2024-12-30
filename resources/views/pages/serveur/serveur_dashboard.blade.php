@include('pages/serveur/serveur_default')
@vite(['resources/css/serveur/serveur_dashboard.css','resources/js/serveur/serveur_dashboard.js'])
<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>
    <style>
        .main-content{
    position: absolute;
    top: 100px;
    left: 500px;
}
    </style>
</head>
<body>

<main class="main-content">

<h1>Bienvenue </h1>
<a href="{{route('serveur-ajouterProduit_recu')}}">Ajouter un produit Recu</a>  <a href="{{route('serveur-enrProduit_vendu')}}">Enregistrer une vente</a> <br>
<a href="{{route('serveur-versement')}}">Effectuer un versement</a> <a href="{{route('serveur-produit_invendu')}}">Enregistrer un produit invendu</a> <br>    
<h2>Liste des produits</h2>

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
     <h2>Liste des produits Vendus</h2>
    
   <br><target name="frame"> <table border=1>
     <thead>
      <tr>
       <th>Code_produit</th>
       <th>Quantite</th>
       <th>Prix</th>
       <th>Total de la vente</th>
       <th>Date de la vente</th>
       <th>Type de L'operation</th>
       <th>Monnaie Recu</th>
      </tr>
     </thead>
     <tbody>
     @foreach($proV as $produit)
      <tr>
      <td>{{$produit->produit}}</td>
      <td>{{$produit->quantite}}</td> 
      <td>{{$produit->prix}}</td> 
      <td>{{$produit->total_ventes}}</td> 
      <td>{{$produit->date_vente}}</td> 
      <td>{{$produit->type}}</td>
      <td>{{$produit->monnaie}}</td>
     </tr>
     @endforeach
     </tbody> 
</main>

</body>
</html>
