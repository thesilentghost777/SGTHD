@extends('layouts.app')

@section('content')
<div class="flex min-h-screen" x-data="{ sidebarOpen: false, isChefMode: true }">
    <!-- Mobile Menu Button -->
    <button
        class="lg:hidden p-4 text-white bg-blue-600 fixed z-50 top-4 left-4 rounded-md shadow-md"
        @click="sidebarOpen = !sidebarOpen"
        aria-label="Open menu">
        <i class="mdi mdi-menu text-2xl"></i>
    </button>

    <!-- Content Area -->
    <main class="flex-1 p-3 lg:ml-72">
        <div class="mb-6">
            <h1 class="text-2xl font-bold mb-2">Ajustement du Solde</h1>
            <p class="text-gray-600">Modification manuelle du solde du chef de production</p>
        </div>

        <!-- Solde actuel -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold mb-2">Solde actuel</h2>
            <p class="text-3xl font-bold text-green-600">{{ number_format($solde->montant, 0, ',', ' ') }} FCFA</p>
        </div>

        <!-- Formulaire d'ajustement -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('solde-cp.store-ajustement') }}" method="POST" id="formAjustement">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type d'opération</label>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="operation" value="ajouter" class="h-5 w-5 text-blue-600" checked>
                            <span class="ml-2 text-gray-700">Ajouter au solde</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="operation" value="soustraire" class="h-5 w-5 text-blue-600">
                            <span class="ml-2 text-gray-700">Soustraire du solde</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="operation" value="fixer" class="h-5 w-5 text-blue-600">
                            <span class="ml-2 text-gray-700">Fixer à un montant précis</span>
                        </label>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="montant" class="block text-sm font-medium text-gray-700 mb-2">Montant (FCFA)</label>
                    <input
                        type="number"
                        id="montant"
                        name="montant"
                        class="w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        required
                        min="0"
                    >
                    <p class="text-sm text-gray-500 mt-1" id="operationDescription">
                        Le montant sera ajouté au solde actuel.
                    </p>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Motif de l'ajustement</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                        required
                    ></textarea>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-alert-circle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Attention : Les ajustements de solde sont journalisés et ne peuvent pas être annulés.
                                Assurez-vous de bien vérifier les informations avant de valider.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('solde-cp.index') }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200">
                        Annuler
                    </a>
                    <button
                        type="button"
                        onclick="confirmAjustement()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        Valider l'ajustement
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

@push('scripts')
<!-- Ajout de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const operationRadios = document.querySelectorAll('input[name="operation"]');
        const operationDescription = document.getElementById('operationDescription');
        const soldeActuel = {{ $solde->montant }};

        function updateDescription() {
            const selectedOperation = document.querySelector('input[name="operation"]:checked').value;
            const montant = document.getElementById('montant').value || 0;

            switch (selectedOperation) {
                case 'ajouter':
                    operationDescription.textContent = `Le montant sera ajouté au solde actuel. Nouveau solde estimé: ${formatNumber(parseInt(soldeActuel) + parseInt(montant))} FCFA`;
                    break;
                case 'soustraire':
                    operationDescription.textContent = `Le montant sera soustrait du solde actuel. Nouveau solde estimé: ${formatNumber(parseInt(soldeActuel) - parseInt(montant))} FCFA`;
                    break;
                case 'fixer':
                    operationDescription.textContent = `Le solde sera fixé à ce montant exactement.`;
                    break;
            }
        }

        function formatNumber(number) {
            return new Intl.NumberFormat('fr-FR').format(number);
        }

        // Mettre à jour la description lors du changement d'opération
        operationRadios.forEach(radio => {
            radio.addEventListener('change', updateDescription);
        });

        // Mettre à jour la description lors du changement de montant
        document.getElementById('montant').addEventListener('input', updateDescription);

        // Initialiser la description
        updateDescription();
    });

    function confirmAjustement() {
        const operation = document.querySelector('input[name="operation"]:checked').value;
        const montant = document.getElementById('montant').value;
        const description = document.getElementById('description').value;
        const soldeActuel = {{ $solde->montant }};

        if (!montant || !description) {
            Swal.fire({
                title: 'Erreur',
                text: 'Veuillez remplir tous les champs obligatoires',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        let message = '';
        switch (operation) {
            case 'ajouter':
                message = `Vous êtes sur le point d'ajouter ${montant} FCFA au solde actuel.`;
                break;
            case 'soustraire':
                if (parseInt(montant) > soldeActuel) {
                    Swal.fire({
                        title: 'Erreur',
                        text: 'Le montant à soustraire est supérieur au solde actuel.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                message = `Vous êtes sur le point de soustraire ${montant} FCFA du solde actuel.`;
                break;
            case 'fixer':
                message = `Vous êtes sur le point de fixer le solde à ${montant} FCFA.`;
                break;
        }

        Swal.fire({
            title: 'Confirmation',
            text: message + ' Cette action sera enregistrée dans l\'historique. Voulez-vous continuer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, valider',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formAjustement').submit();
            }
        });
    }
</script>
@endpush

@endsection
