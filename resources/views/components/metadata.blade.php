@props(['dataType', 'data'])

<div class="bg-white rounded-lg shadow-lg p-6">

    <h2 class="text-lg font-semibold text-gray-900 mb-4">Métadonnées</h2>

    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div>

            <dt class="text-sm font-medium text-gray-500">Type</dt>

            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($dataType) }}</dd>

        </div>

        <div>

            <dt class="text-sm font-medium text-gray-500">Mise à jour</dt>

            <dd class="mt-1 text-sm text-gray-900">{{ now()->format('d/m/Y H:i') }}</dd>

        </div>

        @if(is_array($data) || is_object($data))

            <div>

                <dt class="text-sm font-medium text-gray-500">Taille</dt>

                <dd class="mt-1 text-sm text-gray-900">{{ is_array($data) ? count($data) : count((array)$data) }} éléments</dd>

            </div>

        @endif

    </dl>

</div>
