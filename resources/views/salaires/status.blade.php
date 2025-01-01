@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card">
        <h2 class="card-title">Statut de votre demande d'avance</h2>

        @if(!$as)
            <p class="text-gray-600">Aucune demande d'avance en cours.</p>
        @else
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded">
                    <span class="font-semibold">Montant demandé:</span>
                    <span>{{ number_format($as->sommeAs, 0, ',', ' ') }} XAF</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded">
                    <span class="font-semibold">Statut:</span>
                    <span class="px-3 py-1 rounded-full {{ $as->flag ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $as->flag ? 'Approuvée' : 'En attente' }}
                    </span>
                </div>

                @if($as->flag && !$as->retrait_valide)
                    <form action="{{ route('recup-retrait') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="as_id" value="{{ $as->id }}">
                        <button type="submit" class="btn-blue w-full">
                            Demander le retrait
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection