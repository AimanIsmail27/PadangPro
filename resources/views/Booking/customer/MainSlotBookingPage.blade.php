@extends('layout.customer')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Booking for Field: {{ $field->field_Label ?? 'Unknown' }}</h2>

    <div id="calendar" style="width: 95%; margin: 0 auto;"></div>
</div>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Increase the height of each time slot row */
.fc-timegrid-slot {
    height: 80px !important; /* adjust: 60px = compact, 80px = taller */
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            slotMinTime: "08:00:00",   // Start at 8 AM
            slotMaxTime: "24:00:00",   // End at midnight
            slotDuration: "02:00:00",  // 2-hour increments
            allDaySlot: false,
            expandRows: true,
            height: "auto",
            contentHeight: 1200,       // overall calendar height
            dayHeaderFormat: { weekday: 'long' },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            // 🔄 Fetch events dynamically from backend (applies 10-min rule)
            events: `/booking/{{ $field->fieldID }}/slots-json`,

            eventClick: function(info) {
                // Extract details
                const slotStatus = info.event.extendedProps.status;
                const slotId = info.event.extendedProps.slotId; // ✅ real slot ID from DB
                const slotDate = new Date(info.event.start).toLocaleDateString();
                const slotTime = new Date(info.event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const slotEnd = new Date(info.event.end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const slotPrice = info.event.extendedProps.price; 
                // Show SweetAlert2 popup
                Swal.fire({
                    title: 'Slot Details',
                    html: `
                        <p><strong>Date:</strong> ${slotDate}</p>
                        <p><strong>Time:</strong> ${slotTime} - ${slotEnd}</p>
                        <p><strong>Status:</strong> ${slotStatus}</p>
                        <p><strong>Price:</strong> RM ${slotPrice}</p>
                    `,
                    icon: slotStatus === 'available' ? 'success' : 'error',
                    showCancelButton: slotStatus === 'available',
                    confirmButtonText: 'Continue to Booking',
                    cancelButtonText: 'Close',
                }).then((result) => {
                    if (result.isConfirmed && slotStatus === 'available') {
                        // ✅ Redirect to booking add page with slotID
                        window.location.href = `/booking/${slotId}/add`;
                    }
                });
            }
        });

        calendar.render();

        // 🔄 Auto-refresh events every 30 seconds (keeps expired bookings cleaned up)
        setInterval(function() {
            calendar.refetchEvents();
        }, 30000);
    }
});
</script>
@endsection
