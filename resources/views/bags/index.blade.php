@extends('pages/serveur/serveur_default')

@section('page-content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-blue-600">Gestion des Sacs</h1>
        <a href="{{ route('bags.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
            Nouveau Sac
        </a>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Succès!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="px-6 py-3 text-left">Nom</th>
                    <th class="px-6 py-3 text-left">Prix</th>
                    <th class="px-6 py-3 text-left">Stock</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($bags as $bag)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $bag->name }}</td>
                        <td class="px-6 py-4">{{ round(number_format($bag->price, 2),1) }} XAF</td>
                        <td class="px-6 py-4">
                            <span class="@if($bag->isLowStock()) text-red-600 font-bold @endif">
                                {{ $bag->stock_quantity }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($bag->isLowStock())
                                <button onclick="orderMore({{ $bag->id }})"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm">
                                    Commander
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function orderMore(bagId) {
    Swal.fire({
        title: 'Commander plus de stock?',
        text: "Voulez-vous créer une commande pour ce produit?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui, commander!',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement order logic here
            Swal.fire(
                'Commandé!',
                'La commande a été créée.',
                'success'
            );
        }
    });
}
</script>
@endsection
