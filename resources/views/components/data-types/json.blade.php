<div class="space-y-6">
    @foreach($data as $index => $item)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            {{-- En-tête de section --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">
                    Section {{ $index + 1 }}
                </h3>
            </div>

            {{-- Contenu --}}
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($item as $key => $value)
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                            {{-- En-tête de l'élément --}}
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-2 border-b border-gray-200">
                                <dt class="text-sm font-medium text-gray-700">
                                    {{ is_string($key) ? ucfirst($key) : "Élément " . ($key + 1) }}
                                </dt>
                            </div>

                            {{-- Valeur de l'élément --}}
                            <dd class="p-4">
                                @if(is_array($value) || is_object($value))
                                    <div class="relative">
                                        <pre class="bg-gray-50 p-3 rounded-lg overflow-x-auto text-xs font-mono text-gray-700 max-h-48">
                                            <code>{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                                        </pre>
                                        <div class="absolute bottom-0 right-0 p-1">
                                            <button class="text-xs text-blue-600 hover:text-blue-800" onclick="navigator.clipboard.writeText(this.parentElement.previousElementSibling.textContent.trim())">
                                                Copier
                                            </button>
                                        </div>
                                    </div>
                                @elseif(is_bool($value))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $value ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                        <span class="mr-2 h-2 w-2 rounded-full {{ $value ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                        {{ $value ? 'Vrai' : 'Faux' }}
                                    </span>
                                @elseif(is_numeric($value))
                                    <span class="text-base font-medium text-gray-900">
                                        {{ number_format($value, is_float($value) ? 2 : 0, ',', ' ') }}
                                    </span>
                                @elseif(is_string($value) && (strtotime($value) !== false))
                                    <span class="text-base text-gray-700">
                                        {{ \Carbon\Carbon::parse($value)->format('d/m/Y H:i') }}
                                    </span>
                                @else
                                    <span class="text-base text-gray-700">
                                        {{ $value }}
                                    </span>
                                @endif
                            </dd>

                            {{-- Métadonnées (si nécessaire) --}}
                            @if(is_numeric($value))
                                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
                                    <p class="text-xs text-gray-500">
                                        Type: {{ is_float($value) ? 'Décimal' : 'Entier' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
