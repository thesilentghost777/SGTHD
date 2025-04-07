<div class="space-y-4">

    <div class="text-center p-8">
        @if(empty($data))
            <span class="text-2xl font-semibold text-gray-400">Aucun Resultat</span>
        @else
        @foreach($data as $object)

        <div class="bg-gray-50 p-4 rounded-lg">

            @foreach((array)$object as $key => $value)

                <div class="mb-2">

                    <span class="text-sm font-medium text-gray-500">{{ ucfirst($key) }}:</span>

                    <span class="ml-2 text-sm text-gray-900">{{ is_scalar($value) ? $value : json_encode($value) }}</span>

                </div>

            @endforeach

        </div>

    @endforeach
        @endif
    </div>


</div>
