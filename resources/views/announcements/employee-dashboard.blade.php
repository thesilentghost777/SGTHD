@extends('layouts.app')
@section('content')
<div class="container max-w-7xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-blue-600 to-teal-500 rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-2xl md:text-3xl font-bold text-white">Annonces de la direction</h2>
    </div>

    @foreach($announcements as $announcement)
    <div class="bg-white rounded-lg shadow-md mb-8 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
            <div class="flex flex-wrap justify-between items-center gap-2">
                <h3 class="text-xl font-semibold text-blue-800">{{ $announcement->title }}</h3>
                <div class="text-sm text-gray-600">
                    Par <span class="font-medium">{{ $announcement->user->name }}</span>
                    <span class="mx-1">•</span>
                    {{ $announcement->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="p-6">
            <p class="text-gray-700 leading-relaxed mb-8">{{ $announcement->content }}</p>

            <!-- Section réactions -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h4 class="text-lg font-semibold text-blue-800 mb-4">
                    Réactions ({{ $announcement->reactions->count() }})
                </h4>

                @foreach($announcement->reactions as $reaction)
                <div class="bg-white rounded-lg p-4 mb-3 border-l-4 border-blue-400 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="font-medium text-gray-800">{{ $reaction->user->name }}</span>
                        <span class="text-sm text-gray-500">
                            {{ $reaction->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <p class="text-gray-600">{{ $reaction->comment }}</p>
                </div>
                @endforeach
            </div>

            <!-- Formulaire de réaction -->
            <form action="{{ route('announcements.react', $announcement) }}" method="POST"
                  class="bg-blue-50 rounded-lg p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">
                        Votre réaction
                    </label>
                    <textarea
                        name="comment"
                        rows="2"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                        placeholder="Partagez votre avis..."
                    ></textarea>
                </div>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium rounded-md shadow-md transition-all transform hover:scale-105">
                    Envoyer ma réaction
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<style>
    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }
    }
</style>
@endsection
