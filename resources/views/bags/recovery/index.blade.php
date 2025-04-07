@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Récupération des sacs invendus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($salesByServer->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-lg text-gray-600">Aucun sac invendu à récupérer pour le moment.</p>
                        </div>
                    @else
                        <div class="space-y-8">
                            @foreach ($salesByServer as $serverName => $sales)
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-semibold text-blue-800 mb-3">Sacs invendus par {{ $serverName }}</h3>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white">
                                            <thead>
                                                <tr class="bg-blue-100 text-blue-800">
                                                    <th class="py-2 px-4 text-left">Sac</th>
                                                    <th class="py-2 px-4 text-left">Date de vente</th>
                                                    <th class="py-2 px-4 text-right">Quantité invendue</th>
                                                    <th class="py-2 px-4 text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach ($sales as $sale)
                                                    <tr class="hover:bg-blue-50">
                                                        <td class="py-3 px-4">{{ $sale->reception->assignment->bag->name }}</td>
                                                        <td class="py-3 px-4">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                                        <td class="py-3 px-4 text-right">{{ $sale->quantity_unsold }}</td>
                                                        <td class="py-3 px-4 text-center">
                                                            <button
                                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm"
                                                                onclick="openRecoveryModal({{ $sale->id }}, {{ $sale->quantity_unsold }}, '{{ $sale->reception->assignment->bag->name }}')"
                                                            >
                                                                Récupérer
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de récupération -->
    <div id="recoveryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Récupérer les sacs invendus</h3>

            <form id="recoveryForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Sac: <span id="bagName" class="font-medium"></span></p>
                    <p class="text-sm text-gray-600 mb-4">Quantité invendue: <span id="unsoldQuantity" class="font-medium"></span></p>

                    <label for="quantity_to_recover" class="block text-sm font-medium text-gray-700">Quantité à récupérer</label>
                    <input type="number" name="quantity_to_recover" id="quantity_to_recover" min="1"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <div id="quantityError" class="text-red-500 text-sm mt-1 hidden">La quantité doit être comprise entre 1 et le nombre de sacs invendus.</div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRecoveryModal()" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-md text-sm">
                        Annuler
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                        Confirmer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRecoveryModal(saleId, unsoldQuantity, bagName) {
            document.getElementById('bagName').textContent = bagName;
            document.getElementById('unsoldQuantity').textContent = unsoldQuantity;
            document.getElementById('quantity_to_recover').max = unsoldQuantity;
            document.getElementById('quantity_to_recover').value = unsoldQuantity;
            document.getElementById('recoveryForm').action = `/bags/recovery/${saleId}`;
            document.getElementById('recoveryModal').classList.remove('hidden');

            // Validation de la quantité
            document.getElementById('quantity_to_recover').addEventListener('input', function() {
                const value = parseInt(this.value);
                const max = parseInt(this.max);

                if (isNaN(value) || value < 1 || value > max) {
                    document.getElementById('quantityError').classList.remove('hidden');
                } else {
                    document.getElementById('quantityError').classList.add('hidden');
                }
            });
        }

        function closeRecoveryModal() {
            document.getElementById('recoveryModal').classList.add('hidden');
        }
    </script>
@endsection
