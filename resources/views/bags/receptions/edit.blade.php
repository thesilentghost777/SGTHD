@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Modifier la Réception</h1>
        <p class="text-gray-600 mt-1">Modifiez les informations de réception de sacs</p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('bag.receptions.update', $reception) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Type de sac</label>
                <div class="bg-gray-100 px-3 py-2 rounded-md">
                    {{ $reception->assignment->bag->name }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Quantité assignée</label>
                <div class="bg-gray-100 px-3 py-2 rounded-md">
                    {{ $reception->assignment->quantity_assigned }}
                </div>
            </div>

            <div class="mb-4">
                <label for="quantity_received" class="block text-gray-700 text-sm font-bold mb-2">Quantité reçue</label>
                <input type="number" name="quantity_received" id="quantity_received"
                    value="{{ old('quantity_received', $reception->quantity_received) }}"
                    required min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    data-assigned="{{ $reception->assignment->quantity_assigned }}"
                    data-total-received="{{ $reception->assignment->total_received - $reception->quantity_received }}">
                <p id="reception-warning" class="text-yellow-500 text-xs mt-1 hidden">
                    Attention: Le total des sacs reçus dépassera la quantité assignée!
                </p>
                @error('quantity_received')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes (facultatif)</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $reception->notes) }}</textarea>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('bag.receptions.create') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded shadow transition duration-150 ease-in-out">
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
    const quantityInput = document.getElementById('quantity_received');
    const receptionWarning = document.getElementById('reception-warning');
    const assignedQuantity = parseInt(quantityInput.getAttribute('data-assigned'));
    const otherReceivedQuantity = parseInt(quantityInput.getAttribute('data-total-received'));

    function checkQuantity() {
        const newQuantity = parseInt(quantityInput.value) || 0;

        if (otherReceivedQuantity + newQuantity > assignedQuantity) {
            receptionWarning.classList.remove('hidden');
        } else {
            receptionWarning.classList.add('hidden');
        }
    }

    quantityInput.addEventListener('input', checkQuantity);

    // Vérification initiale
    checkQuantity();
});
</script>
@endsection
