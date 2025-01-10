@extends('pages.chef_production.chef_production_default')

@section('page-content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Gestion des Réservations de Matières Premières</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producteur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Disponible</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reservations as $reservation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $reservation->producteur->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $reservation->matiere->nom }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ round($reservation->quantite_demandee,1) }} {{ $reservation->unite_demandee }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ round($reservation->matiere->quantite,1) }} Unite de {{ round($reservation->matiere->quantite_par_unite,1) }} {{ $reservation->matiere->unite_classique }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap space-x-2">
                            <button
                                onclick="openValidationModal('{{ $reservation->id }}')"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                Valider
                            </button>
                            <button
                                onclick="openRefusalModal('{{ $reservation->id }}')"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                Refuser
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Aucune réservation en attente
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Validation -->
<div id="validationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmer la validation</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Êtes-vous sûr de vouloir valider cette réservation ?
                </p>
            </div>
            <form id="validationForm" method="POST" class="mt-4">
                @csrf
                <div class="items-center px-4 py-3">
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Confirmer
                    </button>
                    <button type="button" onclick="closeValidationModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 ml-2">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Refus -->
<div id="refusalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Motif du refus</h3>
            <form id="refusalForm" method="POST" class="mt-4">
                @csrf
                <div class="mt-2 px-7 py-3">
                    <textarea
                        name="commentaire"
                        required
                        class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none"
                        rows="4"
                        placeholder="Veuillez indiquer le motif du refus"></textarea>
                </div>
                <div class="items-center px-4 py-3">
                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Refuser
                    </button>
                    <button type="button" onclick="closeRefusalModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 ml-2">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<script>
function openValidationModal(reservationId) {
    const modal = document.getElementById('validationModal');
    const form = document.getElementById('validationForm');
    form.action = "{{ route('chef.reservations.valider', ['reservation' => ':id']) }}".replace(':id', reservationId);
    modal.classList.remove('hidden');
}

function closeValidationModal() {
    document.getElementById('validationModal').classList.add('hidden');
}

function openRefusalModal(reservationId) {
    const modal = document.getElementById('refusalModal');
    const form = document.getElementById('refusalForm');
    form.action = "{{ route('chef.reservations.refuser', ['reservation' => ':id']) }}".replace(':id', reservationId);
    modal.classList.remove('hidden');
}

function closeRefusalModal() {
    document.getElementById('refusalModal').classList.add('hidden');
}
</script>

