@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Mon Planning</h1>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                @forelse($plannings as $planning)
                    <div class="mb-6 last:mb-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $planning->libelle }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    {{ $planning->date->format('d/m/Y') }}
                                    @if($planning->heure_debut)
                                        de {{ $planning->heure_debut->format('H:i') }}
                                        à {{ $planning->heure_fin->format('H:i') }}
                                    @endif
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                {{ $planning->type === 'repos' ?
                                    'bg-red-100 text-red-800' :
                                    'bg-blue-100 text-blue-800' }}">
                                {{ $planning->type === 'repos' ? 'Repos' : 'Tâche' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">
                        Aucun planning n'est défini pour le moment
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
