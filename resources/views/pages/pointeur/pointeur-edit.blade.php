<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="{{asset('css/pointeur/pointeur-edit.css')}}">
</head>
<body>
    <div class="edit-product-container">
        <h2>Modifier le produit</h2>

        <form action="{{ route('produit.update', $produit->produit) }}" method="POST">
            @csrf

            <label for="nom">Nom du produit :</label>
            <input type="text" id="nom" name="nom" value="{{ $produit->nom }}" readonly>

            <label for="quantite">Quantité :</label>
            <input type="number" id="quantite" name="quantite" value="{{ $produit->quantite }}" required>

            <label for="prix">Prix :</label>
            <input type="number" id="prix" name="prix" value="{{ $produit->prix }}" required>

            <button type="submit" class="btn-save">Enregistrer les modifications</button>
            <a href="{{ route('pointeur-dashboard') }}" class="btn-cancel">Annuler</a>
        </form>
    </div>

    </body>
</html>