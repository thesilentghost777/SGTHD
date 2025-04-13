@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-6">Modifier le stagiaire</h2>

                    <form action="{{ route('stagiaires.update', $stagiaire) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                                <input type="text" name="nom" id="nom" value="{{ $stagiaire->nom }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                                <input type="text" name="prenom" id="prenom" value="{{ $stagiaire->prenom }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" id="email" value="{{ $stagiaire->email }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="tel" name="telephone" id="telephone" value="{{ $stagiaire->telephone }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="ecole" class="block text-sm font-medium text-gray-700">École</label>
                                <input type="text" name="ecole" id="ecole" value="{{ $stagiaire->ecole }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="niveau_etude" class="block text-sm font-medium text-gray-700">Niveau d'étude</label>
                                <input type="text" name="niveau_etude" id="niveau_etude" value="{{ $stagiaire->niveau_etude }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="filiere" class="block text-sm font-medium text-gray-700">Filière</label>
                                <input type="text" name="filiere" id="filiere" value="{{ $stagiaire->filiere }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="type_stage" class="block text-sm font-medium text-gray-700">Type de stage</label>
                                <select name="type_stage" id="type_stage" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="academique" {{ $stagiaire->type_stage === 'academique' ? 'selected' : '' }}>Académique</option>
                                    <option value="professionnel" {{ $stagiaire->type_stage === 'professionnel' ? 'selected' : '' }}>Professionnel</option>
                                </select>
                            </div>

                            <div>
                                <label for="departement" class="block text-sm font-medium text-gray-700">Secteur</label>
                                <select name="departement" id="departement" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="production" {{ $stagiaire->departement === 'production' ? 'selected' : '' }}>Production</option>
                                    <option value="alimentation" {{ $stagiaire->departement === 'alimentation' ? 'selected' : '' }}>Alimentation</option>
                                    <option value="administration" {{ $stagiaire->departement === 'administration' ? 'selected' : '' }}>Administration</option>
                                </select>
                            </div>

                            <div>
                                <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début</label>
                                <input type="date" name="date_debut" id="date_debut" value="{{ $stagiaire->date_debut->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin</label>
                                <input type="date" name="date_fin" id="date_fin" value="{{ $stagiaire->date_fin->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="nature_travail" class="block text-sm font-medium text-gray-700">Nature du travail</label>
                            <textarea name="nature_travail" id="nature_travail" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ $stagiaire->nature_travail }}</textarea>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('stagiaires.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                                Annuler
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
