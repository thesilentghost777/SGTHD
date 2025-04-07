<!-- Modal d'ajustement des quantités -->
<div id="adjustQuantityModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg px-4 pt-5 pb-4 sm:p-6 sm:pb-4 relative w-full max-w-lg mx-4">
            <!-- Bouton fermeture -->
            <button type="button" onclick="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500">
                <span class="sr-only">Fermer</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Contenu -->
            <div class="sm:flex sm:items-start">
                <div class="w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title"></h3>

                    <form id="adjustQuantityForm" class="space-y-4">
                        @csrf
                        <input type="hidden" id="itemId" name="itemId">
                        <input type="hidden" id="itemType" name="itemType">
                        <input type="hidden" id="operation" name="operation">

                        <div>
                            <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.01" name="quantite" id="quantite"
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md"
                                    required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm" id="uniteLabel"></span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700">Note (optionnel)</label>
                            <div class="mt-1">
                                <textarea id="note" name="note" rows="2"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="submitAdjustment()"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Confirmer
                </button>
                <button type="button" onclick="closeModal()"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function adjustQuantity(type, id, adjustmentType) {
    const modal = document.getElementById('adjustQuantityModal');
    const titleElement = document.getElementById('modal-title');
    const uniteLabel = document.getElementById('uniteLabel');

    // Réinitialiser le formulaire
    document.getElementById('adjustQuantityForm').reset();

    // Configurer les champs cachés
    document.getElementById('itemId').value = id;
    document.getElementById('itemType').value = type;
    document.getElementById('operation').value = adjustmentType;

    // Configurer le titre et l'unité en fonction du type
    if (type === 'matiere') {
        fetch(`/stock/search-matiere?id=${id}`)
            .then(response => response.json())
            .then(data => {
                titleElement.textContent = `${adjustmentType === 'add' ? 'Ajouter' : 'Réduire'} - ${data.nom}`;
                uniteLabel.textContent = data.unite_classique;
            });
    } else {
        fetch(`/stock/search-produit?id=${id}`)
            .then(response => response.json())
            .then(data => {
                titleElement.textContent = `${adjustmentType === 'add' ? 'Ajouter' : 'Réduire'} - ${data.nom}`;
                uniteLabel.textContent = 'unités';
            });
    }

    // Afficher la modale
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('adjustQuantityModal').classList.add('hidden');
}

function submitAdjustment() {
    const form = document.getElementById('adjustQuantityForm');
    const itemType = document.getElementById('itemType').value;
    const itemId = document.getElementById('itemId').value;

    const formData = new FormData(form);
    formData.append('operation', document.getElementById('operation').value);

    // Construire l'URL en fonction du type
    const url = itemType === 'matiere'
        ? `/stock/adjust-matiere-quantity/${itemId}`
        : `/stock/adjust-produit-quantity/${itemId}`;

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer la modale et recharger la page pour afficher les changements
            closeModal();
            window.location.reload();
        } else {
            alert(data.message || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de l\'ajustement de la quantité');
    });
}

// Fermer la modale si l'utilisateur clique en dehors
document.getElementById('adjustQuantityModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
