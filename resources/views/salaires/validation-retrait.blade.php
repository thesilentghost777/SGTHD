@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card">
        <h2 class="card-title">Validation du retrait</h2>

        @if(!$as)
            <p class="text-gray-600">Aucune avance à retirer.</p>
        @else
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded">
                    <p class="font-semibold">Montant à retirer: {{ number_format($as->sommeAs, 0, ',', ' ') }} XAF</p>
                </div>

                @if(!$as->retrait_demande)
                    <form action="{{ route('recup-retrait') }}" method="POST">
                        @csrf
                        <input type="hidden" name="as_id" value="{{ $as->id }}">
                        <button type="submit" class="btn-blue w-full">
                            Confirmer la demande de retrait
                        </button>
                    </form>
                @else
                    <div class="alert-success">
                        Demande de retrait envoyée. En attente de validation par le CP.
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection