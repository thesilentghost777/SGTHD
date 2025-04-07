<!-- resources/views/pages/chef_production/versements_validation.blade.php -->
@extends('pages/chef_production/chef_production_default')

@section('page-content')
<div class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">Validation des Versements</h1>
            </div>

            <div class="p-6">
                @if($versements->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        Aucun versement en attente de validation
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verseur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Encaisseur</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($versements as $versement)
                                    <tr id="versement-{{ $versement->code_vcsg }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ date('d/m/Y', strtotime($versement->date)) }}
                                        </td>
                                        <td class="px-6 py-4">{{ $versement->libelle }}</td>
                                        <td class="px-6 py-4">{{ number_format($versement->somme, 0, ',', ' ') }} FCFA</td>
                                        <td class="px-6 py-4">{{ $versement->nom_verseur }}</td>
                                        <td class="px-6 py-4">{{ $versement->nom_encaisseur }}</td>
                                        <td class="px-6 py-4 space-x-2">
                                            <button
                                                onclick="validerVersement({{ $versement->code_vcsg }}, 'valider')"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Valider
                                            </button>
                                            <button
                                                onclick="validerVersement({{ $versement->code_vcsg }}, 'rejeter')"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Rejeter
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Ajout de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function validerVersement(codeVcsg, action) {
    Swal.fire({
        title: action === 'valider' ? 'Valider le versement ?' : 'Rejeter le versement ?',
        input: 'textarea',
        inputLabel: 'Commentaire (optionnel)',
        inputPlaceholder: 'Entrez un commentaire...',
        showCancelButton: true,
        confirmButtonText: action === 'valider' ? 'Valider' : 'Rejeter',
        cancelButtonText: 'Annuler',
        confirmButtonColor: action === 'valider' ? '#059669' : '#DC2626'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/chef-production/versements/${codeVcsg}/valider`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: action,
                    commentaire: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById(`versement-${codeVcsg}`).remove();
                    Swal.fire('Succès', data.message, 'success');
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire('Erreur', error.message, 'error');
            });
        }
    });
}
</script>
@endsection
