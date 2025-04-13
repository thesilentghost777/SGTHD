@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div id="calendar"></div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
.fc {
    --fc-button-bg-color: #2563eb;
    --fc-button-border-color: #2563eb;
    --fc-button-hover-bg-color: #1d4ed8;
    --fc-button-hover-border-color: #1d4ed8;
    --fc-today-bg-color: #dbeafe;
    --fc-event-bg-color: #3b82f6;
    --fc-event-border-color: #2563eb;
}

.fc-event {
    border-radius: 4px;
    padding: 2px;
}
</style>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: [
            @if($reposConge)
                {
                    title: 'Jour de repos',
                    daysOfWeek: [ {{ $jourNumber }} ],
                    color: '#22c55e',
                    textColor: '#ffffff'
                },
                @if($reposConge->conges)
                {
                    title: 'Congés',
                    start: '{{ $reposConge->debut_c->format("Y-m-d") }}',
                    end: '{{ $reposConge->debut_c->addDays($reposConge->conges)->format("Y-m-d") }}',
                    color: '#3b82f6',
                    textColor: '#ffffff'
                }
                @endif
            @endif
        ]
    });
    calendar.render();
});
</script>
@endsection
