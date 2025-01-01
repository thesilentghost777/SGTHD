@vite(['resources\css\ serveur/serveur-produit_invendu.css','resources\js\ serveur\ serveur-produit_invendu.js'])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrer une vente</title>
</head>
<body>
@if ($errors->any())
@foreach ($errors->all() as $error)
{{ $error }}
@endforeach
@endif @if(session('error'))
{{ session('error') }}
@endif
     <form action="{{route('saveProduit_invendu')}}" method="POST">
        @csrf
        @method('POST')
      Produit :
      <select name="produit">
          <option value="">Selectionner un produit </option>
        @foreach($produitR as $all_product)
        <option value="{{$all_product->code_produit}}">{{$all_product->nom}}</option>
        @endforeach
</select><br><br>
        Quantite :
        <input type="number" name="quantite"><br><br>
        Prix:
        <input type="number" name="prix"><br><br>
        Type de L'operation :
        <select name="type">
          <option value="">Selectionner une operation</option>
          <option value="Vente">Vente</option>
          <option value="Produit invendu">Produit Invendu</option>
</select><br><br>
        <input type="submit" value="Enregistrer un produit invendu"><br>
</form>
date : {{ $heure_actuelle->format('d:m:Y') }} <br>
hour : {{ $heure_actuelle->format('H:i:s') }}
</body>
</html>