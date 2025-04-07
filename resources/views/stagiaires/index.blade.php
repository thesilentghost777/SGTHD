@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Liste des Stagiaires</h2>
                        <a href="{{ route('stagiaires.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Ajouter un stagiaire
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom & Prénom</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">École</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type de Stage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rémunération</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($stagiaires as $stagiaire)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $stagiaire->nom }} {{ $stagiaire->prenom }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $stagiaire->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stagiaire->ecole }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ucfirst($stagiaire->type_stage) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stagiaire->date_debut->format('d/m/Y') }} - {{ $stagiaire->date_fin->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($stagiaire->remuneration, 2) }} XAF
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('stagiaires.edit', $stagiaire) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                            <button onclick="openRemunerationModal({{ $stagiaire->id }})" class="text-green-600 hover:text-green-900">Rémunération</button>
                                            <button onclick="openAppreciationModal({{ $stagiaire->id }})" class="text-blue-600 hover:text-blue-900">Appréciation</button>
                                            <a href="{{ route('stagiaires.report', $stagiaire) }}" class="text-purple-600 hover:text-purple-900">Rapport</a>
                                            <form action="{{ route('stagiaires.destroy', $stagiaire) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce stagiaire ?')">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Rémunération -->
    <div id="remunerationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Définir la rémunération</h3>
                <form id="remunerationForm" method="POST" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <input type="number" name="remuneration" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <div class="mt-4 flex justify-end">
                        <button type="button" onclick="closeRemunerationModal()" class="mr-2 px-4 py-2 bg-gray-300 text-gray-700 rounded">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Valider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Appréciation -->
    <div id="appreciationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Ajouter une appréciation</h3>
                <form id="appreciationForm" method="POST" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <textarea name="appreciation" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required></textarea>
                    <div class="mt-4 flex justify-end">
                        <button type="button" onclick="closeAppreciationModal()" class="mr-2 px-4 py-2 bg-gray-300 text-gray-700 rounded">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Valider</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRemunerationModal(stagiaireId) {
            document.getElementById('remunerationModal').classList.remove('hidden');
            document.getElementById('remunerationForm').action = `/stagiaires/${stagiaireId}/remuneration`;
        }

        function closeRemunerationModal() {
            document.getElementById('remunerationModal').classList.add('hidden');
        }

        function openAppreciationModal(stagiaireId) {
            document.getElementById('appreciationModal').classList.remove('hidden');
            document.getElementById('appreciationForm').action = `/stagiaires/${stagiaireId}/appreciation`;
        }

        function closeAppreciationModal() {
            document.getElementById('appreciationModal').classList.add('hidden');
        }
    </script>
@endsection
