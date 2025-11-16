@extends('layout.customer')

@section('title', 'Customer Dashboard')

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-28 px-10 rounded-lg shadow-2xl">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold">Welcome back, {{ $fullName ?? 'Customer' }}!</h1>
            <p class="mt-2 text-indigo-100">Here’s a quick overview of your activities.</p>
        </div>
        <div class="flex-shrink-0 flex gap-4">
            <a href="{{ route('booking.page', 'F01') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold py-3 px-5 rounded-lg shadow-md transition-all backdrop-blur-sm">
                Book Standard Field
            </a>
            <a href="{{ route('booking.mini') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold py-3 px-5 rounded-lg shadow-md transition-all backdrop-blur-sm">
                Book Mini Field
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <a href="{{ route('booking.view') }}" 
               class="group relative flex items-center space-x-4 p-4 rounded-lg transition-all bg-slate-50 hover:bg-white hover:shadow-md border border-slate-200"
               data-tippy-content="This is the total number of bookings you have successfully paid for.">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="bi bi-calendar-check-fill text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Bookings</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $kpi_totalBookings }}</p>
                </div>
            </a>
            
            <a href="{{ route('customer.rental.history') }}" 
               class="group relative flex items-center space-x-4 p-4 rounded-lg transition-all bg-slate-50 hover:bg-white hover:shadow-md border border-slate-200"
               data-tippy-content="The number of your rentals that are currently active.">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="bi bi-tags-fill text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Active Rentals</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $kpi_activeRentals }}</p>
                </div>
            </a>
            
            <a href="{{ route('matchmaking.personal') }}" 
               class="group relative flex items-center space-x-4 p-4 rounded-lg transition-all bg-slate-50 hover:bg-white hover:shadow-md border border-slate-200"
               data-tippy-content="The number of new applications on your matchmaking ads that you haven't reviewed yet.">
                @if($kpi_newApplications > 0)
                    <div class="absolute top-2 right-2 h-5 w-5 bg-red-600 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                        {{ $kpi_newApplications }}
                    </div>
                @endif
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="bi bi-megaphone-fill text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">My Ads</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $kpi_newApplications }}</p>
                </div>
            </a>
        </div>
    </div>

    <hr class="my-8">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- =============================================== --}}
        {{-- MAIN CONTENT (LEFT - 2/3 width) --}}
        {{-- =============================================== --}}
        <div class="lg:col-span-2 space-y-8">

            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Next Booking</h2>
                @if($nextBooking)
                    <div class="bg-indigo-50 border-2 border-indigo-200 rounded-xl p-6 flex flex-wrap items-center justify-between gap-4 shadow-lg">
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                <i class="bi bi-calendar-event-fill text-indigo-600 text-5xl"></i>
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm text-indigo-800 font-semibold uppercase">
                                    {{ $nextBooking->field->field_Label }}
                                    @if(str_contains($nextBooking->field->field_Size, 'STANDARD'))
                                        (Standard Field)
                                    @elseif(str_contains($nextBooking->field->field_Size, 'MINI SIZED'))
                                        (Mini Pitch)
                                    @endif
                                </p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($nextBooking->slot->slot_Date)->format('D, F jS') }} at {{ \Carbon\Carbon::parse($nextBooking->slot->slot_Time)->format('h:i A') }}</h3>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('booking.view') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-5 rounded-lg shadow-md transition-all">
                                View Details
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed">
                        <i class="bi bi-calendar-x text-6xl text-indigo-200"></i>
                        <h3 class="mt-4 text-xl font-bold text-gray-700">No Upcoming Bookings</h3>
                        <p class="mt-2 text-gray-500">You don't have any paid games scheduled. Book one now!</p>
                    </div>
                @endif
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Upcoming Paid Bookings (Next 3)</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Field</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($upcomingBookingsTable as $booking)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $booking->field->field_Label }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->slot->slot_Date)->format('D, M jS') }} at {{ \Carbon\Carbon::parse($booking->slot->slot_Time)->format('h:i A') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No upcoming paid bookings.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- =============================================== --}}
        {{-- SIDEBAR (RIGHT - 1/3 width) --}}
        {{-- =============================================== --}}
        <div class="lg:col-span-1 space-y-8">
            
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Your Bookings (Last 6 Months)</h2>
                <div class="h-[250px]">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Links</h2>
                <div class="space-y-4">
                    <a href="{{ route('matchmaking.other') }}" class="group flex items-center justify-between p-4 bg-slate-50 hover:bg-indigo-50 border border-slate-200 rounded-lg transition-all">
                        <div>
                            <p class="font-semibold text-gray-800">Find a Match</p>
                            <p class="text-sm text-gray-500">Browse ads from other players</p>
                        </div>
                        <i class="bi bi-arrow-right-short text-xl text-gray-400 group-hover:text-indigo-600 transition-all"></i>
                    </a>
                    <a href="{{ route('customer.rating.main') }}" class="group flex items-center justify-between p-4 bg-slate-50 hover:bg-indigo-50 border border-slate-200 rounded-lg transition-all">
                        <div>
                            <p class="font-semibold text-gray-800">Write a Review</p>
                            <p class="text-sm text-gray-500">Share your experience</p>
                        </div>
                        <i class="bi bi-arrow-right-short text-xl text-gray-400 group-hover:text-indigo-600 transition-all"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- =============================================== --}}
{{-- NEW: Tippy.js for Tooltips --}}
{{-- =============================================== --}}
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 2. Initialize Tippy.js
        tippy('[data-tippy-content]', {
            animation: 'scale-subtle',
            theme: 'translucent', // A nice dark theme
        });

        // 3. Initialize Chart.js
        const ctx = document.getElementById('bookingChart');
        if (ctx) {
            const chartLabels = {!! $chartLabels !!};
            const chartData = {!! $chartData !!};

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.7)'); // Indigo-600
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.1)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Bookings',
                        data: chartData,
                        backgroundColor: gradient,
                        borderColor: 'rgba(79, 70, 229, 1)', // Indigo-600
                        borderWidth: 2,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: { stepSize: 1 } 
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });
</script>
@endpush