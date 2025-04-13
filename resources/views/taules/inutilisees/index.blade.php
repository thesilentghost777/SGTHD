@extends('layouts.app')

@section('content')
<x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Taules Inutilisées') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('taules.inutilisees.create') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Déclarer des taules inutilisées
                </a>
            </div>

            <!-- Mes taules inutilisées -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Mes taules inutilisées</h3>

                    @if(count($taulesDuProducteur) > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type de taule</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de déclaration</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($taulesDuProducteur as $taule)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->typeTaule->nom }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->nombre_taules }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">Vous n'avez pas déclaré de taules inutilisées.</p>
                    @endif
                </div>
            </div>

            <!-- Taules disponibles à récupérer -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Taules disponibles à récupérer</h3>

                    @if(count($taulesDisponibles) > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producteur</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type de taule</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de déclaration</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($taulesDisponibles as $taule)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->producteur->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->typeTaule->nom }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->nombre_taules }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('taules.inutilisees.recuperer', $taule) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900" onclick="return confirm('Êtes-vous sûr de vouloir récupérer ces taules ?')">Récupérer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">Aucune taule disponible à récupérer pour le moment.</p>
                    @endif
                </div>
            </div>

            <!-- Taules récupérées -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Taules que j'ai récupérées</h3>

                    @if(count($taulesRecuperees) > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producteur d'origine</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type de taule</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de récupération</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matière créée</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($taulesRecuperees as $taule)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->producteur->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->typeTaule->nom }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->nombre_taules }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->date_recuperation->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $taule->matiereCreee->nom }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">Vous n'avez pas encore récupéré de taules.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection