@extends('layouts.app')

@section('content')


<div class="container mx-auto px-4 py-8">
    @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Succès!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6'
            });
        @endif

        // Messages d'erreur de validation
@if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Erreur!',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#3085d6'
            });
@endif
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Calendrier -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Liste des employés et leurs jours de repos -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4 text-blue-700">Jours de repos</h2>
                <div class="space-y-4">
                    @foreach($employes as $employe)
                    <div class="border-b border-blue-100 pb-4">
                        <h3 class="font-medium text-blue-900">{{ $employe->name }}</h3>
                        @if($joursRepos->has($employe->id))
                            @foreach($joursRepos[$employe->id] as $repos)
                                <div class="mt-2 flex justify-between items-center">
                                    <span class="text-sm text-blue-600">
                                        {{ $repos->date->format('d/m/Y') }}
                                    </span>
                                    <form action="{{ route('planning.destroy', $repos->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800 text-sm">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        @else
                            <p class="text-sm text-blue-500 mt-1">Aucun jour de repos planifié</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout/modification -->
<div id="planningModal" class="fixed inset-0 bg-blue-900 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative">
            <button type="button" onclick="fermerModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <h3 class="text-lg font-bold mb-4 text-blue-700" id="modalTitle">Ajouter un planning</h3>

            <form id="planningForm" class="space-y-4" method="POST" action="{{ route('planning.store') }}">
                @csrf
                <input type="hidden" name="_method" id="methodField" value="POST">
                <input type="hidden" name="id" id="eventId">

                <div>
                    <label class="block text-sm font-medium text-blue-700">Type</label>
                    <select id="type" name="type" required class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="tache">Tâche</option>
                        <option value="repos">Jour libre</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-blue-700">Employé</label>
                    <select id="employe_id" name="employe_id" required class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}">{{ $employe->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-blue-700">Libellé</label>
                    <input type="text" id="libelle" name="libelle" required
                           class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-blue-700">Date</label>
                    <input type="date" id="date" name="date" required
                           class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-700">Heure début</label>
                        <input type="time" id="heure_debut" name="heure_debut" required
                               class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-blue-700">Heure fin</label>
                        <input type="time" id="heure_fin" name="heure_fin" required
                               class="mt-1 block w-full rounded-md border-blue-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="fermerModal()"
                            class="px-4 py-2 border border-blue-300 rounded-md text-sm font-medium text-blue-700 hover:bg-blue-50">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<style>
.fc {
    --fc-button-bg-color: #2563eb;
    --fc-button-border-color: #2563eb;
    --fc-button-hover-bg-color: #1d4ed8;
    --fc-button-hover-border-color: #1d4ed8;
    --fc-button-active-bg-color: #1e40af;
    --fc-button-active-border-color: #1e40af;
    --fc-today-bg-color: #dbeafe;
    --fc-border-color: #bfdbfe;
    --fc-event-bg-color: #3b82f6;
    --fc-event-border-color: #2563eb;
}

.fc-daygrid-day.fc-day-today {
    background-color: #dbeafe !important;
}

.fc-button-primary {
    text-transform: capitalize !important;
}

.fc-event {
    border-radius: 4px;
    padding: 2px;
    cursor: pointer;
}

.fc-event-title {
    font-weight: 500;
}

.fc th {
    background-color: #eff6ff;
    color: #1e40af;
    padding: 8px 0;
}

.hidden {
    display: none;
}

#planningModal {
    transition: opacity 0.2s ease-in-out;
}
</style>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>

<script>



document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    let currentEventId = null;

    initCalendar();

    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            selectable: true,
            select: function(info) {
                ouvrirModal(info.startStr);
            },
            eventClick: function(info) {
                modifierEvent(info.event);
            },
            events: '{{ route("planning.events") }}',
            eventContent: function(arg) {
                return {
                    html: `
                        <div class="fc-content">
                            <div class="text-xs font-bold text-white">${arg.event.title}</div>
                            <div class="text-xs text-blue-50">${arg.event.extendedProps.employe || ''}</div>
                            ${arg.event.extendedProps.type === 'repos' ?
                                '<div class="text-xs italic text-blue-50">Repos</div>' : ''}
                        </div>
                    `
                };
            },
            eventClassNames: function(arg) {
                return ['shadow-sm'];
            }
        });

        calendar.render();
    }

    const modal = document.getElementById('planningModal');
    const planningForm = document.getElementById('planningForm');

    window.ouvrirModal = function(date = null) {
        currentEventId = null;
        document.getElementById('modalTitle').textContent = 'Ajouter un planning';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('eventId').value = '';
        planningForm.reset();

        if (date) {
            document.getElementById('date').value = date;
        }
        modal.classList.remove('hidden');
    }

    window.fermerModal = function() {
        modal.classList.add('hidden');
        currentEventId = null;
        planningForm.reset();
    }

    window.modifierEvent = function(event) {
        currentEventId = event.id;
        document.getElementById('modalTitle').textContent = 'Modifier le planning';
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('eventId').value = event.id;

        document.getElementById('type').value = event.extendedProps.type || 'tache';
        document.getElementById('employe_id').value = event.extendedProps.employe_id || '';
        document.getElementById('libelle').value = event.title || '';

        if (event.start) {
            document.getElementById('date').value = event.start.toISOString().split('T')[0];
            document.getElementById('heure_debut').value = event.start.toTimeString().slice(0,5);
        }

        if (event.end) {
            document.getElementById('heure_fin').value = event.end.toTimeString().slice(0,5);
        }

        modal.classList.remove('hidden');
    }

    planningForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const method = document.getElementById('methodField').value;
        const url = currentEventId
            ? '{{ route("planning.update", "") }}/' + currentEventId
            : '{{ route("planning.store") }}';

        try {
            const response = await fetch(url, {
                method: method === 'PUT' ? 'POST' : method,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Une erreur est survenue');
            }

            await calendar.refetchEvents();
            fermerModal();
            window.location.reload();

        } catch (error) {
            alert(error.message);
        }
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            fermerModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            fermerModal();
        }
    });
});
</script>
@endsection
