@extends('layouts.app')
@section('content')
<div class="container max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-teal-500 rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl md:text-3xl font-bold text-white">Tableau de bord  - Gestion des annonces</h2>
    </div>

    <!-- Formulaire de création d'annonce -->
    <div class="bg-white rounded-lg shadow-md mb-8 overflow-hidden">
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
            <h3 class="text-xl font-semibold text-blue-800">Nouvelle annonce</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('announcements.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Titre</label>
                    <input type="text" name="title" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Contenu</label>
                    <textarea name="content" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" rows="4" required></textarea>
                </div>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-md shadow-md transition-all transform hover:scale-105">
                    Publier l'annonce
                </button>
            </form>
        </div>
    </div>

    <!-- Liste des annonces -->
    @foreach($announcements as $announcement)
    <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
            <div class="flex justify-between items-center">
                <h4 class="text-lg font-semibold text-blue-800">{{ $announcement->title }}</h4>
                <span class="text-sm text-gray-600">
                    Publié le {{ $announcement->created_at->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
        <div class="p-6">
            <p class="text-gray-700 mb-6">{{ $announcement->content }}</p>

            <!-- Section réactions -->
            <div class="border-t border-gray-100 pt-4">
                <h6 class="text-blue-800 font-medium mb-4">
                    Réactions ({{ $announcement->reactions->count() }})
                </h6>
                @foreach($announcement->reactions as $reaction)
                <div class="border-l-4 border-blue-200 pl-4 mb-4 hover:border-blue-400 transition-colors">
                    <div class="flex items-center gap-2 mb-1">
                        <strong class="text-gray-800">{{ $reaction->user->name }}</strong>
                        <span class="text-sm text-gray-500">
                            le {{ $reaction->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <p class="text-gray-600">{{ $reaction->comment }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

<!-- Ajoutez ces styles dans votre fichier CSS ou dans la section head -->
<style>
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }
    }
</style>
