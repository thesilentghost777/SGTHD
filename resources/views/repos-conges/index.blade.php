@extends('pages.chef_production.chef_production_default')

@section('page-content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- En-tête -->
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Gestion des Repos et Congés</h1>
            <div class="h-1 w-32 bg-blue-400 rounded"></div>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-8 rounded-r shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-12">
            <!-- Formulaire -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-green-50">
                    <h2 class="text-2xl font-semibold text-gray-800">Définir les jours de repos et congés</h2>
                </div>

                <form action="{{ route('repos-conges.store') }}" method="POST" class="p-8 space-y-6">
                    @csrf

                    <!-- Sélection employé -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Employé</label>
                        <select name="employe_id" required class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-colors">
                            <option value="">Sélectionner un employé</option>
                            @foreach($employes as $employe)
                                <option value="{{ $employe->id }}">{{ $employe->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jour de repos -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-700">Jour de repos fixe</label>
                        <select name="jour" required class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-colors">
                            @foreach(['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'] as $jour)
                                <option value="{{ $jour }}">{{ ucfirst($jour) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Section Congés -->
                    <div class="pt-8 border-t border-gray-100">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Période de congés</h3>

                        <!-- Calendrier -->
                        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
                            <div id="mini-calendar"></div>
                        </div>

                        <!-- Dates et durée -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Date de début</label>
                                <input type="date" name="debut_c" id="debut_c"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Nombre de jours</label>
                                <input type="number" name="conges" id="conges" min="1"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                            </div>
                        </div>

                        <!-- Raison -->
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700">Motif du congé</label>
                                <select name="raison_c" id="raison_c"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                                    <option value="">Sélectionner un motif</option>
                                    <option value="maladie">Maladie</option>
                                    <option value="evenement">Événement</option>
                                    <option value="accouchement">Accouchement</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>

                            <div id="autre-raison-container" class="hidden space-y-2">
                                <label class="text-sm font-medium text-gray-700">Préciser le motif</label>
                                <input type="text" name="autre_raison"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                    placeholder="Veuillez préciser...">
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg
                                hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2
                                focus:ring-blue-500 focus:ring-offset-2 shadow-sm transition-all">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des congés -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-blue-50 to-green-50">
                    <h2 class="text-2xl font-semibold text-gray-800">Repos et congés actuels</h2>
                </div>

                <div class="p-8">
                    <div class="space-y-8">
                        @foreach($reposConges as $rc)
                        <div class="bg-gray-50 rounded-lg p-6 hover:bg-blue-50 transition-colors">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $rc->employe->name }}</h3>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                    {{ ucfirst($rc->jour) }}
                                </span>
                            </div>

                            @if($rc->conges)
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <p class="text-sm text-gray-500">Durée</p>
                                    <p class="font-medium">{{ $rc->conges }} jours</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Date de début</p>
                                    <p class="font-medium">{{ $rc->debut_c->format('d/m/Y') }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-500">Motif</p>
                                    <p class="font-medium">{{ ucfirst($rc->raison_c) }}
                                        @if($rc->raison_c === 'autre')
                                        <span class="text-gray-600">({{ $rc->autre_raison }})</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
.fc {
    font-family: inherit;
}
.fc-theme-standard th {
    background-color: #EFF6FF;
    padding: 8px 0;
}
.fc-daygrid-day.fc-day-selected {
    background-color: #60A5FA !important;
}
.fc-daygrid-day-frame {
    padding: 4px;
}
.fc-day-today {
    background-color: #F0FDF4 !important;
}
.fc-button {
    background-color: #3B82F6 !important;
    border-color: #2563EB !important;
}
.fc-button:hover {
    background-color: #2563EB !important;
    border-color: #1D4ED8 !important;
}
</style>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du calendrier
    const calendarEl = document.getElementById('mini-calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: ''
        },
        selectable: true,
        select: function(info) {
            const startDate = info.start;
            const endDate = info.end;
            const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));

            document.getElementById('debut_c').value = startDate.toISOString().split('T')[0];
            document.getElementById('conges').value = days;
        }
    });
    calendar.render();

    // Gestion du champ "autre raison"
    const raisonSelect = document.getElementById('raison_c');
    const autreRaisonContainer = document.getElementById('autre-raison-container');

    raisonSelect.addEventListener('change', function() {
        autreRaisonContainer.classList.toggle('hidden', this.value !== 'autre');
    });
});
</script>
@endsection
