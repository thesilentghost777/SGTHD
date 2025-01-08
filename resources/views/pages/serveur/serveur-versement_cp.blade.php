
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
<form action="{{route('save_versement_cp')}}" method="POST">
    @csrf
    @method('POST')
    <div class="form-group">
    Libelle : 
    <select name="libelle">
        <option value="">Selectionner un libelle</option>
        <option value="montant_recu_mat">Montant Recu le Matin</option>
        <option value="montant_verser_soir">Montant verse le soir</option>
 </select>

Montant du Versement :
<input type="number" name="montant" required><br><br>
Choix du chef de Production :
<select name="cp" required>
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