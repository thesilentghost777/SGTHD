
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrer une vente</title>
    <link rel="stylesheet" href="{{asset('css/serveur/serveur-vendu.css')}}">
</head>
<body>
        <h1>Enregistrer un produit invendu</h1>
@if ($errors->any())
@foreach ($errors->all() as $error)
{{ $error }}
@endforeach
@endif @if(session('error'))
{{ session('error') }}
@endif
<div id="form-container">
     <form action="{{route('saveProduit_invendu')}}" method="POST">
        @csrf
        @method('POST')
        <div class="form-group">
      Produit :
      <select name="produit">
          <option value="">Selectionner un produit </option>
        @foreach($produitR as $all_product)
        <option value="{{$all_product->code_produit}}">{{$all_product->nom}}</option>
        @endforeach
</select><br><br>
</div>
<div class="form-group">
        Quantite :
        <input type="number" name="quantite"><br><br>
        Prix:
        <input type="number" name="prix"><br><br>
</div>
<div class="form-group">
        Type de L'operation :
        <select name="type">
          <option value="">Selectionner une operation</option>
          <option value="Vente">Vente</option>
          <option value="Produit invendu">Produit Invendu</option>
          <option value="Produit Avarie">Produit avarie</option>
</select><br><br>
</div>
        <input type="submit" value="Enregistrer"><br>
</form>
<footer>
Date : {{ $heure_actuelle->format('d:m:Y') }} <br>
Hour : {{ $heure_actuelle->format('H:i:s') }}
</footer>
</div>
</body>
</html>