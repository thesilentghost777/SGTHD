
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrer une vente</title>
    <link rel="stylesheet" href="{{asset('css/serveur/serveur-vendu.css')}}">
    <script>
        // Préremplir le champ "Prix" en fonction du produit sélectionné
        function updatePrice(prices) {
            const selectProduit = document.querySelector('select[name="produit"]');
            const inputPrix = document.querySelector('input[name="prix"]');

            selectProduit.addEventListener('change', function () {
                const selectedCode = this.value;
                inputPrix.value = prices[selectedCode] || '';
            });
        }
        
    </script>
    

</head>
<body>
    <h1>Enregistrer une Vente</h1>
    @if (session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif
    <div id="form-container">
        @if ($errors->any())
            <div class="error-messages">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('error'))
            <div class="error-messages">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('saveProduit_vendu') }}" method="POST">
            @csrf
            @method('POST')

            <div class="form-group">
                <label for="produit">Produit :</label>
                <select name="produit" id="produit" required>
                    <option value="">Sélectionner un produit</option>
                    @foreach($produitR as $all_product)
                        <option value="{{ $all_product->code_produit }}">{{ $all_product->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="quantite">Quantité :</label>
                <input type="number" name="quantite" id="quantite" required>
            </div>

            <div class="form-group">
                <label for="prix">Prix :</label>
                
                <input type="number" name="prix" id="prix"   required>
               
              </div>

            <div class="form-group">
                <label for="type">Type de l'Opération :</label>
                <select name="type" id="type" required>
                    <option value="">Sélectionner une opération</option>
                    <option value="Vente">Vente</option>
                    <option value="Invendu">Produit Invendu</option>
                </select>
            </div>

            <div class="form-group">
                <label for="monnaie">Monnaie Reçue :</label>
                <select name="monnaie" id="monnaie" required>
                    <option value="">Sélectionner une monnaie</option>
                    <option value="Virement">Virement</option>
                    
                    <option value="Espèce">Espèce</option>
                </select>
            </div>

            <input type="submit" value="Enregistrer la vente">
        </form>

        <footer>
            Date : {{ $heure_actuelle->format('d:m:Y') }}<br>
            Heure : {{ $heure_actuelle->format('H:i:s') }}
        </footer>
    </div>
    <div class="search-container">
    <input type="text" id="searchBar" placeholder="Rechercher un produit..." onkeyup="filterTable()">
</div>
<br>
        <h1><u>Liste des produits Vendus  </u></h1>
   <br> <table border=1 id="productTable">
     <thead>
      <tr>
       <th>Nom du Produit</th>
       <th>Quantite</th>
       <th>Prix</th>
       <th>Date de la vente</th>
       
       <th>Monnaie Recu</th>
      </tr>
     </thead>
     <tbody>
     @foreach($proV as $produit)
      <tr>
      <td>{{$produit->produit}}</td>
      <td>{{$produit->quantite}}</td> 
      <td>{{$produit->prix}}</td> 
      <td>{{$produit->date_vente}}</td> 
     
      <td>{{$produit->monnaie}}</td>
     </tr>
     @endforeach
     </tbody>
    </table>
     

    <script>
        // Passer les prix des produits depuis le backend au script JavaScript
        const productPrices = @json($produitR->pluck('prix', 'code_produit'));
        updatePrice(productPrices);
    </script>
    <br><a href="{{ route('serveur-rapport') }}" class="rapport-link">Voir le Rapport de Vente</a>
</body>
</html>