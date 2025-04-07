@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">

        <div class="flex justify-between items-center">

            <h1 class="text-2xl font-bold text-blue-700">Détails du Sac</h1>

            <div class="flex space-x-2">

                <a href="{{ route('bags.edit', $bag) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded shadow transition duration-150 ease-in-out">

                    <i class="fas fa-edit mr-2"></i> Modifier

                </a>

                <a href="{{ route('bags.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded shadow transition duration-150 ease-in-out">

                    <i class="fas fa-arrow-left mr-2"></i> Retour

                </a>

            </div>

        </div>

    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">

        <div class="p-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>

                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations générales</h2>



                    <div class="space-y-3">

                        <div>

                            <span class="text-gray-600 font-medium">Nom:</span>

                            <span class="ml-2 text-gray-900">{{ $bag->name }}</span>

                        </div>



                        <div>

                            <span class="text-gray-600 font-medium">Prix unitaire:</span>

                            <span class="ml-2 text-gray-900">{{ number_format($bag->price, 2, ',', ' ') }} €</span>

                        </div>



                        <div>

                            <span class="text-gray-600 font-medium">Créé le:</span>

                            <span class="ml-2 text-gray-900">{{ $bag->created_at->format('d/m/Y H:i') }}</span>

                        </div>



                        <div>

                            <span class="text-gray-600 font-medium">Dernière mise à jour:</span>

                            <span class="ml-2 text-gray-900">{{ $bag->updated_at->format('d/m/Y H:i') }}</span>

                        </div>

                    </div>

                </div>



                <div>

                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Gestion du stock</h2>



                    <div class="space-y-3">

                        <div>

                            <span class="text-gray-600 font-medium">Quantité en stock:</span>

                            <span class="ml-2 {{ $bag->isLowStock() ? 'text-red-600 font-bold' : 'text-gray-900' }}">

                                {{ number_format($bag->stock_quantity, 0, ',', ' ') }}

                            </span>

                            @if($bag->isLowStock())

                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded ml-2">

                                    Stock bas

                                </span>

                            @endif

                        </div>



                        <div>

                            <span class="text-gray-600 font-medium">Seuil d'alerte:</span>

                            <span class="ml-2 text-gray-900">{{ number_format($bag->alert_threshold, 0, ',', ' ') }}</span>

                        </div>

                    </div>

                </div>

            </div>

            <div class="mt-8">

                <h2 class="text-lg font-semibold text-gray-800 mb-4">Assignations récentes</h2>



                @if($bag->assignments->count() > 0)

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serveur</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>

                                </tr>

                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach($bag->assignments->sortByDesc('created_at')->take(5) as $assignment)

                                    <tr>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

                                            {{ $assignment->user->name }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

                                            {{ number_format($assignment->quantity_assigned, 0, ',', ' ') }}

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

                                            {{ $assignment->created_at->format('d/m/Y H:i') }}

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    <p class="text-gray-500 italic">Aucune assignation pour ce sac.</p>

                @endif

            </div>

            <div class="mt-6 flex justify-center">

                <form action="{{ route('bags.destroy', $bag) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce sac ?')">

                    @csrf

                    @method('DELETE')

                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded shadow transition duration-150 ease-in-out">

                        <i class="fas fa-trash-alt mr-2"></i> Supprimer ce sac

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection
