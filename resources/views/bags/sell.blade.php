@extends('pages/serveur/serveur_default')

@section('page-content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-blue-600 mb-8">Vente de Sacs</h1>

        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#3085d6'
                });
            </script>
        @endif

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

        <form action="{{ route('bags.store-sold') }}" method="POST" class="bg-white rounded-lg shadow-lg p-6 mb-8">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="bag_id">
                    Sélectionner le sac
                </label>
                <select name="bag_id" id="bag_id" required
                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Choisir un sac</option>
                    @foreach($bags as $bag)
                        <option value="{{ $bag->id }}">
                            {{ $bag->name }}-{{ round($bag->price,1) }} XAF
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="quantity">
                    Quantité vendue
                </label>
                <input type="number" name="quantity" id="quantity" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6 relative">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="transaction_date">
                    Date de vente
                </label>
                <div class="flex">
                    <input type="date" name="transaction_date" id="transaction_date" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <button type="button" id="todayButton" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Aujourd'hui
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Enregistrer la vente
                </button>
            </div>
        </form>

        <!-- Liste des ventes -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <h2 class="text-xl font-bold p-4 bg-blue-500 text-white">Historique des Ventes</h2>
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sac</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($bags->flatMap->transactions->where('type', 'sold')->sortByDesc('transaction_date') as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->bag->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ number_format($transaction->quantity * $transaction->bag->price, 2) }} XAF
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('todayButton').addEventListener('click', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('transaction_date').value = today;
    });
</script>
@endsection
