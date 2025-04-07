<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résumé des Matières Premières Assignées</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Styles précédents conservés */
    </style>
</head>
<body class="bg-soft-blue min-h-screen font-sans">
    <div class="container mx-auto px-4 py-8">
        @php
        function convertirUnite($quantite, $unite) {
            // Conversions pour les masses
            $conversionsMasse = [
                'mg' => ['seuil' => 1000, 'unite_superieure' => 'g'],
                'g' => ['seuil' => 1000, 'unite_superieure' => 'kg'],
                'kg' => ['seuil' => 1000, 'unite_superieure' => 't']
            ];

            // Conversions pour les volumes
            $conversionsVolume = [
                'ml' => ['seuil' => 100, 'unite_superieure' => 'dl'],
                'dl' => ['seuil' => 10, 'unite_superieure' => 'l'],
                'l' => ['seuil' => 1000, 'unite_superieure' => 'm³']
            ];

            $groupeConversions = in_array($unite, array_keys($conversionsMasse)) ? $conversionsMasse :
                                 (in_array($unite, array_keys($conversionsVolume)) ? $conversionsVolume : null);

            if ($groupeConversions === null) {
                return [
                    'quantite' => $quantite,
                    'unite' => $unite
                ];
            }

            foreach ($groupeConversions as $uniteActuelle => $config) {
                if ($unite === $uniteActuelle && $quantite >= $config['seuil']) {
                    return [
                        'quantite' => $quantite / $config['seuil'],
                        'unite' => $config['unite_superieure']
                    ];
                }
            }

            return [
                'quantite' => $quantite,
                'unite' => $unite
            ];
        }
        @endphp

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold text-blue-600">Résumé des Matières Premières Assignées</h1>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 border-square font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/>
                        <rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    Tableau de bord
                </a>
                <a href="{{ route('production.chief.workspace') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white border-square font-semibold text-xs uppercase tracking-widest hover:bg-blue-600 transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="m12 19-7-7 7-7"/>
                        <path d="M19 12H5"/>
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        @if(empty($resumeParDate))
            <div class="bg-white border-square shadow-card p-8 text-center">
                <p class="text-gray-500 text-lg">Aucune matière assignée trouvée.</p>
            </div>
        @else
            @php
                $totalGeneral = 0;
            @endphp

            <div class="space-y-10">
                @foreach($resumeParDate as $date => $resumeMatieres)
                    <div class="bg-white border-square shadow-card overflow-hidden">
                        <div class="bg-day-header px-6 py-4 border-b border-light-blue">
                            <h2 class="text-xl font-semibold text-green-700">
                                Assignations du {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                            </h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-header-blue text-white">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Matière</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Producteurs & Détails</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Quantité Totale</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Unité</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Prix Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $totalJour = 0;
                                    @endphp

                                    @foreach($resumeMatieres as $resume)
                                        @php
                                            $quantiteTotaleConvertie = convertirUnite(
                                                $resume['quantite_totale'],
                                                $resume['unite']
                                            );
                                        @endphp

                                        <tr class="hover:bg-soft-green transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $resume['matiere']->nom }}</div>
                                                <div class="text-xs text-gray-500">Référence: {{ $resume['matiere']->reference }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="space-y-3">
                                                    @foreach($resume['details'] as $detail)
                                                        @php
                                                            $quantiteDetailConvertie = convertirUnite(
                                                                $detail['quantite'],
                                                                $detail['unite']
                                                            );
                                                        @endphp
                                                        <div class="text-sm border-l-4 border-blue-300 pl-3 py-1">
                                                            <span class="font-medium text-blue-800">{{ $detail['producteur']->name }}</span>
                                                            <div class="text-xs text-gray-600 mt-1">
                                                                {{ number_format($quantiteDetailConvertie['quantite'], 1, ',', ' ') }} {{ $quantiteDetailConvertie['unite'] }}
                                                                <span class="text-gray-500">({{ number_format($detail['quantite_convertie'], 3, ',', ' ') }} {{ $resume['unite'] }})</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                                {{ number_format($quantiteTotaleConvertie['quantite'], 1, ',', ' ') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $quantiteTotaleConvertie['unite'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-blue-800">{{ number_format($resume['prix_total'], 2, ',', ' ') }} XAF</div>
                                            </td>
                                        </tr>
                                        @php
                                            $totalJour += $resume['prix_total'];
                                            $totalGeneral += $resume['prix_total'];
                                        @endphp
                                    @endforeach

                                    <tr class="bg-gray-50">
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                            Total du jour:
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-lg font-bold text-blue-800">
                                                {{ number_format($totalJour, 2, ',', ' ') }} XAF
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 bg-header-blue text-white border-square shadow-card p-5">
                <div class="text-right">
                    <span class="text-lg font-semibold">Total général: </span>
                    <span class="text-2xl font-bold ml-2">{{ number_format($totalGeneral, 2, ',', ' ') }} XAF</span>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
