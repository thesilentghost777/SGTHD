<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        @if(empty($data))
            <tr>
                <td colspan="4" class="px-4 py-2 text-center text-2xl font-semibold text-gray-400">Aucun Résultat</td>
            </tr>
        @else
            <thead>
                <tr>
                    @foreach(array_keys((array)$data[0]) as $key)
                        <th class="px-4 py-2 text-sm font-medium text-gray-500 border">{{ ucfirst($key) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr>
                        @foreach((array)$row as $cell)
                            <td class="px-4 py-2 text-sm text-center border">{{ is_array($cell) || is_object($cell) ? json_encode($cell) : $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        @endif
    </table>
</div>
