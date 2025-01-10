@extends('pages.chef_production.chef_production_default')

@section('page-content')

<div class="min-h-screen bg-gradient-to-r from-blue-50 to-green-50" x-data="dashboard()">
    <main class="py-8 px-6 sm:px-8 lg:px-10">
        <div class="p-4 mb-4 text-white bg-blue-500 rounded-lg shadow-md">
            <h4 class="text-lg font-semibold mb-2">💡 Conseil : Optimisez vos statistiques de production !</h4>
            <p>Assurez-vous d'avoir <strong>défini clairement la production attendue Journaliere</strong> pour mieux suivre et analyser vos performances. 🚀</p>
        </div>

        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Tableau de bord Chef de Production</h1>
            <button @click="showAssignmentModal = true"
                    class="inline-flex items-center px-5 py-3 border border-transparent rounded-md shadow-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Assigner production
            </button>

        </div>

        <!-- Horloge -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <div class="text-4xl font-bold text-center text-gray-800" x-text="currentTime">
                --:--:--
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <h3 class="text-sm font-medium text-gray-600">Production aujourd'hui</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($productionJour) }}</p>
                <div class="mt-1 text-sm text-gray-500">unités</div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <h3 class="text-sm font-medium text-gray-600">Chiffre d'affaire brut journalier</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($beneficeBrut) }}</p>
                <div class="mt-1 text-sm text-gray-500">FCFA</div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <h3 class="text-sm font-medium text-gray-600">Rendement</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($rendementData['pourcentage'], 1) }}%</p>
                <div class="mt-1 text-sm text-gray-500">
                    {{ number_format($rendementData['reel']) }}/{{ number_format($rendementData['attendu']) }} FCFA
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <h3 class="text-sm font-medium text-gray-600">Cout de production</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($pertes, 1) }}</p>
                <div class="mt-1 text-sm text-gray-500">FCFA</div>
                <div class="mt-1 text-sm text-gray-500">des matières premières</div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <h3 class="text-sm font-medium text-gray-600">Gaspillage de matière</h3>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($graphData['gaspillage'], 1) }}%</p>
                <div class="mt-1 text-sm text-gray-500">Pour qu'il soit fonctionnel, vous devriez avoir défini les matières recommandées</div>
            </div>

        </div>

    <!-- Graphiques - Modification de la structure -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Production journalière</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="productionChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Courbe de gaspillage de la matiere premiere</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="pertesChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des bénéfices</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="beneficesChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition de la production</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="repartitionChart"></canvas>
            </div>
        </div>
    </div>
 <!-- Productions en cours -->
 <div class="bg-white rounded-xl shadow-lg">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-green-50">
        <h2 class="text-lg font-medium text-gray-900">Production en cours</h2>
        <p class="mt-1 text-sm text-gray-500">Aujourd'hui</p>
    </div>

    <div class="p-6">
        @forelse($productionsEnCours as $production)
            <div class="mb-6 last:mb-0">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-900">
                        {{ $production['produit'] }} -
                        {{ $production['status'] == 1 ? 'Terminé' : 'En cours' }}
                        ({{ number_format($production['quantite_actuelle']) }}/{{ number_format($production['quantite_attendue']) }} unités)
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-500 h-3 rounded-full transition-all duration-500"
                         style="width: {{ min($production['progression'], 100) }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-4">Aucune production en cours</p>
        @endforelse
    </div>
</div>

<!-- Modal d'assignation -->
<div
    x-show="showAssignmentModal"
    class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-75 z-50"
    x-cloak>
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-lg mx-4"
        @click.away="showAssignmentModal = false">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Nouvelle production</h3>
            <button @click="showAssignmentModal = false"
                class="text-gray-400 hover:text-gray-600 transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9.293l4.95-4.95a1 1 0 111.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10 3.636 5.05a1 1 0 011.414-1.414L10 8.586z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="assignerProduction" class="p-6">
            <div class="space-y-5">

                <!-- Producteur -->
                <div>
                    <label for="producteur" class="block text-sm font-medium text-gray-700">Producteur</label>
                    <select
                        x-model="formData.producteur"
                        id="producteur"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner un producteur</option>
                        @foreach($producteurs as $producteur)
                            <option value="{{ $producteur->id }}">{{ $producteur->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Produit -->
                <div>
                    <label for="produit" class="block text-sm font-medium text-gray-700">Produit</label>
                    <select
                        x-model="formData.produit"
                        id="produit"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionner un produit</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->code_produit }}">{{ $produit->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantité prévue -->
                <div>
                    <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité prévue</label>
                    <input
                        type="number"
                        id="quantite"
                        x-model="formData.quantite"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea
                        id="notes"
                        x-model="formData.notes"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                <button
                    type="button"
                    @click="showAssignmentModal = false"
                    class="px-5 py-3 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md">
                    Annuler
                </button>
                <button
                    type="submit"
                    class="px-5 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-md">
                    Assigner
                </button>
            </div>
        </form>
    </div>
</div>

</main>
<meta name="csrf-token" content="{{ csrf_token() }}">
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function dashboard() {
    return {
        showAssignmentModal: false,
        currentTime: '--:--:--',
        formData: {
            producteur: '',
            produit: '',
            quantite: '',
            notes: ''
        },
        charts: [],
        mode: localStorage.getItem('mode') || 'chef_production',


        async assignerProduction() {
            try {
                // Validation des données
                if (!this.formData.producteur || !this.formData.produit || !this.formData.quantite) {
                    alert('Veuillez remplir tous les champs obligatoires');
                    return;
                }

                // Récupération du token CSRF
                const token = document.querySelector('meta[name="csrf-token"]').content;

                // Envoi de la requête
                const response = await fetch('/assigner-production', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.formData)
                });

                if (!response.ok) {
                    throw new Error('Erreur lors de l\'assignation');
                }

                const result = await response.json();

                // Fermeture du modal et réinitialisation du formulaire
                this.showAssignmentModal = false;
                this.formData = {
                    producteur: '',
                    produit: '',
                    quantite: '',
                    notes: ''
                };

                // Rafraîchissement des données
                window.location.reload();

            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de l\'assignation de la production');
            }
        },

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            this.initCharts();
            this.applyMode(this.mode); // Appliquer le mode au chargement
        },

        toggleMode(newMode) {
            this.mode = newMode;
            localStorage.setItem('mode', newMode);
            this.applyMode(newMode);
            // Recharger la page avec le nouveau mode
            window.location.href = `?mode=${newMode}`;
        },

        applyMode(mode) {
            const body = document.body;
            body.classList.remove('mode-employe', 'mode-chef');
            body.classList.add(`mode-${mode}`);

            // Masquer/afficher les éléments en fonction du mode
            const chefElements = document.querySelectorAll('.chef-only');
            const employeElements = document.querySelectorAll('.employe-only');

            chefElements.forEach(el => {
                el.style.display = mode === 'chef_production' ? 'block' : 'none';
            });

            employeElements.forEach(el => {
                el.style.display = mode === 'employe' ? 'block' : 'none';
            });
        },

        updateClock() {
            this.currentTime = new Date().toLocaleTimeString();
        },

        initCharts() {
    // Destruction des graphiques existants
    this.charts.forEach(chart => chart.destroy());
    this.charts = [];

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    };

    const lineOptions = {
        ...commonOptions,
        scales: {
            x: {
                grid: {
                    display: true
                },
                ticks: {
                    maxRotation: 0,
                    autoSkip: true,
                    maxTicksLimit: 12
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 5,
                    callback: function(value) {
                        return value.toLocaleString();
                    }
                }
            }
        },
        elements: {
            line: {
                tension: 0.4
            },
            point: {
                radius: 4
            }
        }
    };

    // Production Chart
    const productionData = @json($graphData['productions']);
    const productionChart = new Chart(document.getElementById('productionChart'), {
        type: 'line',
        data: {
            labels: productionData.map(item => item.timestamp.substring(0, 5)),
            datasets: [{
                label: 'Production (unités)',
                data: productionData.map(item => item.total),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true
            }]
        },
        options: lineOptions
    });
    this.charts.push(productionChart);

    // Pertes Chart
    const pertesData = @json($graphData['pertes']);
    const pertesChart = new Chart(document.getElementById('pertesChart'), {
        type: 'line',
        data: {
            labels: pertesData.map(item => item.timestamp.substring(0, 5)),
            datasets: [{
                label: 'Gaspillage (%)',
                data: pertesData.map(item => item.perte),
                borderColor: '#dc2626',
                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                fill: true
            }]
        },
        options: {
            ...lineOptions,
            scales: {
                ...lineOptions.scales,
                y: {
                    ...lineOptions.scales.y,
                    ticks: {
                        callback: function(value) {
                            return value.toFixed(1) + '%';
                        }
                    }
                }
            }
        }
    });
    this.charts.push(pertesChart);

    // Bénéfices Chart
    const beneficesData = @json($graphData['benefices']);
    const beneficesChart = new Chart(document.getElementById('beneficesChart'), {
        type: 'line',
        data: {
            labels: beneficesData.map(item => item.timestamp.substring(0, 5)),
            datasets: [{
                label: 'Bénéfices (FCFA)',
                data: beneficesData.map(item => item.benefice),
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                fill: true
            }]
        },
        options: {
            ...lineOptions,
            scales: {
                ...lineOptions.scales,
                y: {
                    ...lineOptions.scales.y,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' FCFA';
                        }
                    }
                }
            }
        }
    });
    this.charts.push(beneficesChart);

    // Répartition Chart
    const productionsData = @json($productionsEnCours);
    const repartitionChart = new Chart(document.getElementById('repartitionChart'), {
        type: 'doughnut',
        data: {
            labels: productionsData.map(p => p.produit),
            datasets: [{
                data: productionsData.map(p => p.quantite_actuelle),
                backgroundColor: [
                    '#2563eb',
                    '#059669',
                    '#dc2626',
                    '#d97706',
                    '#7c3aed'
                ]
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    this.charts.push(repartitionChart);
}
    }
}
</script>

@endsection



