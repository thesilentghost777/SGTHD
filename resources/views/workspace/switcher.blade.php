@extends('layouts.app')

@section('title', 'Changement de Mode de Travail')

@section('content')
<div class="container mx-auto py-8 px-4">
    <div class="bg-white max-w-5xl mx-auto rounded-lg shadow-md overflow-hidden">
        {{-- Header Section --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
            <h1 class="text-2xl font-bold mb-2">Changement de Mode de Travail</h1>
            <p class="text-blue-100">Accédez aux différents espaces de travail selon vos habilitations</p>

            <div class="mt-4 text-sm space-y-1">
                <div class="flex items-center">
                    <span class="font-semibold mr-2">Utilisateur:</span>
                    <span>{{ $user->name }}</span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold mr-2">Rôle principal:</span>
                    <span>{{ ucfirst($user->role) }}</span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold mr-2">Secteur:</span>
                    <span>{{ ucfirst($user->secteur) }}</span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold mr-2">Mode actuel:</span>
                    <span>{{ ucfirst($currentMode) }}</span>
                    <span class="ml-2 bg-green-100 text-green-600 px-2 py-0.5 rounded-full text-xs">Actif</span>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 m-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 m-4" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Available Modes --}}
        <div class="p-6">
            <h2 class="text-xl font-semibold text-blue-800 border-b-2 border-blue-200 pb-2 mb-6">
                Modes de travail disponibles
            </h2>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach($availableModes as $mode)
                    <div class="border rounded-lg p-5 {{ $currentMode === $mode['role'] ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}
                        relative transition-all duration-300 hover:shadow-md">

                        @if($currentMode === $mode['role'])
                            <div class="absolute top-0 right-0 bg-blue-500 text-white px-3 py-1 text-xs transform rotate-45 translate-x-1/4 -translate-y-1/4">
                                Mode Actuel
                            </div>
                        @endif

                        <h3 class="text-lg font-semibold text-blue-600 mb-2">{{ $mode['name'] }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ $mode['description'] }}</p>
                        <p class="text-xs text-gray-500 mb-4">Secteur: {{ ucfirst($mode['sector']) }}</p>

                        @if($currentMode !== $mode['role'])
                            <button
                                onclick="openSwitchModal('{{ $mode['role'] }}', '{{ $mode['sector'] }}', '{{ $mode['name'] }}')"
                                class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition-colors"
                            >
                                Changer de mode
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Switch Mode Modal --}}
        <div id="switchModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-blue-800">Confirmation de changement de mode</h2>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="switchModeForm" action="{{ route('workspace.switch') }}" method="POST">
                    @csrf
                    <input type="hidden" id="mode-input" name="mode">
                    <input type="hidden" id="sector-input" name="sector">

                    <p class="mb-4 text-gray-600">
                        Vous êtes sur le point de passer en mode
                        <span id="mode-name" class="font-semibold text-blue-600"></span>.
                    </p>

                    <div class="mb-4">
                        <label for="access_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Code d'accès:
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="access_code"
                                name="access_code"
                                value="2025"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            <button
                                type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition-colors"
                    >
                        Confirmer le changement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openSwitchModal(mode, sector, modeName) {
        const modeInput = document.getElementById('mode-input');
        const sectorInput = document.getElementById('sector-input');
        const modeNameElement = document.getElementById('mode-name');
        const modal = document.getElementById('switchModal');

        if (modeInput && sectorInput && modeNameElement && modal) {
            modeInput.value = mode;
            sectorInput.value = sector;
            modeNameElement.textContent = modeName;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal() {
        const modal = document.getElementById('switchModal');
        if (modal) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    }

    function togglePasswordVisibility() {
        const input = document.getElementById('access_code');
        const eyeIcon = document.getElementById('eye-icon');

        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            `;
        } else {
            input.type = 'password';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            `;
        }
    }

    // Event listener to close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('switchModal');
            if (modal && event.target === modal) {
                closeModal();
            }
        });
    });
</script>
@endsection