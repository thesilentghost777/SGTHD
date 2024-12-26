@vite(['resources\css\ serveur/serveur-ajouterProduit_recu.css','resources\js\ serveur\ serveur-ajouterProduit_recu.js'])

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form method="POST" action="{{ route('addProduit_recu') }}">
            @csrf
            <label for="pointeur">Pointeur</label>
            <select id="pointeur" name="pointeur">
                <option value="">Sélectionnez un pointeur</option>
                @foreach ($Employe as $employe)
                <option value="{{ $employe->code_employe }}">{{ $employe->nom }}</option>
                @endforeach
                  
            </select><br><br>
            <label for="produit">Produit</label>
            <select id="pointeur" name="produit">
                <option value="">Sélectionnez un produit</option>
                @foreach ($produitR as $product)
                <option value="{{ $product->code_produit}}">{{ $product->nom }}</option>
                @endforeach
                  
            </select><br><br>
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" required><br><br>
            <label for="qte">Quantité</label>
            <input type="number" id="qte" name="quantite" required><br><br>

            <label for="prix">Prix</label>
            <input type="number" id="prix" name="prix" required><br><br>

            <input type="submit" id="submit" name="submit" value="Envoyer">
        </form>
        <div id="datetime">
    date : {{ $heure_actuelle->format('d:m:Y') }} <br>
    hour : {{ $heure_actuelle->format('H:i:s') }}
  </div>
</body>
</html>