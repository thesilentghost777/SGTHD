<div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6">

    <h1 class="text-3xl font-bold text-white">{{ $title ?? 'Tableau de bord' }}</h1>

    <p class="text-blue-100 mt-2">{{ $description ?? 'Analyse des données' }}</p>

</div>

<div class="p-4">

    <h3 class="text-lg font-semibold text-gray-700 mb-2">Votre question</h3>

    <p class="text-gray-600">{{ $userQuery }}</p>

</div>
