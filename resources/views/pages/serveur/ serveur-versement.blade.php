@vite(['resources\css\ serveur/serveur-versement.css','resources\js\ serveur\ serveur-versement.js'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Versement</title>
</head>
<body>
    
<form action="{{route('save_versement')}}" method="POST">
    @csrf
    @method('POST')
    Libelle : 
    <input type="text" name="libelle" ><br><br>
Date de Versement :
<input type="date" name="date"><br><br>
Montant du Versement :
<input type="number" name="somme"><br><br>
Choix de L'encaisseur :
<select name="encaisseur">
  <option value=""></option>
   @foreach($versement as $versements)
 <option value="{{$versements->id}}">{{$versements->name}}</option>
@endforeach
</select><br><br>
<input type="submit" value="Sousmettre"><br><br>
</body>
</html>