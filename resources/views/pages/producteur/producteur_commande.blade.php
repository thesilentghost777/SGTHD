@vite(['resources/css/producteur/producteur_commande.css','resources/js/producteur/producteur_commande.js'])
@include('pages/producteur/pdefault')
<html><head><base href="https://example.com">
</head>
<body>

<!-- producteur_commande.blade.php -->
<div class="container">
    <div class="user-info">
        <h4>Informations producteur</h4>
        <p>Nom: {{ $nom }}</p>
        <p>Secteur: {{ $secteur }}</p>
    </div>

    <div class="section-header">
        <h2>Liste des Commandes</h2>
    </div>

    @if(count($commandes) > 0)
        <table class="commandes-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                    <th>Date de commande</th>
                    <th>Produit</th>
                    <th>Quantite</th>
                </tr>
            </thead>
            <tbody>
                @foreach($commandes as $commande)
                    <tr>
                        <td>{{ $commande->id }}</td>
                        <td>{{ $commande->libelle }}</td>
                        <td>{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y H:i') }}</td>
                        <td>
                        @if($commande->produit) {{ \App\Models\Produit_fixes::where('code_produit', $commande->produit)->first()->nom ?? 'N/A' }} 
                        @else Non spécifié @endif 
                        </td>
                        <td>{{ $commande->quantite }}</td>  
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <p>Aucune commande trouvée pour votre secteur.</p>
        </div>
    @endif
</div>

<script>
// Script pour ajouter des interactions si nécessaire
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.commandes-table tbody tr');
    
    rows.forEach(row => {
        row.addEventListener('click', function() {
            // Vous pouvez ajouter ici une action au clic sur une ligne
            // Par exemple, afficher plus de détails sur la commande
            console.log('Commande sélectionnée:', this.querySelector('td').textContent);
        });
    });
});
</script>

</body></html>