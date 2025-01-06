@props(['name', 'id' => null, 'selected' => null])

<select
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    {{ $attributes->merge(['class' => 'form-select rounded-md shadow-sm border-gray-300']) }}
>
    <option value="">Sélectionner une unité</option>
    <option value="g" {{ $selected == 'g' ? 'selected' : '' }}>Gramme (g)</option>
    <option value="kg" {{ $selected == 'kg' ? 'selected' : '' }}>Kilogramme (kg)</option>
    <option value="ml" {{ $selected == 'ml' ? 'selected' : '' }}>Millilitre (ml)</option>
    <option value="cl" {{ $selected == 'cl' ? 'selected' : '' }}>Centilitre (cl)</option>
    <option value="dl" {{ $selected == 'dl' ? 'selected' : '' }}>Décilitre (dl)</option>
    <option value="l" {{ $selected == 'l' ? 'selected' : '' }}>Litre (l)</option>
    <option value="cc" {{ $selected == 'cc' ? 'selected' : '' }}>Cuillère à café</option>
    <option value="cs" {{ $selected == 'cs' ? 'selected' : '' }}>Cuillère à soupe</option>
    <option value="pincee" {{ $selected == 'pincee' ? 'selected' : '' }}>Pincée</option>
    <option value="unite" {{ $selected == 'unite' ? 'selected' : '' }}>Unité</option>
</select>
