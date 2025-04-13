@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-blue-600 mb-8">Nouveau Sac</h1>

        @if($errors->any())
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    html: '{!! implode("<br>", $errors->all()) !!}',
                    confirmButtonColor: '#3085d6'
                });
            </script>
        @endif

        <form action="{{ route('bags.store') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Nom du sac
                </label>
                <input type="text" name="name" id="name" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="price">
                    Prix
                </label>
                <input type="number" step="0.01" name="price" id="price" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="stock_quantity">
                    Quantité en stock
                </label>
                <input type="number" name="stock_quantity" id="stock_quantity" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="alert_threshold">
                    Seuil d'alerte
                </label>
                <input type="number" name="alert_threshold" id="alert_threshold" value="100" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection