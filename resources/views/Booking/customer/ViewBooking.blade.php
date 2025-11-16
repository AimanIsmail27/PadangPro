@extends('layout.customer')

@section('title', 'My Booking History')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">My Booking History</h1>
    <p class="mt-2 text-indigo-100">A record of all your bookings.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-6">
        <form action="{{ route('booking.view') }}" method="GET" class="flex flex-wrap items-center space-x-4">
            <label for="month" class="text-sm font-medium text-gray-700">Filter by Month:</label>
            <select name="month" id="month" onchange="this.form.submit()" class="block w-52 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @foreach($monthList as $value => $label)
                    <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            
            @php
                $currentMonth = \Carbon\Carbon::now('Asia/Kuala_Lumpur')->format('Y-m');
            @endphp
            
            @if($selectedMonth != $currentMonth)
                <a href="{{ route('booking.view', ['month' => $currentMonth]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Go to Current Month
                </a>
            @endif
            
            @if(request()->has('month'))
                 <a href="{{ route('booking.view') }}" class="text-sm text-gray-600 hover:text-gray-900">Clear Filter</a>
            @endif
        </form>
    </div>

    @if($pendingBookings->isEmpty() && $completedBookings->isEmpty())
        <div class="text-center py-12">
            <i class="bi bi-calendar-x text-6xl text-indigo-200"></i>
            <h3 class="mt-4 text-2xl font-bold text-gray-700">No Bookings Found</h3>
            <p class="mt-2 text-gray-500">You have no bookings for the selected month.</p>
            
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('booking.page', 'F01') }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-lg shadow-md transition-all transform hover:scale-105">
                    <i class="bi bi-calendar-plus-fill mr-2"></i>
                    Book Standard Field
                </a>
                <a href="{{ route('booking.mini') }}" class="inline-flex items-center bg-white hover:bg-slate-50 text-slate-700 font-bold py-3 px-5 rounded-lg shadow-md border border-slate-300 transition-all transform hover:scale-105">
                    <i class="bi bi-calendar-plus mr-2"></i>
                    Book Mini Field
                </a>
            </div>
        </div>
    @else

        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Pending & Awaiting Balance</h2>
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Field</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Date & Time</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pendingBookings as $booking)
                            {{-- 'isExpired' class removed --}}
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $booking->field->field_Label ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $booking->bookingID }}</div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->formattedDate }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->formattedTime }}</div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $booking->formattedPrice }}
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    @if($booking->display_Status === 'Awaiting Balance')
                                        <a href="{{ route('payment.balance.create', $booking->bookingID) }}" 
                                           class="inline-flex items-center px-4 py-2 rounded-lg text-xs font-semibold bg-green-600 text-white hover:bg-green-700 transition shadow-md">
                                            <i class="bi bi-credit-card-fill mr-1.5"></i> Pay Balance
                                        </a>
                                    @elseif($booking->display_Status === 'Paid (Deposit)')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            <i class="bi bi-calendar-check mr-1.5"></i> Paid (Deposit)
                                        </span>
                                    @elseif($booking->display_Status === 'Expired')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                            <i class="bi bi-clock-history mr-1.5"></i> Expired
                                        </span>
                                    @else
                                        {{-- This will catch 'Pending' --}}
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            {{ $booking->display_Status }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-gray-500 italic">No pending bookings found for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $pendingBookings->links() }}
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Completed Bookings</h2>
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Field</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Date & Time</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($completedBookings as $booking)
                             {{-- 'isExpired' class removed --}}
                            <tr class="hover:bg-slate-50/50 transition-all opacity-70">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $booking->field->field_Label ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $booking->bookingID }}</div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->formattedDate }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->formattedTime }}</div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $booking->formattedPrice }}
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    {{-- This table only shows 'Completed' bookings, so we can simplify --}}
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle-fill mr-1.5"></i> Completed
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-gray-500 italic">No completed bookings found for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $completedBookings->links() }}
            </div>
        </div>
        
    @endif
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for success message (from initial deposit) --}}
@if(session('payment_success'))
    <script>
        Swal.fire({
            title: 'Booking Confirmed!',
            text: "{{ session('payment_success') }}",
            icon: 'success',
            confirmButtonColor: '#4f46e5', // Indigo-600
            confirmButtonText: 'Okay'
        });
    </script>
@endif

{{-- SweetAlert2 for success message (from balance payment) --}}
@if(session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#4f46e5', // Indigo-600
            confirmButtonText: 'Okay'
        });
    </script>
@endif

{{-- SweetAlert2 for ALL errors --}}
@if(session('error'))
    <script>
        Swal.fire({
            title: 'Payment Failed',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#d33', // Red
            confirmButtonText: 'Okay'
        });
    </script>
@endif
@endpush