@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="card">
        <h2 class="card-title">Validation des demandes d'avance</h2>

        @if($demandes->isEmpty())
            <p class="text-gray-600">Aucune demande en attente.</p>
        @else
            <div class="space-y-6">
                @foreach($demandes as $demande)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-semibold">{{ $demande->employe->name }}</h3>
                                <p class="text-gray-600">Montant: {{ number_format($demande->sommeAs, 0, ',', ' ') }} XAF</p>
                            </div>
                            <form action="{{ route('store-validation') }}" method="POST" class="flex space-x-2">
                                @csrf
                                <input type="hidden" name="as_id" value="{{ $demande->id }}">
                                <button type="submit" name="decision" value="1" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                    Approuver
                                </button>
                                <button type="submit" name="decision" value="0" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                    Refuser
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