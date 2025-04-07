@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Test des Notifications</h2>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('notifications.send') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="recipient_id" class="block text-sm font-medium text-gray-700">Destinataire</label>
                    <select name="recipient_id" id="recipient_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Sélectionner un destinataire</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('recipient_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('recipient_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Sujet</label>
                    <input type="text" name="subject" id="subject" required value="{{ old('subject') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea name="message" id="message" rows="4" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Envoyer la notification
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Vos notifications non lues</h3>
            <div id="notifications-list" class="space-y-4">
                <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadNotifications() {
    fetch('/notifications/unread')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(notifications => {
            const container = document.getElementById('notifications-list');
            if (!notifications || notifications.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center">Aucune notification non lue</p>';
                return;
            }

            container.innerHTML = notifications.map(notification => `
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${notification.data.subject}</p>
                            <p class="text-gray-600 mt-1">${notification.data.message}</p>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-sm text-gray-500">De: ${notification.data.sender_name}</p>
                                <p class="text-sm text-gray-500">${new Date(notification.data.created_at).toLocaleString()}</p>
                            </div>
                        </div>
                        <button onclick="markAsRead('${notification.id}')"
                                class="ml-4 text-blue-500 hover:text-blue-700 focus:outline-none">
                            Marquer comme lu
                        </button>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Erreur:', error);
            const container = document.getElementById('notifications-list');
            container.innerHTML = '<p class="text-red-500 text-center">Erreur lors du chargement des notifications</p>';
        });
}

function markAsRead(id) {
    fetch(`/notifications/${id}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.json();
    })
    .then(() => {
        loadNotifications();
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du marquage de la notification comme lue');
    });
}

// Charger les notifications au chargement de la page
document.addEventListener('DOMContentLoaded', loadNotifications);

// Rafraîchir les notifications toutes les 30 secondes
setInterval(loadNotifications, 30000);
</script>
@endpush
@endsection