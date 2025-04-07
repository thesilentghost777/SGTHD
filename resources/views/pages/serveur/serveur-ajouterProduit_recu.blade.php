@extends('pages/serveur/serveur_default')

@section('page-content')
<div x-data="productHandler">


    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-blue-600">Liste des produits</h2>
            <div>
                <button
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition mr-2"
                    @click="toggleForm"
                >
                    <i class="mdi mdi-plus-circle-outline"></i> Ajouter produits
                </button>
                <button
                    class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition"
                    @click="recupererProduitsInvendus"
                >
                    <i class="mdi mdi-plus-circle-outline"></i> Récupérer les produits invendus d'hier
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg shadow-md">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white">
                    <tr>
                        <th class="border px-6 py-4 text-left font-semibold">Code produit</th>
                        <th class="border px-6 py-4 text-left font-semibold">Pointeur</th>
                        <th class="border px-6 py-4 text-left font-semibold">Produit</th>
                        <th class="border px-6 py-4 text-left font-semibold">Prix</th>
                        <th class="border px-6 py-4 text-left font-semibold">Quantité</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(produit, index) in displayedProducts" :key="index">
                        <tr class="hover:bg-gray-100 transition">
                            <td class="border px-6 py-4" x-text="produit.code_produit"></td>
                            <td class="border px-6 py-4" x-text="produit.pointeur"></td>
                            <td class="border px-6 py-4" x-text="produit.nom"></td>
                            <td class="border px-6 py-4" x-text="produit.prix"></td>
                            <td class="border px-6 py-4" x-text="produit.quantite"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div x-show="!showAllProducts && products.length > 10" class="text-center mt-6">
            <button
                class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition"
                @click="showAllProducts = true"
            >
                Afficher toute la liste
            </button>
        </div>

        <div
            x-show="showForm"
            x-transition
            @click.away="showForm = false"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
        >
            <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 max-w-lg w-full m-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-700">Ajouter un produit</h3>
                    <button @click="showForm = false" class="text-gray-500 hover:text-gray-700">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>

                <form @submit.prevent="submitForm" id="productForm">
                    @csrf
                    <div class="mb-4">
                        <label for="pointeur" class="block text-sm font-medium text-gray-700">Pointeur</label>
                        <select
                            id="pointeur"
                            name="pointeur"
                            x-model="formData.pointeur"
                            class="w-full border-gray-300 rounded-lg"
                            required
                        >
                            <option value="">Sélectionnez un pointeur</option>
                            @foreach ($Employe as $employe)
                                <option value="{{ $employe->id }}">{{ $employe->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="produit" class="block text-sm font-medium text-gray-700">Produit</label>
                        <select
                            id="produit"
                            name="produit"
                            x-model="formData.produit"
                            class="w-full border-gray-300 rounded-lg"
                            required
                        >
                            <option value="">Sélectionnez un produit</option>
                            @foreach ($produitR as $product)
                                <option value="{{ $product->code_produit }}">{{ $product->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                        <input
                            type="number"
                            id="quantite"
                            name="quantite"
                            x-model="formData.quantite"
                            class="w-full border-gray-300 rounded-lg"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="prix" class="block text-sm font-medium text-gray-700">Prix</label>
                        <input
                            type="number"
                            id="prix"
                            name="prix"
                            x-model="formData.prix"
                            class="w-full border-gray-300 rounded-lg"
                            step="0.01"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <div class="flex items-center space-x-2">
                            <input
                                type="date"
                                id="date"
                                name="date"
                                x-model="formData.date"
                                class="w-full border-gray-300 rounded-lg"
                                required
                            >
                            <button
                                type="button"
                                @click="formData.date = new Date().toISOString().split('T')[0]"
                                class="bg-gray-300 px-4 py-2 rounded-lg shadow hover:bg-gray-400 transition"
                            >
                                Aujourd'hui
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition"
                        >
                            Envoyer
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productHandler', () => ({
        showForm: false,
        showAllProducts: false,
        products: [],
        formData: {
            pointeur: '',
            produit: '',
            quantite: '',
            prix: ''
        },

        init() {
            // Safely parse the products data
            try {
                const productsData = @json($produits);
                this.products = Array.isArray(productsData) ? productsData : [];
            } catch (error) {
                console.error('Error initializing products:', error);
                this.products = [];
            }
        },

        get displayedProducts() {
            return this.showAllProducts ? this.products : this.products.slice(0, 10);
        },

        toggleForm() {
            this.showForm = !this.showForm;
            if (!this.showForm) {
                this.resetForm();
            }
        },

        resetForm() {
            this.formData = {
                pointeur: '',
                produit: '',
                quantite: '',
                prix: ''
            };
        },

        validateFormData() {
            const errors = [];
            if (!this.formData.pointeur) errors.push('Le pointeur est requis');
            if (!this.formData.produit) errors.push('Le produit est requis');
            if (!this.formData.quantite || this.formData.quantite <= 0) errors.push('La quantité doit être supérieure à 0');
            if (!this.formData.prix || this.formData.prix <= 0) errors.push('Le prix doit être supérieur à 0');
            return errors;
        },

        async submitForm() {
            try {
                // Validate form data
                const errors = this.validateFormData();
                if (errors.length > 0) {
                    Swal.fire('Erreur de validation', errors.join('\n'), 'error');
                    return;
                }

                // Prepare the form data
                const formDataToSend = new FormData();
                Object.keys(this.formData).forEach(key => {
                    formDataToSend.append(key, this.formData[key]);
                });

                const response = await fetch('{{ route("addProduit_recu") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.formData)
                });

                // Check if the response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Réponse du serveur non valide');
                }

                const data = await response.json();

                if (response.ok && data.success) {
                    Swal.fire('Succès', data.message || 'Produit ajouté avec succès', 'success');
                    this.toggleForm();
                    location.reload();
                } else {
                    throw new Error(data.message || 'Erreur lors de l\'ajout du produit');
                }
            } catch (error) {
                console.error('Erreur lors de la soumission:', error);
                Swal.fire('Erreur', error.message || 'Une erreur est survenue lors de l\'envoi du formulaire', 'error');
            }
        },

        async recupererProduitsInvendus() {
            try {
                const response = await fetch('{{ route("recupererInvendus") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }

                const data = await response.json();

                if (data.success) {
                    Swal.fire('Succès', data.message, 'success');
                    location.reload();
                } else {
                    throw new Error(data.message || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error('Erreur:', error);
                Swal.fire('Erreur', error.message || 'Erreur de connexion', 'error');
            }
        }
    }));
});
</script>
@endsection
