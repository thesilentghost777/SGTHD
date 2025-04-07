<div class="space-y-4">
    @foreach($data as $object)
        <div class="bg-gray-50 p-4 rounded-lg">
            <!-- Vérification si l'objet est une chaîne de caractères -->
            @if(is_string($object))
                <div class="mb-2">
                    <span class="text-sm font-medium text-gray-500">Valeur:</span>
                    <span class="ml-2 text-sm text-gray-900">{{ $object }}</span>
                </div>
            @else
                <!-- Si l'objet n'est pas une chaîne, traiter comme un tableau ou un objet -->
                @foreach((array)$object as $key => $value)
                    <div class="mb-2">
                        <span class="text-sm font-medium text-gray-500">{{ ucfirst($key) }}:</span>
                        <span class="ml-2 text-sm text-gray-900">{{ is_scalar($value) ? $value : json_encode($value) }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach
</div>
