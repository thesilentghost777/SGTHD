@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Enregistrer une Réception de Sacs</h1>
        <p class="text-gray-600 mt-1">Déclarez les sacs que vous avez reçus du chef de production</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 mb-8">
        <form action="{{ route('bag.receptions.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="bag_assignment_id" class="block text-gray-700 text-sm font-bold mb-2">Assignation de sacs</label>
                <select name="bag_assignment_id" id="bag_assignment_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Sélectionner une assignation</option>
                    @foreach($assignments as $assignment)
                    <option value="{{ $assignment->id }}" data-assigned="{{ $assignment->quantity_assigned }}" data-received="{{ $assignment->total_received }}" {{ old('bag_assignment_id') == $assignment->id ? 'selected' : '' }}>
                        {{ $assignment->bag->name }} - Assigné: {{ $assignment->quantity_assigned }}, Déjà reçu: {{ $assignment->total_received }}
                    </option>
                    @endforeach
                </select>
                @error('bag_assignment_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="quantity_received" class="block text-gray-700 text-sm font-bold mb-2">Quantité reçue</label>
                <input type="number" name="quantity_received" id="quantity_received" value="{{ old('quantity_received') }}" required min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded shadow transition duration-150 ease-in-out">
                    Enregistrer la réception
                </button>
            </div>
        </form>
    </div>

    <!-- Réceptions récentes -->
    <div>
        <h2 class="text-xl font-semibold text-blue-700 mb-4">Réceptions récentes</h2>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-blue-50 text-blue-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Sac</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Quantité reçue</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentReceptions as $reception)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reception->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reception->assignment->bag->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reception->quantity_received }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('bag.receptions.edit', $reception) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucune réception récente</td>
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
    const assignmentSelect = document.getElementById('bag_assignment_id');
    const quantityInput = document.getElementById('quantity_received');
    const receptionWarning = document.getElementById('reception-warning');

    function checkQuantity() {
        const selectedOption = assignmentSelect.options[assignmentSelect.selectedIndex];
        if (!selectedOption.value) return;

        const assignedQuantity = parseInt(selectedOption.getAttribute('data-assigned'));
        const alreadyReceived = parseInt(selectedOption.getAttribute('data-received'));
        const newQuantity = parseInt(quantityInput.value) || 0;

        if (alreadyReceived + newQuantity > assignedQuantity) {
            receptionWarning.classList.remove('hidden');
        } else {
            receptionWarning.classList.add('hidden');
        }
    }

    assignmentSelect.addEventListener('change', checkQuantity);
    quantityInput.addEventListener('input', checkQuantity);

    // Initial check
    checkQuantity();
});
</script>
@endsection
