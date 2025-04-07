<!-- resources/views/components/deli-form.blade.php -->
@props(['deli' => null, 'employes' => []])

<div class="space-y-4">
    <div>
        <label for="nom" class="block text-sm font-medium text-gray-700">Nom du deli</label>
        <input type="text"
               name="nom"
               id="nom"
               value="{{ old('nom', $deli?->nom) }}"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nom') border-red-500 @enderror"
               required>
        @error('nom')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description détaillée</label>
        <textarea name="description"
                  id="description"
                  rows="4"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                  required>{{ old('description', $deli?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="montant" class="block text-sm font-medium text-gray-700">Montant (F CFA)</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <input type="number"
                   name="montant"
                   id="montant"
                   value="{{ old('montant', $deli?->montant) }}"
                   class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 @error('montant') border-red-500 @enderror"
                   required>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <span class="text-gray-500 sm:text-sm">F CFA</span>
            </div>
        </div>
        @error('montant')
            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Styles supplémentaires pour un look professionnel -->
    <style>
        .form-checkbox {
            @apply rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500;
        }
        .form-label {
            @apply block text-sm font-medium text-gray-700;
        }
        .form-input {
            @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500;
        }
    </style>
</div>
