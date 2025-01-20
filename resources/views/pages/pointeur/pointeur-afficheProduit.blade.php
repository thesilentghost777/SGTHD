<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="{{ asset('css/serveur/serveur-stats.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste de produits</title>
</head>
<body>
<h2>Liste des produits</h2>
<div class="stats-overview bg-light p-4 rounded shadow-sm mb-5">
     <table  class="table table-bordered shadow-sm">
     <thead class="list-group">
      <tr>
       <th class="list-group-item d-flex justify-content-between align-items-center">Code_produit</th>
       <th>Produit</th>
       <th>Prix</th>
       <th>Quantite</th>
      </tr>
     </thead>
     <tbody>
     @foreach($produits as $produit)
      <tr>
      <td>{{$produit->code_produit}}</td>
      <td>{{$produit->nom}}</td> 
      <td>{{$produit->prix}}</td> 
      <td>{{$produit->quantite}}</td> 
     </tr>
     @endforeach
     </tbody> 

</div>



</body>
</html>