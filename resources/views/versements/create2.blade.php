@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
<div class="max-w-2xl mx-auto">
<div class="bg-white rounded-lg shadow-md overflow-hidden">
<div class="px-6 py-4 border-b border-gray-200">
<h2 class="text-xl font-bold text-gray-900">Nouveau Versement</h2>
</div>
<!-- Alerte informative sur la réinitialisation du solde CP -->
<div class="bg-yellow-50 px-6 py-3 border-b border-yellow-200">
    <p class="text-sm text-yellow-700">
        <span class="font-medium">Important :</span> Tout nouveau versement réinitialisera automatiquement le solde CP.
    </p>
</div>
<form action="{{ route('versements.store') }}" method="POST" class="p-6">
@csrf
<div class="mb-6">
<label for="libelle" class="block text-sm font-medium text-gray-700 mb-2">
 Libellé
</label>
<input type="text"
name="libelle"
id="libelle"
class="form-input w-full rounded-md shadow-sm"
value="{{ old('libelle') }}"
required>
@error('libelle')
<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div class="mb-6">
<label for="montant" class="block text-sm font-medium text-gray-700 mb-2">
 Montant (FCFA)
</label>
<input type="number"
name="montant"
id="montant"
class="form-input w-full rounded-md shadow-sm"
value="{{ old('montant') }}"
required>
@error('montant')
<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div class="mb-6">
<label for="date" class="block text-sm font-medium text-gray-700 mb-2">
 Date
</label>
<div class="flex items-center space-x-3">
<input type="date"
name="date"
id="date"
class="form-input w-full rounded-md shadow-sm"
value="{{ old('date') }}"
required>
<button type="button"
id="setToday"
class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300">
 Aujourd'hui
</button>
</div>
@error('date')
<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
<div class="flex justify-end space-x-3">
<a href="{{ route('versements.index') }}"
class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
 Annuler
</a>
<button type="submit"
class="px-4 py-2 bg-blue-500 text-white rounded-md text-sm font-medium hover:bg-blue-600">
 Enregistrer
</button>
</div>
</form>
</div>
</div>
</div>
<script>
document.getElementById('setToday').addEventListener('click', function () {
const today = new Date().toISOString().split('T')[0]; // Format YYYY-MM-DD
document.getElementById('date').value = today;
 });
document.getElementById('setTotalMontant').addEventListener('click', function () {
const totalMontant = {{ $total_today }}; // Injected from the server
document.getElementById('montant').value = totalMontant;
 });
</script>
@endsection