@include('buttons')

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codes des Secteurs</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #000000e6;
            color: #FFFFFF;
        }

        .sector-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border-left: 4px solid #0284c7;
            transition: transform 0.2s ease;
        }

        .sector-card:hover {
            transform: translateY(-2px);
        }

        .table-header {
            background: linear-gradient(90deg, #0284c7 0%, #0ea5e9 100%);
        }

        .table-row:nth-child(even) {
            background-color: #1a1a1a;
        }

        .table-row:hover {
            background-color: #2a2a2a;
        }

        .code-badge {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }

        @media (max-width: 640px) {
            .responsive-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Message de confidentialité -->
        <p class="text-red-600 text-xl font-bold mb-8 p-4 border-2 border-red-600 rounded-lg text-center">
            ATTENTION : Ces codes sont strictement confidentiels et ne doivent en aucun cas être divulgués à des personnes non autorisées.
            Toute diffusion non autorisée est strictement interdite et peut entraîner des sanctions disciplinaires.
        </p>

        <h1 class="text-4xl font-extrabold text-white mb-6 text-center shadow-lg p-4 rounded-lg bg-gray-900">
            Codes secrets pour l'enregistrement du personnel
        </h1>

        <br>


        <!-- Section Alimentation -->
        <div class="sector-card rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-blue-700 mb-4">
                Secteur Alimentation
                <span class="code-badge text-sm text-white px-3 py-1 rounded-full ml-2">
                    Code secteur: 75804
                </span>
            </h2>
            <div class="responsive-table">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="table-header text-white">
                            <th class="px-6 py-3 text-left">Rôle</th>
                            <th class="px-6 py-3 text-left">Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Caissière</td>
                            <td class="px-6 py-4">75804</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Calviste</td>
                            <td class="px-6 py-4">75804</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Rayoniste</td>
                            <td class="px-6 py-4">75804</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Contrôleur</td>
                            <td class="px-6 py-4">75804</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Technicien Surface</td>
                            <td class="px-6 py-4">75804</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Magasinier</td>
                            <td class="px-6 py-4">75804</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Chef Rayoniste</td>
                            <td class="px-6 py-4">75804</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Virgil</td>
                            <td class="px-6 py-4">75804</td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Production -->
        <div class="sector-card rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-blue-700 mb-4">
                Secteur Production
            </h2>
            <div class="responsive-table">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="table-header text-white">
                            <th class="px-6 py-3 text-left">Rôle</th>
                            <th class="px-6 py-3 text-left">Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Pâtissier</td>
                            <td class="px-6 py-4">182736</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Boulanger</td>
                            <td class="px-6 py-4">394857</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Pointeur</td>
                            <td class="px-6 py-4">527194</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Enfourneur</td>
                            <td class="px-6 py-4">639285</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Technicien Surface</td>
                            <td class="px-6 py-4">748196</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Glace -->
        <div class="sector-card rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-blue-700 mb-4">
                Secteur Glace
            </h2>
            <div class="responsive-table">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="table-header text-white">
                            <th class="px-6 py-3 text-left">Rôle</th>
                            <th class="px-6 py-3 text-left">Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Glacière</td>
                            <td class="px-6 py-4">583492</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Administration -->
        <div class="sector-card rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-blue-700 mb-4">
                Secteur Administration
            </h2>
            <div class="responsive-table">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="table-header text-white">
                            <th class="px-6 py-3 text-left">Rôle</th>
                            <th class="px-6 py-3 text-left">Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Chef Production</td>
                            <td class="px-6 py-4">948371</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">DG</td>
                            <td class="px-6 py-4">217634</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">DDG</td>
                            <td class="px-6 py-4">365982</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Gestionnaire Alimentation</td>
                            <td class="px-6 py-4">365982</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">PDG</td>
                            <td class="px-6 py-4">592483</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Vente -->
        <div class="sector-card rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-blue-700 mb-4">
                Secteur Vente
            </h2>
            <div class="responsive-table">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="table-header text-white">
                            <th class="px-6 py-3 text-left">Rôle</th>
                            <th class="px-6 py-3 text-left">Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Vendeur Boulangerie</td>
                            <td class="px-6 py-4">748596</td>
                        </tr>
                        <tr class="table-row border-b">
                            <td class="px-6 py-4 font-medium">Vendeur Pâtisserie</td>
                            <td class="px-6 py-4">983214</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        <!-- ... keep existing code (garder le reste du contenu avec les sections des secteurs,
             mais maintenant avec le nouveau style sombre car nous avons modifié le CSS global) -->
    </div>

</body>
</html>

