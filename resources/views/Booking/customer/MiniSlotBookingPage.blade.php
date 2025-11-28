@extends('layout.customer')

@section('content')
<div class="container mx-auto px-4 pb-12">
    
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Booking for {{ $field->field_Label ?? 'Unknown' }}</h2>
            <p class="text-gray-500 mt-1">Select a time slot to book your game.</p>
        </div>
        
        {{-- Average Rating Badge --}}
        <div class="mt-4 md:mt-0 flex items-center bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-200">
            <div class="flex text-yellow-400 text-lg mr-2">
                @for($i=1; $i<=5; $i++)
                    @if($i <= round($averageRating)) <i class="bi bi-star-fill"></i>
                    @else <i class="bi bi-star text-gray-300"></i>
                    @endif
                @endfor
            </div>
            <div class="text-gray-700 font-bold">
                {{ number_format($averageRating, 1) }} <span class="text-gray-400 font-normal text-sm">({{ $totalReviews }} reviews)</span>
            </div>
        </div>
    </div>

    {{-- Calendar Container --}}
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 mb-12">
        <div id="calendar"></div>
    </div>

    {{-- REVIEWS SECTION (Unchanged) --}}
    <div class="mt-12" id="reviews-section">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="bi bi-chat-quote-fill text-indigo-600"></i> Recent Reviews
        </h3>

        @if($reviews->isEmpty())
            <div class="bg-gray-50 rounded-xl p-8 text-center border border-dashed border-gray-300">
                <p class="text-gray-500 italic">No reviews yet for this field. Be the first to play and rate it!</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($reviews as $review)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                    {{ substr($review->customer->customer_FullName ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $review->customer->customer_FullName ?? 'Anonymous' }}</p>
                                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($review->review_Time, 'Asia/Kuala_Lumpur')->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex text-yellow-400 text-xs">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= $review->rating_Score ? '' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm italic">"{{ $review->review_Given }}"</p>
                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-6">
                {{ $reviews->fragment('reviews-section')->links() }}
            </div>
        @endif
    </div>

</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
/* Increase the height of each time slot row */
.fc-timegrid-slot { height: 80px !important; }
.fc-event { cursor: pointer; }
.fc-col-header-cell { background-color: #f8fafc; padding: 10px 0; color: #475569; }
.fc-button-primary { background-color: #4f46e5 !important; border-color: #4f46e5 !important; }
.fc-button-active { background-color: #4338ca !important; }

/* FIX: Hide unnecessary button text and control layout on mobile */
@media (max-width: 767px) {
    .fc-toolbar-chunk:last-child .fc-button-group > button:not(.fc-timeGridDay-button) { display: none !important; }
    .fc-today-button { text-indent: -9999px; position: relative; }
    .fc-today-button::before { content: '◉'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-indent: 0; font-size: 1rem; line-height: 1; }
    .fc-toolbar-title { font-size: 1.1rem !important; white-space: normal !important; line-height: 1.2 !important; }
}

/* CSS for mobile hint banner */
#landscapeHint { 
    transform: translateY(100%); /* Start off-screen */
}
</style>

{{-- === MISSING HTML BANNER (FIX) === --}}
<div id="landscapeHint" class="fixed bottom-0 left-0 right-0 p-3 z-50 shadow-2xl bg-indigo-600/95 backdrop-blur-sm transition-transform duration-500">
    <div class="flex items-center justify-between mx-auto max-w-lg">
        <span class="text-white text-sm font-medium">
            <i class="bi bi-phone-rotate mr-2 text-lg"></i> Rotate for best view of the Calendar!
        </span>
        <button onclick="document.getElementById('landscapeHint').style.transform = 'translateY(100%)';" class="text-white opacity-70 hover:opacity-100 transition">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</div>
{{-- === END MISSING HTML === --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    const hintBanner = document.getElementById('landscapeHint'); // NEW: Get the banner

    // Function to determine the initial view based on screen size (FIX)
    function getInitialView() {
        return window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek';
    }

    // Function to set the header content based on screen size (FIX)
    function getHeaderToolbar() {
        if (window.innerWidth < 768) {
            return { left: 'prev,next', center: 'title', right: 'today,timeGridDay' };
        } else {
            return { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' };
        }
    }

    // --- NEW: Orientation Check Function ---
    function checkOrientation() {
        const isMobile = window.innerWidth < 768;
        const isPortrait = window.matchMedia("(orientation: portrait)").matches;
        
        if (isMobile && isPortrait) {
            // Mobile and Portrait: Show the banner
            hintBanner.style.transform = 'translateY(0)';
        } else {
            // Desktop or Landscape: Hide the banner
            hintBanner.style.transform = 'translateY(100%)';
        }
    }


    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: getInitialView(),
            slotMinTime: "08:00:00",
            slotMaxTime: "24:00:00",
            slotDuration: "02:00:00",
            allDaySlot: false,
            expandRows: true,
            height: "auto",
            contentHeight: 1200,
            dayHeaderFormat: { weekday: 'long' },
            headerToolbar: getHeaderToolbar(),
            events: `/booking/{{ $field->fieldID }}/slots-json`,

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
                        <div class="text-left ml-8">
                        <p><strong>Date:</strong> ${slotDate}</p>
                        <p><strong>Time:</strong> ${slotTime} - ${slotEnd}</p>
                        <p><strong>Status:</strong> ${slotStatus}</p>
                        <p><strong>Price:</strong> RM ${slotPrice}</p>
                        </div>
                    `,
                    icon: slotStatus === 'available' ? 'success' : 'error',
                    showCancelButton: slotStatus === 'available',
                    confirmButtonText: 'Book Now',
                    confirmButtonColor: '#4f46e5',
                    cancelButtonText: 'Close',
                }).then((result) => {
                    if (result.isConfirmed && slotStatus === 'available') {
                        window.location.href = `/booking/${slotId}/add`;
                    }
                });
            }
        });

        calendar.render();
        checkOrientation(); // Initial check on load

        window.addEventListener('resize', function() {
            let isMobileNow = window.innerWidth < 768;
            let currentHeaderState = calendar.view.type === 'timeGridDay'; // Check if mobile view is active
            
            // 1. Update FullCalendar View/Header only if necessary
            if (isMobileNow !== currentHeaderState) {
                calendar.setOption('initialView', getInitialView());
                calendar.setOption('headerToolbar', getHeaderToolbar());
                calendar.changeView(getInitialView());
            }
            calendar.updateSize();
            
            // 2. Check and show/hide banner
            checkOrientation();
        });

        setInterval(function() {
            calendar.refetchEvents();
        }, 30000);
    }
});
</script>
@endsection