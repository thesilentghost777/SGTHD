@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Déclarer les Sacs Vendus/Invendus</h1>
        <p class="text-gray-600 mt-1">Enregistrez les sacs que vous avez vendus ou qui sont restés invendus</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 mb-8">
        <form action="{{ route('bag.sales.store') }}" method="POST" id="salesForm">
            @csrf

            <div class="mb-4">
                <label for="bag_reception_id" class="block text-gray-700 text-sm font-bold mb-2">Réception de sacs</label>
                <select name="bag_reception_id" id="bag_reception_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Sélectionner une réception</option>
                    @foreach($receptions as $reception)
                    <option value="{{ $reception->id }}" data-quantity="{{ $reception->quantity_received }}" {{ old('bag_reception_id') == $reception->id ? 'selected' : '' }}>
                        {{ $reception->assignment->bag->name }} - Reçu le {{ $reception->created_at->format('d/m/Y') }} - {{ $reception->quantity_received }} sacs
                    </option>
                    @endforeach
                </select>
                @error('bag_reception_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="quantity_sold" class="block text-gray-700 text-sm font-bold mb-2">Sacs vendus</label>
                    <input type="number" name="quantity_sold" id="quantity_sold" value="{{ old('quantity_sold') }}" required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('quantity_sold')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity_unsold" class="block text-gray-700 text-sm font-bold mb-2">Sacs invendus</label>
                    <input type="number" name="quantity_unsold" id="quantity_unsold" value="{{ old('quantity_unsold') }}" required min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('quantity_unsold')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-2">
                <p id="total-info" class="text-blue-600 font-medium hidden">
                    Total: <span id="total-quantity">0</span> / <span id="expected-quantity">0</span> sacs
                </p>
                <p id="balance-warning" class="text-red-500 text-sm font-medium hidden">
                    La somme des sacs vendus et invendus doit être égale à la quantité reçue!
                </p>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes (facultatif)</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                    Enregistrer la déclaration
                </button>
            </div>
        </form>
    </div>

    <!-- Ventes récentes -->
    <div>
        <h2 class="text-xl font-semibold text-blue-700 mb-4">Déclarations récentes</h2>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-blue-50 text-blue-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Sac</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Vendus</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Invendus</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentSales as $sale)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $sale->reception->assignment->bag->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->quantity_sold }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->quantity_unsold }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('bag.sales.edit', $sale) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucune déclaration récente</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const receptionSelect = document.getElementById('bag_reception_id');
    const soldInput = document.getElementById('quantity_sold');
    const unsoldInput = document.getElementById('quantity_unsold');
    const totalInfo = document.getElementById('total-info');
    const totalQuantity = document.getElementById('total-quantity');
    const expectedQuantity = document.getElementById('expected-quantity');
    const balanceWarning = document.getElementById('balance-warning');
    const salesForm = document.getElementById('salesForm');

    function checkBalance() {
        const selectedOption = receptionSelect.options[receptionSelect.selectedIndex];
        if (!selectedOption.value) {
            totalInfo.classList.add('hidden');
            balanceWarning.classList.add('hidden');
            return;
        }

        const receivedQuantity = parseInt(selectedOption.getAttribute('data-quantity'));
        const soldQuantity = parseInt(soldInput.value) || 0;
        const unsoldQuantity = parseInt(unsoldInput.value) || 0;
        const totalDeclared = soldQuantity + unsoldQuantity;

        // Afficher les informations de total
        totalInfo.classList.remove('hidden');
        totalQuantity.textContent = totalDeclared;
        expectedQuantity.textContent = receivedQuantity;

        // Vérifier l'équilibre
        if (totalDeclared !== receivedQuantity) {
            balanceWarning.classList.remove('hidden');
        } else {
            balanceWarning.classList.add('hidden');
        }
    }

    receptionSelect.addEventListener('change', checkBalance);
    soldInput.addEventListener('input', checkBalance);
    unsoldInput.addEventListener('input', checkBalance);

    // Validation du formulaire
    salesForm.addEventListener('submit', function(event) {
        const selectedOption = receptionSelect.options[receptionSelect.selectedIndex];
        if (!selectedOption.value) return;

        const receivedQuantity = parseInt(selectedOption.getAttribute('data-quantity'));
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
