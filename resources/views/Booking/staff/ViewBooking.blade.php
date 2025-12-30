@extends('layout.staff')

@section('title', 'View All Bookings - PadangPro Staff')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Manage All Bookings</h1>
    <p class="mt-2 text-lime-100">Review, edit, or cancel customer and walk-in bookings.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative space-y-12">

    {{-- Filter Form --}}
    {{-- Filter Form --}}
<div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
    <form action="{{ route('staff.booking.viewAll') }}" method="GET" class="flex flex-wrap items-center gap-4">
        
        {{-- Month Filter --}}
        <div>
            <label for="month" class="text-sm font-medium text-gray-700">Filter by Month:</label>
            <select name="month" id="month" onchange="this.form.submit()" class="block w-52 rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500 sm:text-sm mt-1">
                @foreach($monthList as $value => $label)
                    <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        
        {{-- Date Filter --}}
        <div>
            <label for="search_date" class="text-sm font-medium text-gray-700">Search by Date:</label>
            <input type="date" name="search_date" id="search_date" value="{{ request('search_date') }}"
                   class="block w-52 rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500 sm:text-sm mt-1">
        </div>

        {{-- Status Filter --}}
        <div>
            <label for="status" class="text-sm font-medium text-gray-700">Filter by Status:</label>
            <select name="status" id="status" onchange="this.form.submit()" class="block w-52 rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500 sm:text-sm mt-1">
                <option value="all" {{ ($selectedStatus ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                <option value="paid" {{ ($selectedStatus ?? '') === 'paid' ? 'selected' : '' }}>Deposited</option>
                <option value="completed" {{ ($selectedStatus ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>


        {{-- Search & Clear Buttons --}}
        <div class="mt-6">
            <button type="submit" class="bg-zinc-700 hover:bg-zinc-800 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition">
                Search
            </button>
            <a href="{{ route('staff.booking.viewAll') }}" class="text-sm text-gray-600 hover:text-gray-900 ml-3">Clear Filters</a>
        </div>

    </form>
</div>


    {{-- STAFF & ADMIN BOOKINGS --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Staff & Admin Reservations (Internal)</h2>

        {{-- Desktop Table --}}
        <div class="hidden md:block">
            @if($adminBookings->isEmpty())
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
                    <i class="bi bi-calendar-x text-4xl text-gray-400"></i>
                    <p class="mt-4 text-gray-500">No staff/admin reservations found for the selected filters.</p>
                </div>
            @else
                <div class="overflow-x-auto shadow-md rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-zinc-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Title / Customer Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Reserved By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Field</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-lime-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($adminBookings as $booking)
                                <tr class="hover:bg-slate-50/50 transition-colors {{ $booking->isExpired ? 'opacity-60' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $booking->booking_Name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->user->full_name ?? 'Unknown Staff/Admin' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->field->field_Label ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->formattedDate }}, {{ $booking->formattedTime }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($booking->booking_Status === 'completed')
                                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="bi bi-check-circle-fill mr-1.5"></i> Completed
                                            </span>
                                        @elseif($booking->isExpired)
                                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="bi bi-clock-history mr-1.5"></i> Expired
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <i class="bi bi-journal-check mr-1.5"></i> Reserved
                                            </span>
                                        @endif

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        @if(!$booking->isExpired && $booking->booking_Status !== 'completed')

                                            <div class="flex justify-center items-center gap-2">

                                                {{-- Record Cash --}}
                                                <form action="{{ route('staff.payment.markCompleted', $booking->bookingID) }}" method="POST">
                                                    @csrf
                                                    <button type="button"
                                                        class="record-cash-btn inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                                                            bg-green-600 text-white hover:bg-green-700 transition shadow">
                                                        <i class="bi bi-cash-coin mr-1"></i> Record Cash
                                                    </button>
                                                </form>

                                                {{-- Edit --}}
                                                <a href="{{ route('staff.booking.edit', $booking->bookingID) }}"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                                                        bg-blue-600 text-white hover:bg-blue-700 transition shadow">
                                                    <i class="bi bi-pencil-square mr-1"></i> Edit
                                                </a>

                                                {{-- Cancel --}}
                                                <form action="{{ route('staff.booking.cancel', $booking->bookingID) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="cancel-booking-btn inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                                                            bg-red-600 text-white hover:bg-red-700 transition shadow">
                                                        <i class="bi bi-x-circle mr-1"></i> Cancel
                                                    </button>
                                                </form>

                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $adminBookings->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden space-y-4">
            @forelse($adminBookings as $booking)
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 shadow-sm {{ $booking->isExpired ? 'opacity-60' : '' }}">
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-bold text-gray-900">{{ $booking->booking_Name }}</div>
                        <div class="text-sm text-gray-500">{{ $booking->formattedDate }}</div>
                    </div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Reserved By:</strong> {{ $booking->user->full_name ?? 'Unknown Staff/Admin' }}</div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Field:</strong> {{ $booking->field->field_Label ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Time:</strong> {{ $booking->formattedTime }}</div>
                    <div class="mb-2">
                        @if($booking->booking_Status === 'completed')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="bi bi-check-circle-fill mr-1.5"></i> Completed
                            </span>
                        @elseif($booking->isExpired)
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                <i class="bi bi-clock-history mr-1.5"></i> Expired
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="bi bi-journal-check mr-1.5"></i> Reserved
                            </span>
                        @endif

                    </div>
                    @if(!$booking->isExpired && $booking->booking_Status !== 'completed')
                        <div class="mt-3 space-y-2">

                            {{-- Record Cash --}}
                            <form action="{{ route('staff.payment.markCompleted', $booking->bookingID) }}" method="POST">
                                @csrf
                                <button type="button"
                                    class="record-cash-btn w-full inline-flex justify-center items-center px-4 py-2
                                        rounded-lg text-sm font-semibold bg-green-600 text-white hover:bg-green-700 transition shadow">
                                    <i class="bi bi-cash-coin mr-2"></i> Record Cash
                                </button>
                            </form>

                            {{-- Edit --}}
                            <a href="{{ route('staff.booking.edit', $booking->bookingID) }}"
                            class="w-full inline-flex justify-center items-center px-4 py-2
                                    rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition shadow">
                                <i class="bi bi-pencil-square mr-2"></i> Edit Booking
                            </a>

                            {{-- Cancel --}}
                            <form action="{{ route('staff.booking.cancel', $booking->bookingID) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button"
                                    class="cancel-booking-btn w-full inline-flex justify-center items-center px-4 py-2
                                        rounded-lg text-sm font-semibold bg-red-600 text-white hover:bg-red-700 transition shadow">
                                    <i class="bi bi-x-circle mr-2"></i> Cancel Booking
                                </button>
                            </form>

                        </div>
                    @endif

                </div>
            @empty
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
                    <i class="bi bi-calendar-x text-4xl text-gray-400"></i>
                    <p class="mt-4 text-gray-500">No staff/admin reservations found for the selected filters.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- CUSTOMER BOOKINGS --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Registered Customer Bookings</h2>

        {{-- Desktop Table --}}
        <div class="hidden md:block">
            @if($customerBookings->isEmpty())
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
                    <i class="bi bi-person-x text-4xl text-gray-400"></i>
                    <p class="mt-4 text-gray-500">No customer bookings found for the selected filters.</p>
                </div>
            @else
                <div class="overflow-x-auto shadow-md rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-zinc-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Booked By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Field</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-lime-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customerBookings as $booking)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->full_name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->user->user_Email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->field->field_Label ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->formattedDate }}, {{ $booking->formattedTime }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($booking->display_Status === 'Awaiting Balance')
                                            <form action="{{ route('staff.payment.markCompleted', $booking->bookingID) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="button" class="inline-flex items-center px-4 py-2 rounded-lg text-xs font-semibold bg-green-600 text-white hover:bg-green-700 transition shadow-md record-cash-btn">
                                                    <i class="bi bi-cash-coin mr-1.5"></i> Record Cash
                                                </button>
                                            </form>
                                        @elseif($booking->display_Status === 'Paid (Deposit)')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                <i class="bi bi-calendar-check mr-1.5"></i> Paid (Deposit)
                                            </span>
                                        @elseif($booking->display_Status === 'Completed')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="bi bi-check-circle-fill mr-1.5"></i> Completed
                                            </span>
                                        @elseif($booking->display_Status === 'Expired')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                                <i class="bi bi-clock-history mr-1.5"></i> Expired
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                {{ $booking->display_Status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $customerBookings->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden space-y-4">
            @forelse($customerBookings as $booking)
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-bold text-gray-900">{{ $booking->user->full_name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ $booking->formattedDate }}</div>
                    </div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Email:</strong> {{ $booking->user->user_Email ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Field:</strong> {{ $booking->field->field_Label ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Time:</strong> {{ $booking->formattedTime }}</div>
                    <div class="mt-2">
                        @if($booking->display_Status === 'Awaiting Balance')
                            <form action="{{ route('staff.payment.markCompleted', $booking->bookingID) }}" method="POST" class="inline-block w-full">
                                @csrf
                                <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 rounded-lg text-sm font-semibold bg-green-600 text-white hover:bg-green-700 transition shadow-md record-cash-btn">
                                    <i class="bi bi-cash-coin mr-1.5"></i> Record Cash
                                </button>
                            </form>
                        @elseif($booking->display_Status === 'Paid (Deposit)')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                <i class="bi bi-calendar-check mr-1.5"></i> Paid (Deposit)
                            </span>
                        @elseif($booking->display_Status === 'Completed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <i class="bi bi-check-circle-fill mr-1.5"></i> Completed
                            </span>
                        @elseif($booking->display_Status === 'Expired')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                <i class="bi bi-clock-history mr-1.5"></i> Expired
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                {{ $booking->display_Status }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
                    <i class="bi bi-person-x text-4xl text-gray-400"></i>
                    <p class="mt-4 text-gray-500">No customer bookings found for the selected filters.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonColor: '#166534',
        confirmButtonText: 'Okay'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        title: 'Error!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Okay'
    });
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cancel Booking
    const cancelButtons = document.querySelectorAll('.cancel-booking-btn');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "This booking will be permanently cancelled!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'Keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Record Cash Payment
    const cashButtons = document.querySelectorAll('.record-cash-btn');
    cashButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Confirm Cash Payment',
                text: "Are you sure you have received the cash payment for this booking's balance?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, payment received!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
