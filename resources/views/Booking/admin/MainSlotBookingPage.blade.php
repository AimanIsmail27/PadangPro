@extends('layout.admin')

@section('title', 'Booking Calendar - PadangPro Admin')

@section('content')
{{-- This container mimics the original customer layout --}}
<div class="container-fluid">
    <h2 class="mb-4 text-2xl font-bold text-gray-800">Booking for Field: {{ $field->field_Label ?? 'Unknown' }}</h2>

    {{-- This div has the exact inline style for width and centering --}}
    <div id="calendar" style="width: 95%; margin: 0 auto;"></div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<style>
/* Replicating the exact style from the customer's page */
.fc-timegrid-slot {
    height: 80px !important;
}
.fc-event {
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
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
            
            // --- URL UPDATED FOR ADMIN ROUTE ---
            events: '{{ route("admin.booking.slots.json", $field->fieldID) }}',

            eventClick: function(info) {
                const slotStatus = info.event.extendedProps.status;
                const slotId = info.event.extendedProps.slotId;
                const slotDate = new Date(info.event.start).toLocaleDateString();
                const slotTime = new Date(info.event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const slotEnd = new Date(info.event.end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const slotPrice = info.event.extendedProps.price;

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
                        
                        // --- URL UPDATED FOR ADMIN ROUTE ---
                        window.location.href = `/admin/booking/${slotId}/add`;
                    }
                });
            }
        });

        calendar.render();

        // Auto-refresh events every 30 seconds
        setInterval(function() {
            calendar.refetchEvents();
        }, 30000);
    }
});
</script>
@endpush