
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Groupes de Produits</h1>
        <a href="{{ route('inventory.groups.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded flex items-center">
            <i class="fas fa-plus mr-2"></i> Nouveau Groupe
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        @if($groups->isEmpty())
            <div class="p-8 text-center">
                <p class="text-gray-500 mb-4">Aucun groupe de produits n'a été créé.</p>
                <a href="{{ route('inventory.groups.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    Créer votre premier groupe
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Nom</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Description</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Produits</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Créé le</th>
                            <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($groups as $group)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <a href="{{ route('inventory.groups.show', $group) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $group->name }}
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ $group->description ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ $group->products->count() }}</td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ $group->created_at->format('d/m/Y') }}</td>
                                <td class="py-3 px-4 text-sm text-gray-700">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('inventory.groups.show', $group) }}" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.groups.edit', $group) }}" class="text-amber-600 hover:text-amber-800">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('inventory.groups.destroy', $group) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
