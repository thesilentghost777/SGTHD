<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Vente Mensuel</title>
    <link rel="stylesheet" href="{{ asset('css/serveur/serveur-rapport.css') }}">
</head>
<body>
    <div class="rapport-container">
        <h1>Rapport de Vente Mensuel</h1>
        <h2>Nom complet : {{ $employe->name }}</h2>
        <h3>Date : {{ now()->format('d/m/Y') }}</h3>

        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Date de Vente</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ventes as $vente)
                    <tr>
                        <td>{{ $vente->produit }}</td>
                        <td>{{ $vente->quantite }}</td>
                        <td>{{ number_format($vente->prix, 2) }} FCFA</td>
                        <td>{{ $vente->date_vente }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Recette Totale : {{ number_format($recetteTotale, 2) }} FCFA</h2>

        <button onclick="window.print()">Imprimer le Rapport</button>
    </div>
</body>
</html>
