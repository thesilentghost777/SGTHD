@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Centre de Messages</h1>
            <p class="text-gray-600">Gérez vos communications et notifications</p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Private Complaints Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500 hover:shadow-lg transition-shadow">
                <div class="text-3xl mb-4">🔒</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Plaintes privées</h3>
                <p class="text-gray-600 text-sm">Messages privés et confidentiels. L'identité de l'expéditeur reste anonyme.</p>
            </div>

            <!-- Suggestions Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500 hover:shadow-lg transition-shadow">
                <div class="text-3xl mb-4">💡</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Suggestions</h3>
                <p class="text-gray-600 text-sm">Idées et propositions d'amélioration de nos services.</p>
            </div>

            <!-- Reports Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-yellow-500 hover:shadow-lg transition-shadow">
                <div class="text-3xl mb-4">📝</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Reports</h3>
                <p class="text-gray-600 text-sm">Signalements et rapports d'incidents ou de problèmes.</p>
            </div>

            <!-- Tips Card -->
            <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-purple-500 hover:shadow-lg transition-shadow">
                <div class="text-3xl mb-4">ℹ️</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Conseils</h3>
                <p class="text-gray-600 text-sm">Après lecture, supprimez les messages traités ou à ignorer.</p>
            </div>
        </div>

        <!-- Messages Section -->
        <div class="space-y-6">
            <!-- Private Complaints -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-blue-500 text-white cursor-pointer flex justify-between items-center"
                     onclick="toggleMessages('complaint-private')">
                    <h2 class="text-xl font-semibold">Plaintes privées</h2>
                    @if($messages_complaint_private->where('read', false)->count() > 0)
                        <span class="bg-white text-blue-500 px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $messages_complaint_private->where('read', false)->count() }}
                        </span>
                    @endif
                </div>
                <div id="complaint-private" class="hidden">
                    @forelse($messages_complaint_private as $message)
                        <div class="border-b last:border-b-0 p-4 {{ $message->read ? 'bg-white' : 'bg-blue-50' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">Anonyme</p>
                                    <p class="text-gray-600 mt-2">{{ $message->message }}</p>
                                    <p class="text-sm text-gray-500 mt-2">{{ date('d/m/Y', strtotime($message->date_message)) }}</p>
                                </div>
                                <form action="{{ route('messages.destroy', ['message' => $message->id]) }}"
                                      method="POST" class="ml-4" onsubmit="return deleteMessage(this)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500">Aucun message</div>
                    @endforelse
                </div>
            </div>

            <!-- Suggestions -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-green-500 text-white cursor-pointer flex justify-between items-center"
                     onclick="toggleMessages('suggestion')">
                    <h2 class="text-xl font-semibold">Suggestions</h2>
                    @if($messages_suggestion->where('read', false)->count() > 0)
                        <span class="bg-white text-green-500 px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $messages_suggestion->where('read', false)->count() }}
                        </span>
                    @endif
                </div>
                <div id="suggestion" class="hidden">
                    @forelse($messages_suggestion as $message)
                        <div class="border-b last:border-b-0 p-4 {{ $message->read ? 'bg-white' : 'bg-green-50' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">
                                        {{ $message->name !== 'null' ? $message->name : 'Anonyme' }}
                                    </p>
                                    <p class="text-gray-600 mt-2">{{ $message->message }}</p>
                                    <p class="text-sm text-gray-500 mt-2">{{ date('d/m/Y', strtotime($message->date_message)) }}</p>
                                </div>
                                <form action="{{ route('messages.destroy', ['message' => $message->id]) }}"
                                      method="POST" class="ml-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors"
                                            onclick="return confirm('Supprimer ce message ?')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500">Aucun message</div>
                    @endforelse
                </div>
            </div>

            <!-- Reports -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-yellow-500 text-white cursor-pointer flex justify-between items-center"
                     onclick="toggleMessages('report')">
                    <h2 class="text-xl font-semibold">Reports</h2>
                    @if($messages_report->where('read', false)->count() > 0)
                        <span class="bg-white text-yellow-500 px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $messages_report->where('read', false)->count() }}
                        </span>
                    @endif
                </div>
                <div id="report" class="hidden">
                    @forelse($messages_report as $message)
                        <div class="border-b last:border-b-0 p-4 {{ $message->read ? 'bg-white' : 'bg-yellow-50' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">
                                        {{ $message->name !== 'null' ? $message->name : 'Anonyme' }}
                                    </p>
                                    <p class="text-gray-600 mt-2">{{ $message->message }}</p>
                                    <p class="text-sm text-gray-500 mt-2">{{ date('d/m/Y', strtotime($message->date_message)) }}</p>
                                </div>
                                <form action="{{ route('messages.destroy', ['message' => $message->id]) }}"
                                      method="POST" class="ml-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors"
                                            onclick="return confirm('Supprimer ce message ?')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-gray-500">Aucun message</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour au Dashboard
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleMessages(type) {
    const container = document.getElementById(type);
    const isHidden = container.classList.contains('hidden');

    // Hide all containers
    document.querySelectorAll('.messages-container').forEach(el => {
        el.classList.add('hidden');
    });

    // Toggle selected container
    if (isHidden) {
        container.classList.remove('hidden');

        // Mark messages as read
        fetch(`/messages/mark-read/${type}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => {
            // Update UI
            container.querySelectorAll('.bg-blue-50, .bg-green-50, .bg-yellow-50').forEach(msg => {
                msg.classList.remove('bg-blue-50', 'bg-green-50', 'bg-yellow-50');
                msg.classList.add('bg-white');
            });

            const countBadge = container.previousElementSibling.querySelector('span');
            if (countBadge) {
                countBadge.remove();
            }
        });
    } else {
        container.classList.add('hidden');
    }
}

function deleteMessage(formElement) {
    return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');
}
</script>
@endpush
@endsection