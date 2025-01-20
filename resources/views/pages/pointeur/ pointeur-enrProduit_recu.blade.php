

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/serveur/serveur-stats.css') }}">
    <style>
         #formulaire {
    display: none;
    position: relative;
    left: 35em;
    top: 10em;
    max-width: 500px;
    margin: 10px ;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
}

#bouton {
    position: absolute;
    left: 85%;
    display: block;
    margin: 20px auto;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}
#recuperer-bouton{
    position: absolute;
    left: 70%;
    display: block;
    margin: 70px auto;
    padding: 15px 25px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

#bouton,#recuperer-bouton:hover {
    background-color: #45a049;
}

form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

label {
    font-weight: bold;
    margin-bottom: 5px;
}

input[type="text"],
input[type="number"] {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

input[type="submit"] {
    padding: 10px;
    background-color: #008CBA;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

input[type="submit"]:hover {
    background-color: #006F8E;
}
@media screen and (max-width: 768px) {
    #formulaire {
        max-width: 90%;
    }

    #bouton, #recuperer-bouton {
        width: 50%;
        padding: 12px;
        font-size: 10px;
    }

    input[type="text"],
    input[type="number"] {
        font-size: 13px;
    }

    input[type="submit"] {
        font-size: 13px;
        padding: 8px;
    }
}

/* Écrans très petits (mobiles < 480px) */
@media screen and (max-width: 480px) {
    #formulaire {
        max-width: 100%;
    }

    h1 {
        font-size: 1.8rem;
    }

    #bouton, #recuperer-bouton {
        
        right:-500px;
        width: 50%;
        padding: 10px;
        font-size: 10px;
    }

    input[type="text"],
    input[type="number"] {
        font-size: 12px;
    }

    input[type="submit"] {
        font-size: 12px;
        padding: 8px;
    }
}
        </style>
</head>
<body>

<div id='main_class'>
    <button id="bouton" onclick="afficherFormulaire()"><i class="mdi mdi-plus-circle-outline"> Ajouter produits</i></button>
    
    <div id="formulaire">
    <form method="POST" action="{{ route('pointeur-store') }}">
            @csrf                  
            </select><br><br>
            <label for="produit">Produit</label>
            <select id="pointeur" name="produit">
                <option value="">Sélectionnez un produit</option>
                @foreach ($produitR as $product)
                <option value="{{ $product->code_produit}}">{{ $product->nom }}</option>
                @endforeach
                  
            </select><br><br>
            <label for="qte">Quantité</label>
            <input type="number" id="qte" name="quantite" required><br><br>

            <label for="prix">Prix</label>
            <input type="number" id="prix" name="prix" required><br><br>

            <input type="submit" id="submit" name="submit" value="Envoyer">
        </form>
       </div>
        <div id="datetime">
    date : {{ $heure_actuelle->format('d:m:Y') }} <br>
    hour : {{ $heure_actuelle->format('H:i:s') }}
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
        bouton.style.display = "";
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