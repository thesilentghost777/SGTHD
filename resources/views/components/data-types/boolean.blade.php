<div class="space-y-4">

    <div class="text-center p-8">
        @if(empty($data))
            <span class="text-2xl font-semibold text-gray-400">Aucun Résultat</span>
        @else
            @foreach($data as $object)

                <div class="bg-gray-50 p-4 rounded-lg">

                    @foreach((array)$object as $key => $value)

                        <div class="mb-2">
                            <span class="text-sm font-medium text-gray-500">{{ ucfirst($key) }}:</span>

                            <span class="ml-2 text-sm text-gray-900">
                                @if(is_bool($value))
                                    <!-- Si c'est un booléen, afficher "Vrai" ou "Faux" avec style -->
                                    @if($value)
                                        <span class="text-green-600 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Vrai
                                        </span>
                                    @else
                                        <span class="text-red-600 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Faux
                                        </span>
                                    @endif
                                @else
                                    <!-- Sinon, afficher la valeur comme avant -->
                                    {{ is_scalar($value) ? $value : json_encode($value) }}
                                @endif
                            </span>
                        </div>

                    @endforeach

                </div>
            @endforeach
        @endif
    </div>

</div>

<!-- Style supplémentaire pour les animations -->
<style>
    /* Animation de brillance */
    .animate-pulse {
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 0.6;
            transform: scale(0.95);
        }
        50% {
            opacity: 1;
            transform: scale(1);
        }
        100% {
            opacity: 0.6;
            transform: scale(0.95);
        }
    }
</style>
