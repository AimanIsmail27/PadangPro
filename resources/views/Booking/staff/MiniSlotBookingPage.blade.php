@extends('layout.staff')

@section('title', 'Booking Calendar - PadangPro Staff')

@section('content')
{{-- This content is placed directly into the <main class="p-8"> of the layout --}}
<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8">
    <h2 class="mb-6 text-2xl font-bold text-gray-800">Booking for Field: {{ $field->field_Label ?? 'Unknown' }}</h2>

    <div id="calendar" class="w-full"></div>
</div>
@endsection

@push('scripts')
{{-- FullCalendar CSS and JS --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

{{-- SweetAlert2 for popups --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Custom FullCalendar row height */
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
            
            // --- URL UPDATED FOR STAFF ROUTE ---
            events: '{{ route("staff.booking.slots.json", $field->fieldID) }}',

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
                     // Staff theme color (Lime-500)
                }).then((result) => {
                    if (result.isConfirmed && slotStatus === 'available') {
                        
                        // --- URL UPDATED FOR STAFF ROUTE ---
                        window.location.href = `{{ url('staff/booking') }}/${slotId}/add`;
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