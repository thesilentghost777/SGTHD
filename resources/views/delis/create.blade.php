<!-- resources/views/delis/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Incidents</h1>

        <form action="{{ route('delis.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">type</label>
                    <input type="text" name="nom" id="nom"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label for="montant" class="block text-sm font-medium text-gray-700">Santion(Montant)(F CFA)</label>
                    <input type="number" name="montant" id="montant"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <label for="date_incident" class="block text-sm font-medium text-gray-700">Date de l'incident</label>
                    <div class="flex gap-2">
                        <input type="date"
                               name="date_incident"
                               id="date_incident"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <button type="button"
                                onclick="setTodayDate()"
                                class="mt-1 inline-flex items-center px-3 py-2 border border-blue-500 text-sm font-medium rounded-md text-blue-500 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Aujourd'hui
                        </button>
                    </div>
                </div>



                <div>
                    <label class="block text-sm font-medium text-gray-700">Employés concernés</label>
                    <div class="mt-2 space-y-2">
                        @foreach($employes as $employe)
                        <div class="flex items-center">
                            <input type="checkbox" name="employes[]" value="{{ $employe->id }}"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label class="ml-2 text-sm text-gray-600">{{ $employe->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('delis.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Annuler
                </a>
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function setTodayDate() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const formattedDate = `${year}-${month}-${day}`;
        document.getElementById('date_incident').value = formattedDate;
    }
</script>
@endsection
