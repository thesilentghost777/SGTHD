@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Roboto:wght@300;400;500&family=Inter:wght@400;500;600&display=swap');

    .production-title {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #1a237e;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 2rem;
        position: relative;
        padding-bottom: 0.5rem;
        text-align: center;
    }

    .production-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 3px;
        background: linear-gradient(90deg, #1a237e, #4caf50);
    }

    .lot-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin: 0 auto 2rem auto; /* Centrage horizontal */
        overflow: hidden;
        transition: transform 0.3s ease;
        max-width: 800px; /* Largeur maximale pour meilleure lisibilité */
    }

    .lot-card:hover {
        transform: translateY(-5px);
    }

    .lot-header {
        background: linear-gradient(135deg, #1a237e, #283593);
        color: white;
        padding: 1.2rem;
        font-family: 'Inter', sans-serif;
        text-align: center;
    }

    .product-section {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .product-name {
        font-family: 'Poppins', sans-serif;
        color: #1a237e;
        font-weight: 500;
        margin-bottom: 1rem;
        text-align: center;
    }

    .materials-list {
        background-color: white;
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin: 0 auto; /* Centrage horizontal */
    }

    .material-item {
        padding: 0.8rem;
        border-bottom: 1px solid #e0e0e0;
        font-family: 'Roboto', sans-serif;
        display: flex;
        justify-content: space-between;
        align-items: center;
        text-align: center;
    }

    .material-item:last-child {
        border-bottom: none;
    }

    .stats-table {
        width: 100%;
        margin: 1.5rem auto 0 auto; /* Centrage horizontal */
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-collapse: collapse;
    }

    .stats-table th,
    .stats-table td {
        padding: 1rem;
        font-family: 'Roboto', sans-serif;
        font-weight: 500;
        text-align: center;
        border: 1px solid #ddd;
    }

    .stats-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #1a237e;
    }

    .value-production {
        color: #1565c0;
        text-align: center;
    }

    .cost-materials {
        color: #2e7d32;
        text-align: center;
    }

    .profit-positive {
        color: #2e7d32;
        font-weight: 600;
        text-align: center;
    }

    .profit-negative {
        color: #c62828;
        font-weight: 600;
        text-align: center;
    }

    /* Container centré */
    .container.py-5 {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2.5rem 15px;
    }
</style>

<div class="container py-5">
    <h1 class="production-title text-center">Productions par Lot</h1>

    @foreach($productionsParLot as $idLot => $production)
    <div class="lot-card card">
        <div class="lot-header">
            <h2 class="h4 mb-0">Lot : {{ $idLot }}</h2>
        </div>

        <div class="card-body">
            <div class="product-section">
                <h3 class="product-name">{{ $production['produit'] }}</h3>
                <p class="mb-0">Quantité produite : {{ number_format($production['quantite_produit'], 2) }}</p>
            </div>

            <div class="materials-list">
                <h4 class="h6 mb-3 text-center">Matières premières utilisées :</h4>
                @foreach($production['matieres'] as $matiere)
                    @php
                        [$convertedQuantity, $convertedUnit] = \App\Services\UnitConverter::convert($matiere['quantite'], $matiere['unite']);
                    @endphp
                    <div class="material-item">
                        <span>{{ $matiere['nom'] }} : {{ number_format($convertedQuantity, 2) }} {{ $convertedUnit }}</span>
                        <span>Coût : {{ number_format($matiere['cout'], 0, ',', ' ') }} XAF</span>
                    </div>
                @endforeach
            </div>

            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Valeur de la production</th>
                        <th>Coût des matières</th>
                        <th>Bénéfice Estimer(brut)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="value-production">{{ number_format($production['valeur_production'], 0, ',', ' ') }} XAF</td>
                        <td class="cost-materials">{{ number_format($production['cout_matieres'], 0, ',', ' ') }} XAF</td>
                        <td class="{{ $production['valeur_production'] - $production['cout_matieres'] > 0 ? 'profit-positive' : 'profit-negative' }}">
                            {{ number_format($production['valeur_production'] - $production['cout_matieres'], 0, ',', ' ') }} XAF
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection
