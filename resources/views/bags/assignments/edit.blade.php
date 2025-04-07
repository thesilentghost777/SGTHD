@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Modifier l'Assignation</h1>
        <p class="text-gray-600 mt-1">Modifiez les informations de l'assignation de sacs</p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('bag.assignments.update', $assignment) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Type de sac</label>
                <div class="bg-gray-100 px-3 py-2 rounded-md">
                    {{ $assignment->bag->name }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Serveur</label>
                <div class="bg-gray-100 px-3 py-2 rounded-md">
                    {{ $assignment->user->name }}
                </div>
            </div>

            <div class="mb-4">
                <label for="quantity_assigned" class="block text-gray-700 text-sm font-bold mb-2">Quantité assignée</label>
                <input type="number" name="quantity_assigned" id="quantity_assigned"
                    value="{{ old('quantity_assigned', $assignment->quantity_assigned) }}"
                    required min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    data-original="{{ $assignment->quantity_assigned }}"
                    data-stock="{{ $assignment->bag->stock_quantity }}">
                <p id="stock-warning" class="text-red-500 text-xs mt-1 hidden">
                    La quantité demandée dépasse le stock disponible!
                </p>
                @error('quantity_assigned')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes (facultatif)</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $assignment->notes) }}</textarea>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('bag.assignments.create') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded shadow transition duration-150 ease-in-out">
                    Annuler
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity_assigned');
    const stockWarning = document.getElementById('stock-warning');
    const originalQuantity = parseInt(quantityInput.getAttribute('data-original'));
    const availableStock = parseInt(quantityInput.getAttribute('data-stock'));

    function checkStock() {
        const newQuantity = parseInt(quantityInput.value) || 0;
        // Calculer la différence par rapport à la valeur originale
        const difference = newQuantity - originalQuantity;

        // Si la différence est positive (augmentation), vérifier si le stock est suffisant
        if (difference > 0 && difference > availableStock) {
            stockWarning.classList.remove('hidden');
        } else {
            stockWarning.classList.add('hidden');
        }
    }

    quantityInput.addEventListener('input', checkStock);

    // Vérification initiale
    checkStock();
});
</script>
@endsection
