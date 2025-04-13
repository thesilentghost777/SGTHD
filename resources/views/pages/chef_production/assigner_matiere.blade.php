@extends('pages.chef_production.chef_production_default')

@section('page-content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Assigner des Matières Premières</h1>

        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif

        @if($errors->any())
            <x-alert type="error" :message="$errors->first()" />
        @endif

        <!-- Formulaire d'assignation -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <form action="{{ route('chef.commandes.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Producteur
                        </label>
                        <select name="producteur_id" required class="form-select rounded-md shadow-sm border-gray-300 w-full">
                            <option value="">Sélectionner un producteur</option>
                            @foreach($producteurs as $producteur)
                                <option value="{{ $producteur->id }}">{{ $producteur->name }} ({{ ucfirst($producteur->role) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Matière Première
                        </label>
                        <select name="matiere_id" required class="form-select rounded-md shadow-sm border-gray-300 w-full">
                            <option value="">Sélectionner une matière</option>
                            @foreach($matieres as $matiere)
                                <option value="{{ $matiere->id }}">
                                    {{ $matiere->nom }} (Stock: {{ round($matiere->quantite,1) }} unite de  {{ round($matiere->quantite_par_unite,1) }} {{ $matiere->unite_classique }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Quantité à Assigner
                        </label>
                        <input type="number" name="quantite_assignee" step="0.001" required
                               class="form-input rounded-md shadow-sm border-gray-300 w-full" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Unité
                        </label>
                        <x-unite-select name="unite_assignee" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Date Limite d'Utilisation
                        </label>
                        <input type="datetime-local" name="date_limite_utilisation" required
                               class="form-input rounded-md shadow-sm border-gray-300 w-full" />
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Assigner la matière première
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des assignations -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Liste des Assignations</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producteur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Limite</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assignations as $assignation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $assignation->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignation->producteur->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignation->matiere->nom }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ round($assignation->quantite_assignee,1) }} {{ $assignation->unite_assignee }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $assignation->date_limite_utilisation->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openEditModal({{ $assignation->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Modifier
                                    </button>
                                    <form action="{{ route('chef.assignations.destroy', $assignation) }}"
                                          method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette assignation ?')">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de modification -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Modifier l'assignation</h3>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Quantité
                    </label>
                    <input type="number" name="quantite_assignee" step="0.001" required
                           class="form-input rounded-md shadow-sm border-gray-300 w-full" />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Unité
                    </label>
                    <x-unite-select name="unite_assignee" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Date Limite d'Utilisation
                    </label>
                    <div class="flex items-center space-x-2">
                        <input
                            type="datetime-local"
                            name="date_limite_utilisation"
                            required
                            id="date_limite_utilisation"
                            class="form-input rounded-md shadow-sm border-gray-300 w-full"
                        />
                        <button
                            type="button"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            onclick="setTodayEndOfDay()"
                        >
                            Today before 23h59
                        </button>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeEditModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function setTodayEndOfDay() {
    const today = new Date();
    today.setHours(23, 59, 0, 0);

    // Formatage de la date pour l'input datetime-local
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    const hours = String(today.getHours()).padStart(2, '0');
    const minutes = String(today.getMinutes()).padStart(2, '0');

    const formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
    document.getElementById('date_limite_utilisation').value = formattedDateTime;
}

function openEditModal(assignationId) {
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');
    form.action = `/chef/assignations/${assignationId}`;
    modal.classList.remove('hidden');
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.add('hidden');
}
</script>
@endsection
