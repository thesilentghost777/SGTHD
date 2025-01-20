<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vente de Glace </title>
    <link rel="stylesheet" href="{{asset('css/serveur/serveur-vendu.css')}}">
</head>
<body>
<h1>Enregistrer une Vente</h1>
<div id="form-container">
<form action="{{ route('glace-store') }}" method="POST">
            @csrf
            @method('POST')
            <div class="form-group">

                <label for="par">Parfum :</label>
                <input type="text" name="parfum" id="par" required>
            </div>

            <div class="form-group">
                <label for="prix">Prix :</label>
                
                <input type="number" name="prix" id="prix"   required>
               
              </div>

            <div class="form-group">
                <label for="type">Type de l'Opération :</label>
                <select name="type" id="type" required>
                    <option value="">Sélectionner une action</option>
                    <option value="Vente_glace">Vente de Glace</option>
                    <option value="">Vente de Produit</option>
                </select>
            </div>

           

            <input type="submit" value="Enregistrer la vente">
        </form>

       
    </div>

</body>
</html>