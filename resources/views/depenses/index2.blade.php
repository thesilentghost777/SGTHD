@extends('pages.chef_production.chef_production_default')

@section('page-content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-blue-600">Gestion des Livraisons</h1>
        <a href="{{ route('depenses.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
            Nouvelle Depense ou Livraison
        </a>
    </div>

    <!-- Explication des fonctionnalités de la session -->
    <div class="bg-blue-100 text-blue-700 p-4 rounded-lg mb-6">
        <p>
            Cette section vous permet de gérer toutes les dépenses liées à la production. Vous pouvez ajouter une nouvelle dépense,
            visualiser les informations détaillées (comme le nom, le prix, et le statut), et effectuer des actions comme la validation ou la modification.
        </p>
        <p class="mt-2">
            Les messages de succès, comme la confirmation des actions, apparaissent sous forme de notifications grâce à la gestion des sessions.
        </p>
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

    <div class="bg-white rounded-lg shadow-lg overflow-hidden ml-4">
        <table class="min-w-full">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Nom</th>
                    <th class="px-6 py-3 text-left">Matière</th>
                    <th class="px-6 py-3 text-left">Prix</th>
                    <th class="px-6 py-3 text-left">Auteur</th>
                    <th class="px-6 py-3 text-left">Statut</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($depenses as $depense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $depense->date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">{{ $depense->nom }}</td>
                        <td class="px-6 py-4">
                            {{ $depense->matiere->nom ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">{{ number_format($depense->prix, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4">{{ $depense->user->name }}</td>
                        <td class="px-6 py-4">
                            @if($depense->valider)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Validé</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded">En attente</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('depenses.edit', $depense) }}"
                               class="text-blue-600 hover:text-blue-900">Modifier</a>
                            @if($depense->type === 'livraison_matiere' && !$depense->valider)
                                <form action="{{ route('depenses.valider-livraison', $depense) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-green-600 hover:text-green-900">
                                        Valider livraison
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
