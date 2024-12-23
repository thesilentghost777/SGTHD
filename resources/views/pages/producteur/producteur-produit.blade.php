@vite(['resources/css/producteur/producteur-produit.css','resources/js/producteur/producteur-produit.js'])
@include('pages/producteur/pdefault')
<!DOCTYPE html>
<html><head><base href="/" />
<title>
    Formulaire avec Affichage Dynamique
</title>
</head>
<body>
<div id='main_class'>
    <button id="bouton" onclick="afficherFormulaire()"><i class="mdi mdi-plus-circle-outline"> Ajouter produits</i></button>
    <div id="formulaire">
        <form method="POST" action="{{ route('enr_produits') }}">
            @csrf
            <label for="qte">Producteur</label>
            <input type="text" id="producteur" name="producteur" required> 
            <label for="produit">Produit</label>
            <input type="text" id="produit" name="nom" required>
            <label for="qte">Quantité</label>
            <input type="number" id="qte" name="qte" required>

            <label for="prix">Prix</label>
            <input type="number" id="prix" name="prix" required>

            <input type="submit" id="submit" name="submit" value="Envoyer">
        </form>
    </div>
    <div id="afficheur_produit">
        <table id="table_produit" border="1" cellpadding = "5" cellspacing="0">
            <tr>
                <th>nom</th>
                <th>prix</th>
                <th>quantite</th>
            </tr>
            @foreach ($produits as $item)
                <tr>
                    <td>
                        {{ $item->nom }}
                    </td>
                    <td>
                        {{ $item->prix }}
                    </td>
                    <td>
                        {{ $item->quantite }}
                    </td>
                </tr>
        @endforeach
    </table>
    <br>
  </div>
  <div id="datetime">
    date : {{ $heure_actuelle->format('d:m:Y') }} <br>
    hour : {{ $heure_actuelle->format('H:i:s') }}
  </div>

</div>
<script>
    function afficherFormulaire() {
        const formulaire = document.getElementById("formulaire");
        const bouton = document.getElementById("bouton");
        console.log("ok")
        // Animer l'apparition du formulaire
        formulaire.style.display = "block";
        formulaire.style.opacity = "0";
        let opacity = 0;
        const fadeIn = setInterval(() => {
        opacity += 0.1;
        formulaire.style.opacity = opacity;
        if (opacity >= 1) clearInterval(fadeIn);
        }, 50);
        //Animer la disparition du bouton
        bouton.style.display = "none";
        let buttonOpacity = 1;
        const fadeOut = setInterval(() => {
            buttonOpacity -= 0.1;
            bouton.style.opacity = buttonOpacity;
            if (buttonOpacity <= 0) {
             clearInterval(fadeOut);
                bouton.style.display = "none";
            }
        }, 50);
}
</script>
</body>
</html>