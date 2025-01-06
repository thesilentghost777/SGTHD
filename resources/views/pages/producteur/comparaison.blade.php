@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-4">Comparaison des Producteurs</h1>

        <form action="{{ route('producteur.comparaison') }}" method="GET" class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Critère</label>
                    <select name="critere" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="benefice" {{ request('critere') == 'benefice' ? 'selected' : '' }}>Bénéfice</option>
                        <option value="quantite" {{ request('critere') == 'quantite' ? 'selected' : '' }}>Quantité produite</option>
                        <option value="efficacite" {{ request('critere') == 'efficacite' ? 'selected' : '' }}>Efficacité</option>
                        <option value="diversite" {{ request('critere') == 'diversite' ? 'selected' : '' }}>Diversité des produits</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                    <select name="periode" id="periode" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="jour" {{ request('periode') == 'jour' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="semaine" {{ request('periode') == 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="mois" {{ request('periode') == 'mois' ? 'selected' : '' }}>Ce mois</option>
                        <option value="personnalise" {{ request('periode') == 'personnalise' ? 'selected' : '' }}>Période personnalisée</option>
                    </select>
                </div>

                <div class="date-range {{ request('periode') != 'personnalise' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div class="date-range {{ request('periode') != 'personnalise' ? 'hidden' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>

            <div class="mt-4 flex justify-end space-x-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Filtrer
                </button>
                <button onclick="print_()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Imprimer
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producteur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Secteur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité Totale</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bénéfice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Efficacité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diversité</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($resultats as $index => $resultat)
                <tr class="{{ $index % 2 ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $index + 1 }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $resultat['nom'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $resultat['secteur'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($resultat['stats']['quantite_totale'], 0) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($resultat['stats']['benefice'], 0) }} FCFA
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($resultat['stats']['efficacite'], 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $resultat['stats']['nombre_produits'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function print_() {
    window.print();
}
document.getElementById('periode').addEventListener('change', function() {
    const dateRangeInputs = document.querySelectorAll('.date-range');
    if (this.value === 'personnalise') {
        dateRangeInputs.forEach(input => input.classList.remove('hidden'));
    } else {
        dateRangeInputs.forEach(input => input.classList.add('hidden'));
    }
});
</script>
@endsection
