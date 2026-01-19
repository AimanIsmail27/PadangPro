@extends('layout.admin')

@section('title', 'Manage Slot Prices - PadangPro Admin')

@section('content')
<div class="container mx-auto px-4 pb-12">

    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Manage Slot Prices</h2>
            <p class="text-gray-500 mt-1">
                Select slots and apply promotional pricing in bulk.
            </p>
        </div>

        {{-- Field Selector + Help --}}
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('admin.booking.slot.price.manage') }}">
                <select
                    class="border rounded-lg px-4 py-2 bg-white"
                    onchange="window.location.href='{{ url('/admin/booking/slot-price/manage') }}/' + this.value"
                >
                    @foreach($allFields as $f)
                        <option value="{{ $f->fieldID }}" {{ $f->fieldID == $field->fieldID ? 'selected' : '' }}>
                            Field {{ $f->field_Label }}
                        </option>
                    @endforeach
                </select>
            </form>

            <button
                id="showGuideAgain"
                type="button"
                class="px-3 py-2 text-sm rounded-lg border bg-white hover:bg-gray-50 text-gray-700"
                title="Show the step-by-step guide again"
            >
                ❓ Help
            </button>
        </div>
    </div>

    {{-- Live Guidance Card --}}
    <div id="guideCard" class="bg-white border border-gray-100 shadow-sm rounded-xl p-5 mb-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">Quick Guide</p>
                <h3 id="guideTitle" class="text-lg font-bold text-gray-800 mt-1">Step 1: Understand the colors</h3>
                <p id="guideText" class="text-gray-600 mt-2 leading-relaxed">
                    Amber outline = slot price has been changed from default. Dark outline = selected slots for update.
                </p>

                <div class="mt-3 flex flex-wrap gap-2 text-sm">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                        <span class="w-3 h-3 rounded-sm border-2 border-amber-500"></span> Price Changed
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                        <span class="w-3 h-3 rounded-sm border-2 border-gray-900"></span> Selected
                    </span>
                </div>
            </div>

            <button id="guideSkip" type="button" class="text-gray-400 hover:text-gray-700 text-sm font-semibold">
                Skip ✕
            </button>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="text-xs text-gray-400">
                <span id="guideProgress">1</span> / <span id="guideTotal">4</span>
            </div>
            <div class="flex gap-2">
                <button id="guidePrev" type="button" class="px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm" disabled>
                    Back
                </button>
                <button id="guideNext" type="button" class="px-3 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm">
                    Next
                </button>
            </div>
        </div>
    </div>

    {{-- Calendar --}}
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div id="calendar" class="w-full"></div>
    </div>

    {{-- Floating selection bar --}}
    <div
        id="selectionBar"
        class="fixed bottom-5 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-xl hidden z-50"
    >
        <div class="selection-content flex items-center gap-3">
            <span id="selectedCount" class="font-semibold">0 selected</span>

            <button id="clearSelection" type="button" class="px-3 py-1 rounded-lg bg-white/10 hover:bg-white/20">
                Clear
            </button>

            <button id="applyPrice" type="button" class="px-3 py-1 rounded-lg bg-indigo-500 hover:bg-indigo-600">
                Apply Price
            </button>
        </div>
    </div>

</div>

{{-- FullCalendar & SweetAlert --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .fc-timegrid-slot { height: 80px !important; }
    .fc-event { cursor: pointer; }
    .fc-button-primary { background-color: #4f46e5 !important; border-color: #4f46e5 !important; }

    /* Make sure calendar never overflows its container */
    #calendar { max-width: 100%; }
    .fc { max-width: 100%; }

    /* Selected by admin */
    .slot-selected {
        outline: 3px solid #111827 !important;
        filter: brightness(0.92);
    }

    /* Price overridden (detected by controller logic) */
    .slot-price-changed {
        outline: 3px solid #f59e0b !important;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
    }

    /* Mobile toolbar tweaks (mirror your MainSlotBookingPage behavior) */
    @media (max-width: 767px) {
        .fc-toolbar-chunk:last-child .fc-button-group > button:not(.fc-timeGridDay-button) {
            display: none !important;
        }
        .fc-today-button {
            text-indent: -9999px;
            position: relative;
        }
        .fc-today-button::before {
            content: '◉';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-indent: 0;
            font-size: 1rem;
            line-height: 1;
        }
        .fc-toolbar-title {
            font-size: 1.1rem !important;
            white-space: normal !important;
            line-height: 1.2 !important;
        }
    }

    #landscapeHint { transform: translateY(100%); transition: transform 0.5s; }

    @media (max-width: 767px) {
    #selectionBar {
        left: 0 !important;
        right: 0 !important;

        /* kill Tailwind translate + transform */
        --tw-translate-x: 0 !important;
        --tw-translate-y: 0 !important;
        transform: none !important;

        width: 70% !important;          /* ~2/3 size */
        max-width: 320px !important;
        margin: 0 auto !important;      /* ✅ real centering */

        padding: 10px !important;
        border-radius: 14px;
    }

    #selectionBar .selection-content {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }

    #selectedCount {
        text-align: center;
        font-size: 0.9rem;
    }

    #selectionBar button {
        width: 100%;
        padding: 8px 0;
        font-size: 0.9rem;
        border-radius: 9px;
    }
}



</style>

{{-- Orientation Hint Banner --}}
<div id="landscapeHint" class="fixed bottom-0 left-0 right-0 p-3 z-40 shadow-2xl bg-indigo-600/95 backdrop-blur-sm">
    <div class="flex items-center justify-between mx-auto max-w-lg">
        <span class="text-white text-sm font-medium">
            📱 Rotate for best calendar view
        </span>
        <button
            type="button"
            onclick="document.getElementById('landscapeHint').style.transform='translateY(100%)';"
            class="text-white opacity-70 hover:opacity-100 transition"
        >
            ✕
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // -------------------------
    // Live Guidance (card)
    // -------------------------
    const guideCard = document.getElementById('guideCard');
    const guideTitle = document.getElementById('guideTitle');
    const guideText = document.getElementById('guideText');
    const guidePrev = document.getElementById('guidePrev');
    const guideNext = document.getElementById('guideNext');
    const guideSkip = document.getElementById('guideSkip');
    const guideProgress = document.getElementById('guideProgress');
    const guideTotal = document.getElementById('guideTotal');
    const showGuideBtn = document.getElementById('showGuideAgain');

    const steps = [
        {
            title: "Step 1: Understand the colors",
            text: "Amber outline = slot price has been changed from default. Dark outline = selected slots you will update."
        },
        {
            title: "Step 2: Select available slots",
            text: "Click an AVAILABLE slot to add it to your selection. Booked or past slots cannot be selected."
        },
        {
            title: "Step 3: Select more (optional)",
            text: "After selecting a slot, choose 'Select more' to keep adding slots, or continue to set a new price."
        },
        {
            title: "Step 4: Apply new price",
            text: "Tap 'Apply Price' (or choose 'No, apply price') to enter the new RM price and update all selected slots."
        }
    ];

    let stepIndex = 0;
    guideTotal.textContent = steps.length;

    function renderStep() {
        guideTitle.textContent = steps[stepIndex].title;
        guideText.textContent = steps[stepIndex].text;
        guideProgress.textContent = (stepIndex + 1);
        guidePrev.disabled = stepIndex === 0;
        guideNext.textContent = (stepIndex === steps.length - 1) ? "Done" : "Next";
    }

    function hideGuideForever() {
        localStorage.setItem('padangpro_slot_price_guide_hidden', '1');
        guideCard.classList.add('hidden');
    }

    function showGuideAgain() {
        localStorage.removeItem('padangpro_slot_price_guide_hidden');
        stepIndex = 0;
        guideCard.classList.remove('hidden');
        renderStep();
    }

    // Initial state
    if (localStorage.getItem('padangpro_slot_price_guide_hidden') === '1') {
        guideCard.classList.add('hidden');
    } else {
        renderStep();
    }

    guidePrev?.addEventListener('click', () => {
        if (stepIndex > 0) stepIndex--;
        renderStep();
    });

    guideNext?.addEventListener('click', () => {
        if (stepIndex < steps.length - 1) {
            stepIndex++;
            renderStep();
        } else {
            hideGuideForever();
        }
    });

    guideSkip?.addEventListener('click', hideGuideForever);
    showGuideBtn?.addEventListener('click', showGuideAgain);

    // -------------------------
    // Calendar (responsive behavior like MainSlotBookingPage)
    // -------------------------
    const calendarEl = document.getElementById('calendar');
    const hintBanner = document.getElementById('landscapeHint');

    let selectedSlots = new Set();
    const selectionBar = document.getElementById('selectionBar');
    const selectedCountEl = document.getElementById('selectedCount');

    function updateSelectionUI() {
        const count = selectedSlots.size;
        selectedCountEl.textContent = `${count} selected`;
        selectionBar.classList.toggle('hidden', count === 0);
    }

    function toggleEventSelected(eventObj, selected) {
        if (!eventObj.el) return;
        eventObj.el.classList.toggle('slot-selected', selected);
    }

    function getInitialView() {
        return window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek';
    }

    function getHeaderToolbar() {
        if (window.innerWidth < 768) {
            return { left: 'prev,next', center: 'title', right: 'today,timeGridDay' };
        } else {
            return { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' };
        }
    }

    function checkOrientation() {
        const isMobile = window.innerWidth < 768;
        const isPortrait = window.matchMedia("(orientation: portrait)").matches;
        hintBanner.style.transform = (isMobile && isPortrait) ? 'translateY(0)' : 'translateY(100%)';
    }

    async function askAddOrRemove(slotId, status) {
        if (status !== 'available') {
            await Swal.fire({
                icon: 'warning',
                title: 'Not editable',
                text: 'Only AVAILABLE (not booked / not past) slots can be selected.',
                confirmButtonColor: '#4f46e5'
            });
            return null;
        }

        const isAlready = selectedSlots.has(slotId);

        const result = await Swal.fire({
            icon: 'question',
            title: isAlready ? 'Remove this slot?' : 'Select this slot?',
            html: isAlready
                ? 'This slot is already in your selection.'
                : 'Do you want to add this slot to the selection list?',
            showCancelButton: true,
            confirmButtonText: isAlready ? 'Remove' : 'Add',
            confirmButtonColor: isAlready ? '#ef4444' : '#4f46e5',
            cancelButtonText: 'Cancel',
        });

        return result.isConfirmed ? (isAlready ? 'remove' : 'add') : null;
    }

    async function askSelectMore() {
        const result = await Swal.fire({
            icon: 'info',
            title: 'Select more slots?',
            text: 'Click more slots on the calendar, or apply a new price now.',
            showCancelButton: true,
            confirmButtonText: 'Yes, select more',
            cancelButtonText: 'No, apply price',
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#111827',
        });

        return result.isConfirmed;
    }

    async function openApplyPriceFlow() {
        if (selectedSlots.size === 0) return;

        const { value: price } = await Swal.fire({
            title: 'Set new price (RM)',
            input: 'number',
            inputAttributes: { min: 1, step: 0.01 },
            showCancelButton: true,
            confirmButtonText: 'Update selected slots',
            confirmButtonColor: '#4f46e5',
            preConfirm: (val) => {
                const num = parseFloat(val);
                if (!num || num < 1) {
                    Swal.showValidationMessage('Please enter a valid price (min RM 1).');
                }
                return num;
            }
        });

        if (!price) return;

        const res = await fetch(`{{ route('admin.booking.slot.price.updateSelected') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': `{{ csrf_token() }}`
            },
            body: JSON.stringify({
                slot_ids: Array.from(selectedSlots),
                price: price
            })
        });

        const data = await res.json();

        await Swal.fire({
            icon: res.ok ? 'success' : 'error',
            title: res.ok ? 'Updated' : 'Failed',
            text: data.message || 'Something went wrong.',
            confirmButtonColor: '#4f46e5'
        });

        if (res.ok) {
            selectedSlots.clear();
            updateSelectionUI();
            calendar.refetchEvents();
        }
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
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
        events: '{{ route("admin.booking.slots.json", $field->fieldID) }}',

        eventDidMount: function (info) {
            const slotId = info.event.extendedProps.slotId;

            if (info.event.extendedProps.priceChanged) {
                info.el.classList.add('slot-price-changed');
            }

            if (selectedSlots.has(slotId)) {
                info.el.classList.add('slot-selected');
            }
        },

        eventClick: async function (info) {
            const status = info.event.extendedProps.status;
            const slotId = info.event.extendedProps.slotId;

            const action = await askAddOrRemove(slotId, status);
            if (!action) return;

            if (action === 'add') {
                selectedSlots.add(slotId);
                toggleEventSelected(info, true);
            } else {
                selectedSlots.delete(slotId);
                toggleEventSelected(info, false);
            }

            updateSelectionUI();

            if (action === 'add') {
                const selectMore = await askSelectMore();
                if (!selectMore) {
                    await openApplyPriceFlow();
                }
            }
        }
    });

    calendar.render();
    checkOrientation();

    window.addEventListener('resize', function () {
        calendar.setOption('headerToolbar', getHeaderToolbar());
        calendar.changeView(getInitialView());
        calendar.updateSize();
        checkOrientation();
    });

    setInterval(function () {
        calendar.refetchEvents();
    }, 30000);

    document.getElementById('clearSelection').addEventListener('click', () => {
        selectedSlots.clear();
        updateSelectionUI();
        calendar.refetchEvents();
    });

    document.getElementById('applyPrice').addEventListener('click', openApplyPriceFlow);
});
</script>
@endsection
