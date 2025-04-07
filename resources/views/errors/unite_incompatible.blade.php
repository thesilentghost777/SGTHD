<!-- resources/views/errors/unite-incompatible.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur de conversion d'unités</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6">
        <!-- En-tête d'erreur -->
        <div class="flex items-center pb-4 mb-6 border-b border-gray-200">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 text-red-600 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-red-600">Erreur de conversion</h1>
        </div>

        <!-- Message d'erreur -->
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
            <p class="font-medium">
                <span class="font-bold">InvalidArgumentException:</span> Les unités source et cible ne sont pas compatibles.
            </p>
        </div>

        <!-- Détails du code -->
        <div class="bg-gray-100 rounded p-4 mb-6 overflow-x-auto">
            <pre class="text-sm font-mono"><code>// Vérification de la compatibilité des bases
if ($this->conversions[$uniteSourceString]['base'] !== $this->conversions[$uniteCibleString]['base']) {
    throw new \InvalidArgumentException("Les unités source et cible ne sont pas compatibles.");
}</code></pre>
        </div>

        <!-- Conseils de résolution -->
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r">
            <p class="font-medium mb-2">
                <span class="font-bold">Conseil:</span> Assurez-vous que les unités que vous essayez de convertir appartiennent à la même catégorie de mesure.
            </p>
            <p class="mb-2">Exemples de conversions valides:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>Kilogramme → Gramme (masse)</li>
                <li>Litre → Millilitre (volume)</li>
                <li>Mètre → Centimètre (longueur)</li>
            </ul>
        </div>

        <!-- Informations de débogage optionnelles (visible uniquement en développement) -->
        @if(config('app.debug'))
        <div class="bg-gray-50 border border-gray-200 rounded p-4 mb-6">
            <p class="font-bold mb-2 text-gray-700">Informations de débogage:</p>
            <div class="text-sm font-mono">
                <p>Unite Source: {{ $uniteSourceString ?? 'Non disponible' }}</p>
                <p>Unite Cible: {{ $uniteCibleString ?? 'Non disponible' }}</p>
            </div>
        </div>
        @endif

        <!-- Bouton de retour -->
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 hover:underline">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retourner à la page précédente
        </a>
    </div>
</body>
</html>
