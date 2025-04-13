@extends('layouts.app')

@section('content')

@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
        <p>{{ session('success') }}</p>
    </div>
@endif

<!-- Nouvel avertissement pour la validation en fin de mois -->
<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
    <div class="flex items-center">
        <div class="py-1">
            <i class="mdi mdi-calendar-clock text-xl mr-2"></i>
        </div>
        <div>
            <p class="font-bold">Rappel important</p>
            <p>Veuillez noter que les manquants ne doivent être ajustés ou validés qu'à la fin du mois. Toute validation prématurée pourrait affecter les calculs mensuels.</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestion des Manquants</h1>
        <div class="flex space-x-2">
            <a href="{{ route('manquants.calculer') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="mdi mdi-calculator-variant mr-2"></i>Calculer Tous les Manquants
            </a>
            <a href="{{ route('manquant.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="mdi mdi-plus-circle mr-2"></i>Facturer un Manquant
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fonction</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($manquants as $manquant)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="mdi mdi-account text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $manquant->employe->name }}</div>
                                <div class="text-sm text-gray-500">{{ $manquant->employe->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($manquant->employe->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <span class="font-semibold text-gray-900">{{ number_format($manquant->montant, 0, ',', ' ') }} FCFA</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($manquant->statut == 'en_attente')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                En attente
                            </span>
                        @elseif($manquant->statut == 'ajuste')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Ajusté
                            </span>
                        @elseif($manquant->statut == 'valide')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Validé
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex justify-center space-x-2">
                            <a href="#"
                               onclick="showDetails('{{ $manquant->id }}')"
                               class="text-blue-600 hover:text-blue-900">
                                <i class="mdi mdi-eye text-lg"></i>
                            </a>

                            @if($manquant->statut != 'valide')
                                <a href="{{ route('manquants.ajuster', $manquant->id) }}"
                                   class="text-yellow-600 hover:text-yellow-900">
                                    <i class="mdi mdi-pencil text-lg"></i>
                                </a>

                                <a href="{{ route('manquants.valider', $manquant->id) }}"
                                   onclick="return confirm('Êtes-vous sûr de vouloir valider ce manquant?')"
                                   class="text-green-600 hover:text-green-900">
                                    <i class="mdi mdi-check-circle text-lg"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        Aucun manquant trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal pour afficher les détails -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[80vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Détails du Manquant</h3>
                <button onclick="hideDetails()" class="text-gray-400 hover:text-gray-500">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>

            <div id="modalContent" class="space-y-4">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>

            <div class="mt-6 flex justify-end">
                <button onclick="hideDetails()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetails(id) {
        // Récupérer les détails via AJAX
        fetch(`/manquants/${id}/details`)
            .then(response => response.json())
            .then(data => {
                const content = document.getElementById('modalContent');

                let html = `
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Employé</p>
                            <p class="text-base">${data.employe.name}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Fonction</p>
                            <p class="text-base">${data.employe.role}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Montant</p>
                            <p class="text-lg font-semibold">${new Intl.NumberFormat('fr-FR').format(data.montant)} FCFA</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Statut</p>
                            <p class="text-base">${data.statut}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500">Explication</p>
                        <pre class="mt-1 p-3 bg-gray-50 rounded text-sm whitespace-pre-wrap">${data.explication}</pre>
                    </div>
                `;

                if (data.commentaire_dg) {
                    html += `
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-500">Commentaire du DG</p>
                            <p class="mt-1 p-3 bg-blue-50 rounded text-sm">${data.commentaire_dg}</p>
                        </div>
                    `;
                }

                content.innerHTML = html;
                document.getElementById('detailsModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement des détails');
            });
    }

    function hideDetails() {
        document.getElementById('detailsModal').classList.add('hidden');
    }
</script>
@endsection
