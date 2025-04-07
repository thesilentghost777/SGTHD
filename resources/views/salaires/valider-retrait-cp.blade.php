@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-lg rounded-none border-l-4 border-blue-600">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="bg-green-500 w-4 h-4 inline-block mr-3"></span>
                Validation des retraits
            </h2>

            @if($demandes->isEmpty())
                <div class="bg-gray-50 p-8 rounded-none text-center">
                    <p class="text-gray-600 font-medium">Aucune demande de retrait en attente.</p>
                </div>
            @else
                <div class="space-y-4 md:space-y-6">
                    @foreach($demandes as $demande)
                        <div class="border-2 border-gray-200 rounded-none p-4 md:p-6 hover:shadow-md transition-all duration-200">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-blue-700">{{ $demande->employe->name }}</h3>
                                    <p class="text-gray-600 font-medium mt-1">Montant: <span class="text-green-600 font-bold">{{ number_format($demande->sommeAs, 0, ',', ' ') }} XAF</span></p>
                                </div>
                                <form action="{{ route('recup-retrait-cp') }}" method="POST" class="w-full md:w-auto">
                                    @csrf
                                    <input type="hidden" name="as_id" value="{{ $demande->id }}">
                                    <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-none border-2 border-blue-600 transition-colors duration-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
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
</div>
@endsection
