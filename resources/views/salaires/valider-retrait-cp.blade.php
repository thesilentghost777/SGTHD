@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card">
        <h2 class="card-title">Validation des retraits (CP)</h2>

        @if($demandes->isEmpty())
            <p class="text-gray-600">Aucune demande de retrait en attente.</p>
        @else
            <div class="space-y-6">
                @foreach($demandes as $demande)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold">{{ $demande->employe->name }}</h3>
                                <p class="text-gray-600">Montant: {{ number_format($demande->sommeAs, 0, ',', ' ') }} XAF</p>
                            </div>
                            <form action="{{ route('recup-retrait-cp') }}" method="POST">
                                @csrf
                                <input type="hidden" name="as_id" value="{{ $demande->id }}">
                                <button type="submit" class="btn-blue">
                                    Valider le retrait
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection