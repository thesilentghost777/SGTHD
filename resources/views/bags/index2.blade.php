@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-700">Gestion des Sacs</h1>
        <a href="{{ route('bags.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded shadow transition duration-150 ease-in-out">
            <i class="fas fa-plus mr-2"></i> Nouveau Sac
        </a>
        <a href="{{ route('damaged-bags.index') }}" class="bg-green-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded shadow transition duration-150 ease-in-out">
            <i class="fas fa-plus mr-2"></i> Gerer les avaries
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

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-blue-50 text-blue-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Prix</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Seuil d'alerte</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bags as $bag)
                    <tr class="{{ $bag->stock_quantity <= $bag->alert_threshold ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $bag->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($bag->price, 0, ',', ' ') }} XAF</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="{{ $bag->stock_quantity <= $bag->alert_threshold ? 'text-red-600 font-semibold' : '' }}">
                                {{ number_format($bag->stock_quantity, 0, ',', ' ') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ number_format($bag->alert_threshold, 0, ',', ' ') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('bags.edit', $bag) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <form action="{{ route('bags.destroy', $bag) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce sac ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">Aucun sac trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
