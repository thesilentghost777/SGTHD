<div class="overflow-x-auto">
    <div class="text-center p-8">
        @if(empty($data))
            <span class="text-2xl font-semibold text-gray-400">Aucun Résultat</span>
        @else
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        @foreach(array_keys((array)$data[0]) as $key)
                            <th class="px-4 py-2 border text-gray-600 text-left">{{ ucfirst($key) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $object)
                        <tr class="border-b">
                            @foreach((array)$object as $value)
                                <td class="px-4 py-2 border text-gray-900">{{ is_scalar($value) ? $value : json_encode($value) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
