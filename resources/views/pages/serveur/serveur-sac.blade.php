@extends('pages/serveur/serveur_default')

@section('page-content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-xl mx-auto">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-8">Quelle opération voulez-vous effectuer ?</h1>

        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="space-y-4">
                <a href="{{ route('bag.receptions.create') }}" class="block bg-green-500 hover:bg-green-700 text-white text-center font-bold py-4 px-6 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                    📦 Sacs/Contenants reçus
                </a>

                <a href="{{ route('bag.sales.create') }}" class="block bg-blue-500 hover:bg-blue-700 text-white text-center font-bold py-4 px-6 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
                    💰 Sacs/Contenants vendus
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
