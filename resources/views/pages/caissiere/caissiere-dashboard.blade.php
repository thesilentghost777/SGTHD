@include('pages/caissiere/caissiere-default')
<link rel="stylesheet" href="{{asset('css/glace/glace_dashboard.css')}}">
<main class="main-content">
<div id="dynamic-content">
        <div class="header">
            <h1>Tableau de bord Caissier(e)</h1>
        </div>
        <div class="stats-container">
            <div class="stat-card">
                <h3>Versements effectués en fin de journée</h3>
                @if($versement == 0)
                <p style="color: #ff4d4d; font-weight: bold;">Aucun versement n'a été effectué aujourd'hui.</p>
                @else
                <p><strong>{{ number_format($versement, 0, ',', ' ') }} FCFA</strong></p>
                    @endif

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

</script>
    </body>
    </html>