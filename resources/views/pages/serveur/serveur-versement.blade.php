
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Versement</title>
    <link rel="stylesheet" href="{{asset('css/serveur/serveur-vendu.css')}}">
</head>
<body>
    <h1>Effectuer un versement</h1>
    <div id="form-container">
<form action="{{route('save_versement')}}" method="POST">
    @csrf
    @method('POST')
    <div class="form-group">
    Libelle : 
    <input type="text" name="libelle" required><br><br>
Date de Versement :
<input type="date" name="date"><br><br>
Montant du Versement :
<input type="number" name="somme" required><br><br>
Choix de L'encaisseur :
<select name="encaisseur" required>
  <option value=""></option>
   @foreach($versement as $versements)
 <option value="{{$versements->id}}">{{$versements->name}}</option>
@endforeach
</select><br><br>
</div>
<input type="submit" value="Sousmettre"><br><br>
</div>
</body>
</html>