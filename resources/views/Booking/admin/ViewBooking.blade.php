@extends('layout.admin')

@section('title', 'View All Bookings - PadangPro Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold">Manage All Bookings</h1>
</div>

<div class="w-11/12 mx-auto -mt-16 relative space-y-12">

    {{-- Filter Section --}}
    <div class="bg-gray-50 p-4 rounded-lg border">
        <form action="{{ route('admin.booking.viewAll') }}" method="GET" class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-3 md:space-y-0">
            <div class="flex items-center space-x-2">
                <label for="month" class="text-sm font-medium text-gray-700">Filter by Month:</label>
                <select name="month" id="month" onchange="this.form.submit()" class="block w-52 rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm">
                    @foreach($monthList as $value => $label)
                        <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <a href="{{ route('admin.booking.viewAll') }}" class="text-sm text-gray-600 hover:text-gray-900">Clear Filter</a>
        </form>
    </div>

    {{-- ================= ADMIN BOOKINGS ================= --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Admin Reservations (Internal & Walk-in)</h2>

        {{-- DESKTOP TABLE --}}
        <div class="hidden md:block">
            @if($adminBookings->isEmpty())
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
                    <i class="bi bi-calendar-x text-4xl text-gray-400"></i>
                    <p class="mt-4 text-gray-500">No admin reservations found for the selected month.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title / Customer Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($adminBookings as $booking)
                                <tr class="hover:bg-gray-50 transition-colors {{ $booking->isExpired ? 'opacity-60' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $booking->booking_Name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->user->full_name ?? 'Unknown Admin' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->field->field_Label ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->formattedDate }}, {{ $booking->formattedTime }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($booking->isExpired)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800"><i class="bi bi-clock-history mr-1.5"></i> Expired</span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800"><i class="bi bi-journal-check mr-1.5"></i> Reserved</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-4">
                                        @if(!$booking->isExpired)
                                            <a href="{{ route('admin.booking.edit', $booking->bookingID) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('admin.booking.cancel', $booking->bookingID) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="text-red-600 hover:text-red-900 cancel-booking-btn">Cancel</button>
                                            </form>
                                        @else
                                            <span>—</span>
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

        {{-- MOBILE CARDS --}}
        <div class="md:hidden space-y-4">
            @forelse($adminBookings as $booking)
                <div class="bg-white shadow-md rounded-lg border p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-800 text-sm">{{ $booking->booking_Name }}</h3>
                        <span class="text-xs {{ $booking->isExpired ? 'text-gray-500' : 'text-blue-600' }}">
                            @if($booking->isExpired)
                                <i class="bi bi-clock-history mr-1"></i> Expired
                            @else
                                <i class="bi bi-journal-check mr-1"></i> Reserved
                            @endif
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-1"><strong>Reserved By:</strong> {{ $booking->user->full_name ?? 'Unknown Admin' }}</p>
                    <p class="text-xs text-gray-500 mb-1"><strong>Field:</strong> {{ $booking->field->field_Label ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500 mb-2"><strong>Date & Time:</strong> {{ $booking->formattedDate }}, {{ $booking->formattedTime }}</p>
                    @if(!$booking->isExpired)
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('admin.booking.edit', $booking->bookingID) }}" class="px-3 py-1 text-center text-white bg-indigo-600 rounded-md text-sm hover:bg-indigo-700">Edit</a>
                            <form action="{{ route('admin.booking.cancel', $booking->bookingID) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="w-full text-sm px-3 py-1 text-center text-red-600 border border-red-600 rounded-md hover:bg-red-50 cancel-booking-btn">Cancel</button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
                    <i class="bi bi-calendar-x text-4xl text-gray-400"></i>
                    <p class="mt-4 text-gray-500">No admin reservations found for the selected month.</p>
                </div>
            @endforelse
            <div class="mt-4">
                {{ $adminBookings->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- ================= CUSTOMER BOOKINGS ================= --}}
    <div>
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Registered Customer Bookings</h2>

        {{-- Desktop Table --}}
        <div class="hidden md:block">
            @if($customerBookings->isEmpty())
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
                    <i class="bi bi-person-x text-4xl text-gray-400"></i>
                    <p class="mt-4 text-gray-500">No customer bookings found for the selected month.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customerBookings as $booking)
                                <tr class="hover:bg-gray-50 transition-colors {{ $booking->isExpired ? 'opacity-60' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->full_name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->user->user_Email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->field->field_Label ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->formattedDate }}, {{ $booking->formattedTime }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($booking->isExpired)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800"><i class="bi bi-clock-history mr-1.5"></i> Expired</span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"><i class="bi bi-check-circle mr-1.5"></i> Paid</span>
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

        {{-- Mobile Cards --}}
        <div class="md:hidden space-y-4">
            @forelse($customerBookings as $booking)
                <div class="bg-white shadow-md rounded-lg border p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-gray-800 text-sm">{{ $booking->user->full_name ?? 'N/A' }}</h3>
                        <span class="text-xs {{ $booking->isExpired ? 'text-gray-500' : 'text-green-600' }}">
                            @if($booking->isExpired)
                                <i class="bi bi-clock-history mr-1"></i> Expired
                            @else
                                <i class="bi bi-check-circle mr-1"></i> Paid
                            @endif
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mb-1"><strong>Email:</strong> {{ $booking->user->user_Email ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500 mb-1"><strong>Field:</strong> {{ $booking->field->field_Label ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500 mb-2"><strong>Date & Time:</strong> {{ $booking->formattedDate }}, {{ $booking->formattedTime }}</p>
                </div>
            @empty
                <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
                    <i class="bi bi-person-x text-4xl text-gray-400"></i>
                    <p class="mt-4 text-gray-500">No customer bookings found for the selected month.</p>
                </div>
            @endforelse
            <div class="mt-4">
                {{ $customerBookings->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 for success/confirmation messages --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'Okay'
    });
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
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
});
</script>
@endpush

@endsection
