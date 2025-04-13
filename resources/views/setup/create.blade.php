@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 bg-blue-600 text-white">
            <h1 class="text-2xl font-bold">Configuration initiale de l'application</h1>
            <p class="mt-2">Veuillez configurer les informations de base pour commencer à utiliser l'application.</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 border-l-4 border-red-500">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Tabs navigation -->
        <div class="border-b border-gray-200">
            <ul class="flex w-full steps-nav" id="steps-nav">
                <li class="flex-1 active-tab" data-tab="step1">
                    <button class="w-full py-3 px-4 text-center border-b-2 border-blue-600 text-blue-600 focus:outline-none">
                        Informations générales
                    </button>
                </li>
                <li class="flex-1" data-tab="step2">
                    <button class="w-full py-3 px-4 text-center border-b-2 border-transparent text-gray-500 focus:outline-none">
                        Finances
                    </button>
                </li>
                <li class="flex-1" data-tab="step3">
                    <button class="w-full py-3 px-4 text-center border-b-2 border-transparent text-gray-500 focus:outline-none">
                        Finalisation
                    </button>
                </li>
            </ul>
        </div>

        <form id="setup-form" action="{{ route('setup.store') }}" method="POST" class="p-6">
            @csrf

            <!-- Tab 1: Informations générales -->
            <div id="step1" class="tab-content active-content">
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-xl font-semibold mb-4">Informations du complexe</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="nom" class="block text-gray-700 font-medium mb-2">Nom du complexe</label>
                            <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <p class="mt-1 text-sm text-gray-500">Entrez le nom officiel de votre complexe</p>
                        </div>

                        <div>
                            <label for="localisation" class="block text-gray-700 font-medium mb-2">Localisation</label>
                            <input type="text" name="localisation" id="localisation" value="{{ old('localisation') }}"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                            <p class="mt-1 text-sm text-gray-500">Adresse ou emplacement de votre complexe</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" class="next-btn px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Continuer
                    </button>
                </div>
            </div>

            <!-- Tab 2: Finances -->
            <div id="step2" class="tab-content hidden">
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-xl font-semibold mb-4">Finances du complexe</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="revenu_mensuel" class="block text-gray-700 font-medium mb-2">Revenu mensuel (FCFA)</label>
                            <input type="number" name="revenu_mensuel" id="revenu_mensuel" value="{{ old('revenu_mensuel', 0) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Revenus mensuels estimés</p>
                        </div>

                        <div>
                            <label for="revenu_annuel" class="block text-gray-700 font-medium mb-2">Revenu annuel (FCFA)</label>
                            <input type="number" name="revenu_annuel" id="revenu_annuel" value="{{ old('revenu_annuel', 0) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Revenus annuels estimés</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button type="button" class="prev-btn px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Retour
                    </button>
                    <button type="button" class="next-btn px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Continuer
                    </button>
                </div>
            </div>

            <!-- Tab 3: Finalisation -->
            <div id="step3" class="tab-content hidden">
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-xl font-semibold mb-4">Finalisation de la configuration</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="solde" class="block text-gray-700 font-medium mb-2">Solde actuel (FCFA)</label>
                            <input type="number" name="solde" id="solde" value="{{ old('solde', 0) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Solde financier actuel du complexe</p>
                        </div>

                        <div>
                            <label for="caisse_sociale" class="block text-gray-700 font-medium mb-2">Caisse sociale (FCFA)</label>
                            <input type="number" name="caisse_sociale" id="caisse_sociale" value="{{ old('caisse_sociale', 0) }}"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Montant alloué à la caisse sociale</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button type="button" class="prev-btn px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Retour
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Terminer la configuration
                    </button>
                </div>
            </div>
        </form>

        <!-- Progress indicator -->
        <div class="p-4 border-t">
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full progress-bar" style="width: 33%"></div>
            </div>
            <p class="mt-2 text-sm text-center"><span class="step-number">1</span> sur 3</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Sélectionner tous les éléments nécessaires
        const tabs = document.querySelectorAll('#steps-nav li');
        const contents = document.querySelectorAll('.tab-content');
        const nextButtons = document.querySelectorAll('.next-btn');
        const prevButtons = document.querySelectorAll('.prev-btn');
        const progressBar = document.querySelector('.progress-bar');
        const stepNumber = document.querySelector('.step-number');

        let currentStep = 1;
        const totalSteps = tabs.length;

        // Fonction pour changer d'onglet
        function changeTab(step) {
            // Mettre à jour les onglets
            tabs.forEach(tab => {
                tab.classList.remove('active-tab');
                tab.querySelector('button').classList.remove('border-blue-600', 'text-blue-600');
                tab.querySelector('button').classList.add('border-transparent', 'text-gray-500');
            });

            tabs[step-1].classList.add('active-tab');
            tabs[step-1].querySelector('button').classList.remove('border-transparent', 'text-gray-500');
            tabs[step-1].querySelector('button').classList.add('border-blue-600', 'text-blue-600');

            // Mettre à jour le contenu
            contents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active-content');
            });

            // Animation de transition
            const targetContent = document.getElementById('step' + step);

            // Afficher avec animation
            targetContent.classList.remove('hidden');
            targetContent.classList.add('active-content', 'animate-fade-in');

            // Mettre à jour la barre de progression
            const progress = (step / totalSteps) * 100;
            progressBar.style.width = progress + '%';
            stepNumber.textContent = step;

            // Mettre à jour l'étape courante
            currentStep = step;
        }

        // Event listener pour les onglets
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const step = parseInt(this.getAttribute('data-tab').replace('step', ''));
                changeTab(step);
            });
        });

        // Event listener pour les boutons "Suivant"
        nextButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                if (currentStep < totalSteps) {
                    // Validation simple du formulaire pour l'étape actuelle
                    let canProceed = true;

                    // Validation pour l'étape 1
                    if (currentStep === 1) {
                        const nom = document.getElementById('nom').value;
                        const localisation = document.getElementById('localisation').value;

                        if (!nom || !localisation) {
                            alert('Veuillez remplir tous les champs obligatoires.');
                            canProceed = false;
                        }
                    }

                    if (canProceed) {
                        changeTab(currentStep + 1);
                    }
                }
            });
        });

        // Event listener pour les boutons "Précédent"
        prevButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                if (currentStep > 1) {
                    changeTab(currentStep - 1);
                }
            });
        });

        // Animation CSS pour les transitions
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-fade-in {
                animation: fadeIn 0.3s ease-out forwards;
            }

            .active-tab button {
                border-bottom-width: 2px;
                border-color: #2563eb;
                color: #2563eb;
                font-weight: 600;
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endsection
