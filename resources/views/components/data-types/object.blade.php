<div class="flex flex-col items-center p-6">
    @if(empty($data))
        <span class="text-xl font-semibold text-gray-400">Aucun Résultat</span>
    @else
        @foreach($data as $object)
            <div class="border border-gray-300 shadow-sm p-4 rounded-lg w-full max-w-md bg-white">
                @foreach((array)$object as $key => $value)
                    <div class="flex justify-between py-1 border-b last:border-b-0">
                        <span class="text-sm font-medium text-gray-500">{{ ucfirst($key) }}</span>
                        <span class="text-sm text-gray-900">{{ is_scalar($value) ? $value : json_encode($value) }}</span>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
