@extends('layout.customer')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Booking for Mini Pitch: <span id="fieldLabel">{{ $field->field_Label ?? 'Unknown' }}</span></h2>

    <div id="calendar" style="width: 95%; margin: 0 auto;"></div>
</div>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<style>
.fc-timegrid-slot {
    height: 80px !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar;

    function renderCalendar(events) {
        if (calendar) calendar.destroy();

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            slotMinTime: "08:00:00",
            slotMaxTime: "24:00:00",
            slotDuration: "02:00:00",
            allDaySlot: false,
            expandRows: true,
            height: "auto",
            contentHeight: 1200,
            dayHeaderFormat: { weekday: 'long' },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: events,
            eventClick: function(info) {
                alert('Slot status: ' + info.event.extendedProps.status);
            }
        });

        calendar.render();
    }

    // Initial load
    renderCalendar(@json($slotsForCalendar));

    // Make calendar responsive
    window.addEventListener('resize', function() {
        if (calendar) calendar.updateSize();
    });
});
</script>
@endsection
