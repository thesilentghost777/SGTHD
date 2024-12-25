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
            <label for="produit">Produit</label>
            <select id="produit" name="name">
                <option value="">Sélectionnez un produit</option>
                @foreach ($all_produits as $item)
                <option value="{{ $item->nom }}">{{ $item->nom }}</option>
                @endforeach
                <option value="ok">ok</option>   
            </select>
            <label for="qte">Quantité</label>
            <input type="number" id="qte" name="qte" required>

            <label for="prix">Prix</label>
            <input type="number" id="prix" name="prix" required>

            <input type="submit" id="submit" name="submit" value="Envoyer">
        </form>
    </div>
    <div id="Production Journaliere recommander">
  <h2>Production Journaliere recommander(une fois que la tables des produits restants de la journee passer sera creer, soustraire ceux la pour obtenir le vrai result)</h2>
  <h2>Jour {{ $day }}</h2>
    <table id="table_produit_suggerer" border="1" cellpadding = "5" cellspacing="0">
        <tr>
            <th>produit</th>
            <th>prix</th>
            <th>quantite</th>
        </tr>
        @foreach($productions_recommandees as $item)
        <tr>
            <td>
                {{ $item['nom'] }}
            </td>
            <td>
                {{ $item['prix'] }}
            </td>
            <td>
                {{ $item['quantite_recommandee'] }}
            </td>
        </tr>
</table>
    @endforeach
  </div>
    <h2>Production Journaliere</h2>
    <div id="afficheur_produit">
        <table id="table_produit" border="1" cellpadding = "5" cellspacing="0">
            <tr>
                <th>nom</th>
                <th>prix</th>
                <th>quantite</th>
            </tr>
            @foreach ($p as $item)
                <tr>
                    <td>
                        {{ $item['nom'] }}
                    </td>
                    <td>
                        {{ $item['prix'] }}
                    </td>
                    <td>
                        {{ $item['quantite'] }}
                    </td>
                </tr>
        @endforeach
    </table>
    <br>
  </div>
  
  <div id="afficheur_production_attendu">
  <h3>Taches de Production du Jour </h3>
    <table id="table_pa" border="1" cellpadding = "5" cellspacing="0">
            <tr>
                <th>produit</th>
                <th>prix</th>
                <th>quantite attendu(Chef Production)</th>
                <th>quantite deja produite</th>
                <th>progression</th>
                <th>status</th>
            </tr>
    @foreach($productions_attendues as $item)
        <tr>
            <td>
                {{ $item['nom'] }}
            </td>
            <td>
                {{ $item['prix'] }}
            </td>
            <td>
                {{ $item['quantite_attendue'] }}
            </td>
            <td>
                {{ $item['quantite_produite']}}
            </td>
            <td>
                {{ $item['progression']}}%
            </td>
            <td>
                 {{ $item['status'] }}
            </td>
        </tr>
    @endforeach
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