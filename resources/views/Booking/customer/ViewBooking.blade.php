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

    {{-- Filter --}}
    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-6">
        <form action="{{ route('booking.view') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <label for="month" class="text-sm font-medium text-gray-700">Filter:</label>
            <select name="month" id="month" onchange="this.form.submit()" class="block w-full md:w-52 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @foreach($monthList as $value => $label)
                    <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            
            @php $currentMonth = \Carbon\Carbon::now('Asia/Kuala_Lumpur')->format('Y-m'); @endphp
            
            @if($selectedMonth != $currentMonth)
                <a href="{{ route('booking.view', ['month' => $currentMonth]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Current Month
                </a>
            @endif
            
            @if(request()->has('month'))
                 <a href="{{ route('booking.view') }}" class="text-sm text-gray-600 hover:text-gray-900">Clear</a>
            @endif
        </form>
    </div>

    @if($pendingBookings->isEmpty() && $completedBookings->isEmpty())
        <div class="text-center py-12">
            <i class="bi bi-calendar-x text-6xl text-indigo-200"></i>
            <h3 class="mt-4 text-2xl font-bold text-gray-700">No Bookings Found</h3>
            <p class="mt-2 text-gray-500">You have no bookings for this month.</p>
            
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('booking.page', 'F01') }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-lg shadow-md transition-all">
                    <i class="bi bi-calendar-plus-fill mr-2"></i> Book Standard
                </a>
                <a href="{{ route('booking.mini') }}" class="inline-flex items-center bg-white hover:bg-slate-50 text-slate-700 font-bold py-3 px-5 rounded-lg shadow-md border border-slate-300 transition-all">
                    <i class="bi bi-calendar-plus mr-2"></i> Book Mini
                </a>
            </div>
        </div>
    @else

        {{-- =============================================== --}}
        {{-- SECTION 1: PENDING BOOKINGS --}}
        {{-- =============================================== --}}
        @if($pendingBookings->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Pending & Awaiting Balance</h2>
            
            {{-- DESKTOP TABLE --}}
            <div class="hidden md:block overflow-x-auto shadow-md rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase">Field</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase">Price</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingBookings as $booking)
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $booking->field->field_Label ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $booking->bookingID }}</div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->formattedDate }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->formattedTime }}</div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-800">{{ $booking->formattedPrice }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    @include('Booking.customer.partials.status_badge', ['booking' => $booking])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARD VIEW --}}
            <div class="md:hidden space-y-4">
                @foreach($pendingBookings as $booking)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $booking->field->field_Label ?? 'N/A' }}</h3>
                                <p class="text-xs text-gray-500">ID: {{ $booking->bookingID }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-indigo-600">{{ $booking->formattedPrice }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
                            <div>
                                <p class="text-sm text-gray-700">{{ $booking->formattedDate }}</p>
                                <p class="text-xs text-gray-500">{{ $booking->formattedTime }}</p>
                            </div>
                            <div>
                                @include('Booking.customer.partials.status_badge', ['booking' => $booking])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $pendingBookings->links() }}
            </div>
        </div>
        @endif

        {{-- =============================================== --}}
        {{-- SECTION 2: COMPLETED BOOKINGS --}}
        {{-- =============================================== --}}
        @if($completedBookings->isNotEmpty())
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Completed Bookings</h2>
            
            {{-- DESKTOP TABLE --}}
            <div class="hidden md:block overflow-x-auto shadow-md rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase">Field</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase">Price</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($completedBookings as $booking)
                            <tr class="hover:bg-slate-50/50 transition-all opacity-70">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $booking->field->field_Label ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $booking->bookingID }}</div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->formattedDate }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->formattedTime }}</div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-800">{{ $booking->formattedPrice }}</td>
                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle-fill mr-1.5"></i> Completed
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    @include('Booking.customer.partials.rating_button', ['booking' => $booking])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARD VIEW --}}
            <div class="md:hidden space-y-4">
                @foreach($completedBookings as $booking)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 opacity-80">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $booking->field->field_Label ?? 'N/A' }}</h3>
                                <p class="text-xs text-gray-500">ID: {{ $booking->bookingID }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-indigo-600">{{ $booking->formattedPrice }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
                            <div>
                                <p class="text-sm text-gray-700">{{ $booking->formattedDate }}</p>
                                <p class="text-xs text-gray-500">{{ $booking->formattedTime }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-green-100 text-green-800">
                                    Completed
                                </span>
                                @include('Booking.customer.partials.rating_button', ['booking' => $booking])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $completedBookings->links() }}
            </div>
        </div>
        @endif
        
    @endif
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for success messages --}}
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

@if(session('error'))
    <script>
        Swal.fire({
            title: 'Error',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#d33', // Red
            confirmButtonText: 'Okay'
        });
    </script>
@endif
@endpush
