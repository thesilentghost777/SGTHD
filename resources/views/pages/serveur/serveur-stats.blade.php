@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/serveur/serveur-stats.css') }}">
<div class="container">
    <h1 class="text-center my-4">📊 Statistiques des Produits</h1>

    <!-- Sélecteur de période -->
    <div class="filter-section d-flex justify-content-center align-items-center mb-4">
        <label for="period" class="me-2">Période :</label>
        <select id="period" class="form-select w-auto" onchange="filterStats()">
            <option value="current" {{ $period === 'current' ? 'selected' : '' }}>Ce mois-ci</option>
            <option value="last" {{ $period === 'last' ? 'selected' : '' }}>Le mois dernier</option>
            <option value="3months" {{ $period === '3months' ? 'selected' : '' }}>Il y a 3 mois</option>
        </select>
    </div>

    <!-- Script de filtrage -->
    <script>
        function filterStats() {
            const period = document.getElementById('period').value;
            window.location.href = `/serveur/stats/${period}`;
        }
    </script>

    <!-- Statistiques globales -->
    <div class="stats-overview bg-light p-4 rounded shadow-sm mb-5">
        <h3 class="mb-4">📋 Résumé Global</h3>
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Total Produits Reçus :</strong>
                <span>{{ number_format($totalProducts) }} unités</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Total Produits Vendus :</strong>
                <span>{{ number_format($totalSold) }} unités</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Total Coût :</strong>
                <span>{{ number_format($totalCost, 0, ',', ' ') }} FCFA</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Total Revenu :</strong>
                <span>{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <strong>Total Pertes :</strong>
                <span>{{ number_format($totalLosses, 0, ',', ' ') }} FCFA</span>
            </li>
        </ul>
    </div>

    <!-- Statistiques détaillées -->
    <h2 class="text-center mb-4">📦 Détails par Produit</h2>
    <table class="table table-bordered shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>Produit</th>
                <th>Quantité Reçue</th>
                <th>Quantité Vendue</th>
                <th>Quantité Invendue</th>
                <th>Total Reçu (FCFA)</th>
                <th>Total Vendu (FCFA)</th>
                <th>Manquants (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stats as $stat)
            <tr>
                <td>{{ $stat['nom'] }}</td>
                <td>{{ $stat['quantite_recue'] }}</td>
                <td>{{ $stat['quantite_vendue'] }}</td>
                <td>{{ $stat['quantite_invendu'] }}</td>
                <td>{{ number_format($stat['total_recu'], 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($stat['total_vendu'], 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($stat['ttavarie'], 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Détails quotidiens des ventes -->
    <h2 class="text-center mb-4">🗓️ Détails Quotidiens des Ventes</h2>
    <table class="table table-bordered shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>Date</th>
                <th>Produits Reçus</th>
                <th>Produits Vendus</th>
                <th>Produits Avariés</th>
                <th>Manquants (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dailyStats as $date => $details)
            <tr>
                <td>{{ $date }}</td>
                <td>{{ implode(', ', $details['recus']) }}</td>
                <td>{{ implode(', ', $details['vendus']) }}</td>
                <td>{{ implode(', ', $details['avarie']) }}</td>
                <td>{{ number_format($details['manquants'], 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection