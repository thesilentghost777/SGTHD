@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Modifier la Déclaration</h1>
        <p class="text-gray-600 mt-1">Modifiez les informations de vente de sacs</p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('bag.sales.update', $sale) }}" method="POST" id="salesForm">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Type de sac</label>
                <div class="bg-gray-100 px-3 py-2 rounded-md">
                    {{ $sale->reception->assignment->bag->name }}
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Quantité reçue</label>
                <div class="bg-gray-100 px-3 py-2 rounded-md" id="received-quantity" data-quantity="{{ $sale->reception->quantity_received }}">
                    {{ $sale->reception->quantity_received }}
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="quantity_sold" class="block text-gray-700 text-sm font-bold mb-2">Sacs vendus</label>
                    <input type="number" name="quantity_sold" id="quantity_sold"
                        value="{{ old('quantity_sold', $sale->quantity_sold) }}"
                        required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('quantity_sold')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity_unsold" class="block text-gray-700 text-sm font-bold mb-2">Sacs invendus</label>
                    <input type="number" name="quantity_unsold" id="quantity_unsold"
                        value="{{ old('quantity_unsold', $sale->quantity_unsold) }}"
                        required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('quantity_unsold')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-2">
                <p id="total-info" class="text-blue-600 font-medium">
                    Total: <span id="total-quantity">0</span> / {{ $sale->reception->quantity_received }} sacs
                </p>
                <p id="balance-warning" class="text-red-500 text-sm font-medium hidden">
                    La somme des sacs vendus et invendus doit être égale à la quantité reçue!
                </p>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes (facultatif)</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $sale->notes) }}</textarea>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('bag.sales.create') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded shadow transition duration-150 ease-in-out">
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
    const soldInput = document.getElementById('quantity_sold');
    const unsoldInput = document.getElementById('quantity_unsold');
    const totalQuantity = document.getElementById('total-quantity');
    const balanceWarning = document.getElementById('balance-warning');
    const salesForm = document.getElementById('salesForm');
    const receivedQuantity = parseInt(document.getElementById('received-quantity').getAttribute('data-quantity'));

    function checkBalance() {
        const soldQuantity = parseInt(soldInput.value) || 0;
        const unsoldQuantity = parseInt(unsoldInput.value) || 0;
        const totalDeclared = soldQuantity + unsoldQuantity;

        // Mettre à jour l'affichage du total
        totalQuantity.textContent = totalDeclared;

        // Vérifier l'équilibre
        if (totalDeclared !== receivedQuantity) {
            balanceWarning.classList.remove('hidden');
        } else {
            balanceWarning.classList.add('hidden');
        }
    }

    soldInput.addEventListener('input', checkBalance);
    unsoldInput.addEventListener('input', checkBalance);

    // Validation du formulaire
    salesForm.addEventListener('submit', function(event) {
        const soldQuantity = parseInt(soldInput.value) || 0;
        const unsoldQuantity = parseInt(unsoldInput.value) || 0;
        const totalDeclared = soldQuantity + unsoldQuantity;

        if (totalDeclared !== receivedQuantity) {
            event.preventDefault();
            balanceWarning.classList.remove('hidden');
            window.scrollTo(0, balanceWarning.offsetTop - 100);
        }
    });

    // Vérification initiale
    checkBalance();
});
</script>
@endsection
