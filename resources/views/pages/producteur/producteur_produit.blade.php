@extends('pages.producteur.pdefault')

@section('page-content')
<div class="min-h-screen bg-blue-50 py-8">
    <div class="container mx-auto px-4">
        <div id="main_class">
            <button id="bouton" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow mb-6">
                <a href="{{ route('produitmp') }}" class="flex items-center space-x-2">
                    <i class="mdi mdi-plus-circle-outline"></i>
                    <span>Ajouter produits</span>
                </a>
            </button>

            <!-- En-tête -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Tableau de Production</h1>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-gray-600">{{ $secteur }} - {{ $nom }}</p>
                    <p class="text-gray-600">{{ $heure_actuelle->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Productions du jour -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b-2 border-green-600 pb-2">Productions réalisées aujourd'hui</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($p as $production)
                        <div class="bg-white rounded-lg shadow p-6 border border-green-200">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $production['nom'] }}</h3>
                                <span class="text-green-600 font-semibold">{{ number_format($production['prix']) }} FCFA</span>
                            </div>
                            <div class="space-y-2">
                                <p class="text-gray-600">Quantité produite: {{ $production['quantite'] }}</p>
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Matières premières utilisées:</h4>
                                    <ul class="space-y-1">
                                        @foreach($production['matieres_premieres'] as $matiere)
                                            <li class="text-sm text-gray-600">
                                                {{ $matiere['nom'] }}:
                                                @php
                                                    $quantite = $matiere['quantite'];
                                                    $unite = $matiere['unite'];

                                                    // Conversion rules
                                                    $conversionMapping = [
                                                        'g' => ['kg' => 1000],
                                                        'kg' => ['kg' => 1],
                                                        'ml' => ['litre' => 1000],
                                                        'cl' => ['litre' => 100],
                                                        'dl' => ['litre' => 10],
                                                        'l' => ['litre' => 1],
                                                        'cc' => ['ml' => 5],
                                                        'cs' => ['ml' => 15],
                                                        'pincee' => ['g' => 1.5],
                                                        'unite' => ['unite' => 1],
                                                    ];

                                                    $convertedQuantite = $quantite;
                                                    $convertedUnite = $unite;

                                                    // Perform conversion if applicable
                                                    if (isset($conversionMapping[$unite])) {
                                                        foreach ($conversionMapping[$unite] as $targetUnite => $conversionFactor) {
                                                            if ($convertedQuantite >= $conversionFactor) {
                                                                $convertedQuantite = $convertedQuantite / $conversionFactor;
                                                                $convertedUnite = $targetUnite;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                {{ number_format($convertedQuantite, 2, '.', '') }} {{ $convertedUnite }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        </div>
                    @empty
                        <p class="col-span-3 text-gray-500 text-center py-4">Aucune production enregistrée aujourd'hui</p>
                    @endforelse
                </div>
            </div>

            <!-- Productions attendues -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b-2 border-green-600 pb-2">Productions attendues</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($productions_attendues as $attendue)
                        <div class="bg-white rounded-lg shadow p-6 border border-blue-200">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $attendue['nom'] }}</h3>
                                <span class="px-2 py-1 rounded text-sm {{ $attendue['status'] === 'Terminé' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $attendue['status'] }}
                                </span>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Attendu:</span>
                                    <span class="font-medium">{{ $attendue['quantite_attendue'] }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Produit:</span>
                                    <span class="font-medium">{{ $attendue['quantite_produite'] }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min($attendue['progression'], 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-3 text-gray-500 text-center py-4">Aucune production attendue</p>
                    @endforelse
                </div>
            </div>

            <!-- Productions recommandées -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b-2 border-green-600 pb-2">Productions recommandées pour {{ $day }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($productions_recommandees as $recommandee)
                        <div class="bg-white rounded-lg shadow p-6 border border-green-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $recommandee['nom'] }}</h3>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Quantité recommandée:</span>
                                <span class="font-medium">{{ $recommandee['quantite_recommandee'] }}</span>
                            </div>
                            <div class="mt-2 flex justify-between text-sm">
                                <span class="text-gray-600">Prix unitaire:</span>
                                <span class="font-medium">{{ number_format($recommandee['prix']) }} FCFA</span>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-3 text-gray-500 text-center py-4">Aucune production recommandée pour aujourd'hui</p>
                    @endforelse
                </div>
            </div>

            <!-- Liste des produits disponibles -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b-2 border-green-600 pb-2">Tous les produits disponibles</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($all_produits as $produit)
                        <div class="bg-white rounded-lg shadow p-4 border border-blue-200">
                            <h3 class="font-medium text-gray-900">{{ $produit->nom }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ number_format($produit->prix) }} FCFA</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
