@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-8">Mes Primes</h1>

    @if($hasPrimes)
    <div class="celebration-container mb-8">
        <div class="text-center">
            <div class="inline-block animate-bounce">
                🎉
            </div>
            <div class="inline-block animate-bounce delay-100">
                👏
            </div>
            <div class="inline-block animate-bounce delay-200">
                🌟
            </div>
        </div>
        <p class="text-center text-xl font-semibold text-green-600 mt-4">
            Félicitations ! Vous avez reçu des primes !
        </p>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Total des primes reçues</h2>
            <p class="text-3xl font-bold text-green-600">{{ number_format($totalPrimes, 0, ',', ' ') }} FCFA</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Catégorie
                        </th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Montant
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($primes as $prime)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $prime->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $prime->libelle }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-green-600 font-semibold">
                            {{ number_format($prime->montant, 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            Vous n'avez pas encore reçu de prime
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.celebration-container {
    animation: fadeIn 1s ease-out;
}

.delay-100 {
    animation-delay: 0.1s;
}

.delay-200 {
    animation-delay: 0.2s;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
