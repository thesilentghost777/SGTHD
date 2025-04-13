@extends('layouts.app')
@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Gestion des catégories</h2>
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">Les catégories vous permettent de classer vos transactions pour un meilleur suivi de vos finances.</p>
                            <ul class="mt-2 list-disc list-inside text-sm text-blue-600 space-y-1">
                                <li><span class="font-semibold">Matière première :</span> Achats de farine, huile, eau...</li>
                                <li><span class="font-semibold">Réparation matériel :</span> Maintenance des machines...</li>
                                <li><span class="font-semibold">Transport :</span> Frais de transport, carburant...</li>
                                <li><span class="font-semibold">Ventes :</span> Revenus des produits vendus</li>
                                <li><span class="font-semibold">Salaire :</span> Paiements des employés</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form action="{{ route('categories.store') }}" method="POST" class="mb-6">
                    @csrf
                    <div class="flex gap-4">
                        <input type="text" name="name" placeholder="Nouvelle catégorie" required class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Ajouter</button>
                    </div>
                </form>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categories as $category)
                            <tr x-data="{
                                editing: false,
                                name: '{{ $category->name }}',
                                originalName: '{{ $category->name }}',
                                cancelEdit() {
                                    this.editing = false;
                                    this.name = this.originalName;
                                },
                                submitForm() {
                                    // Met à jour l'input caché avant la soumission
                                    this.$refs.hiddenInput.value = this.name;
                                    this.$refs.updateForm.submit();
                                }
                            }">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span x-show="!editing" x-text="name" class="text-gray-900"></span>
                                    <div x-show="editing" class="flex items-center gap-2">
                                        <input type="text"
                                               x-model="name"
                                               @keydown.enter.prevent="submitForm()"
                                               @keydown.escape.window="cancelEdit()"
                                               @click.away="cancelEdit()"
                                               class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <button type="button" @click="submitForm()" class="text-green-600 hover:text-green-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button type="button" @click="cancelEdit()" class="text-red-600 hover:text-red-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <form x-ref="updateForm" action="{{ route('categories.update', $category) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden"
                                               name="name"
                                               x-ref="hiddenInput"
                                               :value="name">
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="editing = true" x-show="!editing" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
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
</div>
@endsection
