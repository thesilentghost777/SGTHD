@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Matières du Complexe</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unité Minimale</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Standard</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Du Complexe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Complexe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($matieres as $matiere)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $matiere->nom }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $matiere->unite_minimale }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ number_format($matiere->prix_par_unite_minimale, 2) }} FCFA</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($matiere->provientDuComplexe())
                                    <span class="flex h-3 w-3 relative">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                    </span>
                                    <span class="ml-1.5 text-sm text-green-600">Oui</span>
                                @else
                                    <span class="flex h-3 w-3 relative">
                                        <span class="relative inline-flex rounded-full h-3 w-3 bg-gray-300"></span>
                                    </span>
                                    <span class="ml-1.5 text-sm text-gray-600">Non</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($matiere->complexe && $matiere->complexe->prix_complexe)
                                <div class="text-sm text-gray-500">{{ number_format($matiere->complexe->prix_complexe, 2) }} FCFA</div>
                            @else
                                <div class="text-sm text-gray-400">Non défini</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('matieres.complexe.toggle', $matiere->id) }}">
                                    @csrf
                                    <button type="submit" class="{{ $matiere->provientDuComplexe() ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white py-1 px-3 rounded">
                                        {{ $matiere->provientDuComplexe() ? 'Retirer' : 'Ajouter' }}
                                    </button>
                                </form>

                                @if($matiere->provientDuComplexe())
                                    <button onclick="togglePrixModal('{{ $matiere->id }}', '{{ $matiere->nom }}', '{{ $matiere->prix_complexe ?? $matiere->prix_par_unite_minimale }}')" class="bg-blue-500 hover:bg-blue-600 text-white py-1 px-3 rounded">
                                        Modifier Prix
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal pour modifier le prix -->
<div id="prixModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Modifier le prix</h3>
            <div class="mt-2 px-7 py-3">
                <form id="prixForm" method="POST" action="">
                    @csrf
                    <div class="mb-4">
                        <label for="prix_complexe" class="block text-sm font-medium text-gray-700">Prix pour le complexe</label>
                        <input type="number" id="prix_complexe" name="prix_complexe" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div class="flex justify-end mt-4">
                        <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">
                            Annuler
                        </button>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePrixModal(id, nom, prix) {
        document.getElementById('modalTitle').innerText = `Modifier le prix de "${nom}"`;
        document.getElementById('prix_complexe').value = prix;
        document.getElementById('prixForm').action = `/matieres/complexe/${id}/prix`;
        document.getElementById('prixModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('prixModal').classList.add('hidden');
    }

    // Fermer la modal si on clique en dehors
    window.onclick = function(event) {
        const modal = document.getElementById('prixModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection
