<!-- resources/views/employees/show.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Profil de {{ $user->name }}</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Informations de l'employé -->
                        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations personnelles</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Âge:</dt>
                                    <dd class="text-gray-900">{{ $user->age }} ans</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Email:</dt>
                                    <dd class="text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Téléphone:</dt>
                                    <dd class="text-gray-900">{{ $user->num_tel }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Secteur:</dt>
                                    <dd class="text-gray-900">{{ $user->secteur }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Année de début:</dt>
                                    <dd class="text-gray-900">{{ $user->annee_debut_service }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-600">Avance sur salaire:</dt>
                                    <dd class="text-gray-900">{{ number_format($user->avance_salaire, 0, ',', ' ') }} FCFA</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Formulaire d'évaluation -->
                        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Évaluation</h3>
                            <form action="{{ route('employees.evaluate', $user) }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="note" class="block text-sm font-medium text-gray-700">Note sur 20</label>
                                        <input type="number" name="note" id="note" min="0" max="20" step="0.5"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('note') border-red-500 @enderror"
                                            required>
                                        @error('note')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="appreciation" class="block text-sm font-medium text-gray-700">Appréciation</label>
                                        <textarea name="appreciation" id="appreciation" rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('appreciation') border-red-500 @enderror"
                                            required></textarea>
                                        @error('appreciation')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit"
                                        class="w-full bg-blue-600 text-white rounded-md py-2 px-4 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Enregistrer l'évaluation
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts pour SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Message de succès
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6'
            });
        @endif

        // Messages d'erreur de validation
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Erreur!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#3085d6'
            });
        @endif
    </script>
@endsection
