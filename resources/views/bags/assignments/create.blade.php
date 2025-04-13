@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Assigner des Sacs</h1>
        <p class="text-gray-600 mt-1">Assignez des sacs aux serveurs pour la vente</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 mb-8">
        <form action="{{ route('bag.assignments.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="bag_id" class="block text-gray-700 text-sm font-bold mb-2">Type de sac</label>
                    <select name="bag_id" id="bag_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un type de sac</option>
                        @foreach($bags as $bag)
                        <option value="{{ $bag->id }}" data-stock="{{ $bag->stock_quantity }}" {{ old('bag_id') == $bag->id ? 'selected' : '' }}>
                            {{ $bag->name }} (Stock: {{ $bag->stock_quantity }})
                        </option>
                        @endforeach
                    </select>
                    @if(count($bags) === 0)
    <p class="text-amber-600 text-sm mt-2">Aucun sac disponible. Veuillez vérifier le stock.</p>
@else
    <p class="text-gray-500 text-sm mt-2">Note: Les sacs avec un stock à 0 n'apparaissent pas dans cette liste.</p>
@endif
                    @error('bag_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Serveur</label>
                    <select name="user_id" id="user_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un serveur</option>
                        @foreach($servers as $server)
                        <option value="{{ $server->id }}" {{ old('user_id') == $server->id ? 'selected' : '' }}>
                            {{ $server->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('user_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="quantity_assigned" class="block text-gray-700 text-sm font-bold mb-2">Quantité à assigner</label>
                <input type="number" name="quantity_assigned" id="quantity_assigned" value="{{ old('quantity_assigned') }}" required min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p id="stock-warning" class="text-red-500 text-xs mt-1 hidden">La quantité demandée dépasse le stock disponible!</p>
                @error('quantity_assigned')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes (facultatif)</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                    Assigner les sacs
                </button>
            </div>
        </form>
    </div>

    <!-- Assignations récentes -->
    <div>
        <h2 class="text-xl font-semibold text-blue-700 mb-4">Assignations récentes</h2>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-blue-50 text-blue-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Sac</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Serveur</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentAssignments as $assignment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $assignment->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $assignment->bag->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $assignment->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $assignment->quantity_assigned }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('bag.assignments.edit', $assignment) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucune assignation récente</td>
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
    const bagSelect = document.getElementById('bag_id');
    const quantityInput = document.getElementById('quantity_assigned');
    const stockWarning = document.getElementById('stock-warning');

    function checkStock() {
        const selectedOption = bagSelect.options[bagSelect.selectedIndex];
        if (!selectedOption.value) return;

        const availableStock = parseInt(selectedOption.getAttribute('data-stock'));
        const requestedQuantity = parseInt(quantityInput.value) || 0;

        if (requestedQuantity > availableStock) {
            stockWarning.classList.remove('hidden');
        } else {
            stockWarning.classList.add('hidden');
        }
    }

    bagSelect.addEventListener('change', checkStock);
    quantityInput.addEventListener('input', checkStock);

    // Initial check
    checkStock();
});
</script>
@endsection
