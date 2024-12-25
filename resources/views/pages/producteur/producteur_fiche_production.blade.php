<html>
    <head>
        <base href="https://example.com">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
@vite(['resources/css/producteur/producteur_fiche_production.css','resources/js/producteur/producteur_fiche_production.js'])

<body>

<!-- Fiche de production - Vue Blade -->
<div class="rapport-container" id="rapport">
    <div class="rapport-header">
        <h1>RAPPORT DE PRODUCTION</h1>
        <div class="info-entreprise">
            <h3>COMPLEXE TH DAMAS</h3>
            <p>Rapport généré le {{ date('d/m/Y') }}</p>
        </div>
    </div>

    <div class="info-producteur">
        <h2>Informations du Producteur</h2>
        <p><strong>Nom Complet :</strong> {{ $nom }}</p>
        <p><strong>Secteur :</strong> {{ $secteur }}</p>
        <p><strong>Numero de telephone :</strong> {{ $num_tel }}</p>
    </div>

    <div class="periode-rapport">
        <h3>Période du Rapport</h3>
        <p>{{ $mois_actuel }}</p>
        <p>Du {{ \Carbon\Carbon::parse($debut_mois)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($fin_mois)->format('d/m/Y') }}</p>
    </div>

    @foreach($statistiques as $stat)
    <div class="produit-section">
        <h3>{{ $stat['nom_produit'] }}</h3>
        
        <div class="stats-grid">
            <div class="stat-item">
                <h4>Production Totale</h4>
                <p>{{ number_format($stat['quantite_totale']) }} unités</p>
            </div>
            <div class="stat-item">
                <h4>Valeur Totale</h4>
                <p>{{ number_format($stat['valeur_totale'], 2) }}XAF</p>
            </div>
            <div class="stat-item">
                <h4>Moyenne Journalière</h4>
                <p>{{ number_format($stat['moyenne_journaliere'], 2) }} unités</p>
            </div>
            <div class="stat-item">
                <h4>Production Maximum</h4>
                <p>{{ $stat['production_max']['quantite'] }} unités</p>
                <p>Le {{ \Carbon\Carbon::parse($stat['production_max']['date'])->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="production-chart">
            <canvas id="chart-{{ $stat['code_produit'] }}"></canvas>
        </div>
    </div>
    @endforeach

    <div class="actions-bar">
        <button class="btn" onclick="window.print()">Imprimer</button>
        <button class="btn"><a href="{{ route('producteur_produit') }}">Retourner au dashboard</a></button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statistiques = {!! json_encode($statistiques) !!};
    
    statistiques.forEach(stat => {
        const ctx = document.getElementById(`chart-${stat.code_produit}`).getContext('2d');
        
        const sortedEntries = Object.entries(stat.productions_journalieres)
            .sort(([dateA], [dateB]) => new Date(dateA) - new Date(dateB));
        
        const dates = sortedEntries.map(([date]) => date);
        const productions = sortedEntries.map(([, value]) => value);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates.map(date => new Date(date).toLocaleDateString('fr-FR')),
                datasets: [{
                    label: 'Production Journalière',
                    data: productions,
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: `Production journalière - ${stat.nom_produit}`
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantité produite'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    });
});
</script>

</body>
</html>