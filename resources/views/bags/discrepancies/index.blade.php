@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-blue-700">Incohérences des Sacs</h1>
        <p class="text-gray-600 mt-1">Visualisez les écarts entre les sacs assignés et reçus</p>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-blue-50 text-blue-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Date d'assignation</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Type de sac</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Serveur</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Quantité assignée</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Quantité reçue</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Écart</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($assignments as $assignment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $assignment->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $assignment->bag->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $assignment->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ $assignment->quantity_assigned }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $assignment->total_received }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $assignment->discrepancy > 0 ? 'text-red-600' : 'text-orange-600' }}">
                            {{ $assignment->discrepancy }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                            Aucune incohérence détectée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Légende explicative -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-blue-700 mb-3">Comment interpréter les écarts ?</h3>

        <div class="space-y-4">
            <div class="flex items-start">
                <div class="flex-shrink-0 h-5 w-5 rounded-full bg-red-600 mt-1"></div>
                <p class="ml-3 text-gray-700">
                    <span class="font-semibold">Écart positif :</span> Le serveur n'a pas encore reçu tous les sacs assignés.
                </p>
            </div>

            <div class="flex items-start">
                <div class="flex-shrink-0 h-5 w-5 rounded-full bg-orange-600 mt-1"></div>
                <p class="ml-3 text-gray-700">
                    <span class="font-semibold">Écart négatif :</span> Le serveur a reçu plus de sacs que ce qui a été assigné.
                </p>
            </div>

            <div class="flex items-start mt-4">
                <div class="flex-shrink-0 h-5 w-5 rounded-full bg-green-600 mt-1"></div>
                <p class="ml-3 text-gray-700">
                    <span class="font-semibold">Pas d'écart (non affiché) :</span> Les quantités assignées et reçues correspondent parfaitement.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
