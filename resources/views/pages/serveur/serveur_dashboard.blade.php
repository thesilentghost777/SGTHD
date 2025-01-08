@include('pages/serveur/serveur_default')

<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>Liste des produits</title>
    <style>
        .main-content{
    position: absolute;
    top: 100px;
    left: 500px;
}
   
</style>

</head>
<body>
        
        <main class="main-content">
        <div id="dynamic-content">
        <h1>Bienvenue <i>{{$nom}}</i></h1>
          <a href="{{route('serveur-enrProduit_vendu')}}">Enregistrer une vente</a> <br>
        <a href="{{route('serveur-versement')}}">Effectuer un versement</a> <a href="{{route('serveur-produit_invendu')}}">Enregistrer un produit invendu</a> <br>    
       

</div>
      
</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const links = document.querySelectorAll('.load-content');

    links.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Empêche la navigation

            const url = this.getAttribute('data-url'); // Récupère l'URL

            // Indicateur de chargement
            document.getElementById('dynamic-content').innerHTML = '<p>Chargement...</p>';

            // Requête AJAX
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('dynamic-content').innerHTML = html;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('dynamic-content').innerHTML = '<p>Erreur lors du chargement du contenu.</p>';
                });
        });
    });
});
function filterTable() {
    console.log("Fonction filterTable appelée"); // Vérifie si la fonction est exécutée
    const searchInput = document.getElementById('searchBar');
    const filter = searchInput.value.toLowerCase();
    console.log("Texte recherché : ", filter); // Vérifie le texte entré

    const table = document.getElementById('productTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const productNameCell = rows[i].getElementsByTagName('td')[0];
        if (productNameCell) {
            const productName = productNameCell.textContent || productNameCell.innerText;
            console.log("Produit trouvé : ", productName); // Vérifie le nom du produit

            rows[i].style.display = productName.toLowerCase().includes(filter) ? '' : 'none';
        }
    }
}

 // Fonction pour ouvrir la fenêtre modale
 function openHelpModal() {
    document.getElementById('helpModal');
}

// Fonction pour fermer la fenêtre modale
function closeHelpModal() {
    document.getElementById('helpModal');
}

// Fermer la fenêtre modale en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('helpModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};

</script>
</body>
</html>
