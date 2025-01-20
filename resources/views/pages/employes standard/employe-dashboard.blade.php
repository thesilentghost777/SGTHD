@include('pages/employes standard/employe-default')

<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title>Tableau de Bord</title>
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
         <h2>Selectioner une option pour commencer</h2>
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

</script>
</body>
</html>
