<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sac de Vente</title>
    <link rel="stylesheet" href="{{asset('css/serveur/serveur-vendu.css')}}">
</head>
<body>
<h1>Sac de  Vente</h1>
<div id="form-container">
 <form action="{{ route('serveur-nbre_sacs') }}" method="POST">
            @csrf
            @method('POST')
            <div class="form-group">
            <label for="quantite">Quantité :</label>
                <input type="number" name="quantite" id="quantite" required>
            Choix du sac :
            <select name="sac">
                <option value="">Selectionner un sac</option>
                <option value="sac">Sac de 100 XAF</option>
                <option value="plastique">Plastique de 50 XAF</option>
            </div>
            <input type="submit" value="Enregistrer">
        </form>
</div>
</body>
</html>