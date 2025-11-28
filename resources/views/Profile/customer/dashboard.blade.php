@extends('layout.customer')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
{{-- Add Alpine.js for tooltips (if not already included globally) --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    /* Basic tooltip styles for Alpine.js */
    [x-cloak] { display: none !important; }
    .tooltip {
        position: absolute;
        bottom: calc(100% + 10px); /* Position above the element */
        left: 50%;
        transform: translateX(-50%);
        background-color: #333;
        color: #fff;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.875rem;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        z-index: 50;
    }
    .tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }
    .group:hover .tooltip {
        opacity: 1;
        visibility: visible;
    }
</style>
@endpush

@section('content')

{{-- 1. ORIGINAL HEADER --}}
<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Dashboard</h1>
    <p class="mt-2 text-indigo-100">Welcome back, <strong>{{ $fullName }}</strong>! Here's your activity overview.</p>
</div>

{{-- 2. MAIN CONTENT CONTAINER (Floating White Box) --}}
<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative space-y-8">

    {{-- ROW 1: KPI STATS with Hover Tooltips --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- KPI 1: Total Games --}}
        <div class="group relative p-5 bg-indigo-50 rounded-xl border border-indigo-100 flex items-center justify-between">
            <div>
                <span class="block text-2xl font-black text-indigo-600">{{ $kpi_totalBookings }}</span>
                <span class="text-xs font-bold text-gray-500 uppercase">Matches Played</span>
            </div>
            <div class="bg-white p-3 rounded-full text-indigo-500 shadow-sm">
                <i class="bi bi-trophy-fill text-xl"></i>
            </div>
            <div class="tooltip" x-data="{}" x-cloak>
                Total number of games you've successfully booked.
            </div>
        </div>
        
        {{-- KPI 2: Active Rentals --}}
        <div class="group relative p-5 bg-purple-50 rounded-xl border border-purple-100 flex items-center justify-between">
            <div>
                <span class="block text-2xl font-black text-purple-600">{{ $kpi_activeRentals }}</span>
                <span class="text-xs font-bold text-gray-500 uppercase">Active Rentals</span>
            </div>
            <div class="bg-white p-3 rounded-full text-purple-500 shadow-sm">
                <i class="bi bi-bag-check-fill text-xl"></i>
            </div>
            <div class="tooltip" x-data="{}" x-cloak>
                Number of rental items currently active or pending return.
            </div>
        </div>
        
        {{-- KPI 3: Pending Matches --}}
        <div class="group relative p-5 bg-green-50 rounded-xl border border-green-100 flex items-center justify-between">
            <div>
                <span class="block text-2xl font-black text-green-600">{{ $kpi_newApplications }}</span>
                <span class="text-xs font-bold text-gray-500 uppercase">Pending Matches</span>
            </div>
            <div class="bg-white p-3 rounded-full text-green-500 shadow-sm">
                <i class="bi bi-people-fill text-xl"></i>
            </div>
            <div class="tooltip" x-data="{}" x-cloak>
                Matches you've applied to or created, awaiting confirmation.
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- ROW 2 (LEFT): NEXT MATCH & CHART --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- NEXT MATCH CARD --}}
            @if($nextMatch)
                <div class="relative bg-slate-900 text-white rounded-2xl overflow-hidden shadow-lg group">
                    {{-- Background Image with Overlay --}}
                    <img src="{{ asset('images/padangcoverpage.jpg') }}" 
                        class="absolute inset-0 w-full h-full object-cover opacity-30 transition-transform duration-700 group-hover:scale-105"
                        alt="Next Match Background">
                    
                    <div class="relative z-10 p-6 md:p-8">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="bg-green-500 text-white px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider mb-2 inline-block">Next Match</span>
                                <h2 class="text-2xl font-bold mb-1">{{ $nextMatch->field->field_Label }}</h2>
                                <p class="text-slate-300 text-sm"><i class="bi bi-geo-alt mr-1"></i> PadangPro Main Stadium</p>
                            </div>
                            
                            <div class="bg-white/10 backdrop-blur-md p-3 rounded-xl text-center min-w-[80px] border border-white/20">
                                <span class="block text-2xl font-bold">{{ \Carbon\Carbon::parse($nextMatch->slot->slot_Date)->format('d') }}</span>
                                <span class="block text-xs uppercase tracking-wide">{{ \Carbon\Carbon::parse($nextMatch->slot->slot_Date)->format('M') }}</span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-white/10 flex items-center justify-between">
                            <div class="text-lg font-medium">
                                <i class="bi bi-clock mr-2"></i> {{ \Carbon\Carbon::parse($nextMatch->slot->slot_Time)->format('h:i A') }}
                            </div>
                            <a href="{{ route('booking.view') }}" class="bg-white text-slate-900 px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-50 transition">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-2xl p-8 text-center border-2 border-dashed border-gray-200">
                    <i class="bi bi-calendar-x text-4xl text-gray-300 block mb-2"></i>
                    <h3 class="text-lg font-bold text-gray-600">No Upcoming Matches</h3>
                    <p class="text-gray-400 text-sm mb-4">You are free! Why not book a game?</p>
                    <a href="{{ route('booking.page', 'F01') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">Book Now</a>
                </div>
            @endif

            {{-- CHART SECTION (Bar Chart) --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Booking Activity</h3>
                <div class="h-64">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>

        </div>

        {{-- ROW 2 (RIGHT): QUICK ACTIONS & RECENT ACTIVITY --}}
        <div class="lg:col-span-1 space-y-6">
            
            <h3 class="text-lg font-bold text-gray-800">Quick Actions</h3>
            
            {{-- Action 1: Rent --}}
            <a href="{{ route('customer.rental.main') }}" class="group relative h-28 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all block">
                <img src="{{ asset('images/matchball.jpg') }}" 
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-80"
                     alt="Rent Gear">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-900/90 to-transparent"></div>
                <div class="absolute inset-0 p-5 flex items-center justify-between z-10">
                    <div>
                        <span class="block text-white font-bold text-lg">Rent Gear</span>
                        <span class="text-indigo-200 text-xs">Jerseys & Equipment</span>
                    </div>
                    <i class="bi bi-arrow-right-circle-fill text-white text-2xl opacity-80 group-hover:opacity-100 transition-opacity"></i>
                </div>
            </a>

            {{-- Action 2: Find Match --}}
            <a href="{{ route('matchmaking.other') }}" class="group relative h-28 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all block">
                <img src="https://images.unsplash.com/photo-1529900748604-07564a03e7a6?q=80&w=400&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-900/90 to-transparent"></div>
                <div class="absolute inset-0 p-5 flex items-center justify-between z-10">
                    <div>
                        <span class="block text-white font-bold text-lg">Find Match</span>
                        <span class="text-purple-200 text-xs">Join other teams</span>
                    </div>
                    <i class="bi bi-arrow-right-circle-fill text-white text-2xl opacity-80 group-hover:opacity-100 transition-opacity"></i>
                </div>
            </a>

            {{-- Recent Activity List --}}
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                <h4 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-3">Recent History</h4>
                <div class="space-y-3">
                    @forelse($upcomingBookingsTable as $booking)
                        <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-slate-100 shadow-sm">
                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0">
                                <i class="bi bi-check-lg text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Booked {{ $booking->field->field_Label }}</p>
                                <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($booking->booking_CreatedAt)->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-xs text-gray-400 py-2">No recent bookings.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('bookingChart');
    
    // Gradient Fill for Bar Chart
    const gradientBar = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradientBar.addColorStop(0, 'rgba(79, 70, 229, 0.8)'); // Indigo-600 strong
    gradientBar.addColorStop(1, 'rgba(79, 70, 229, 0.4)'); // Indigo-600 light

    new Chart(ctx, {
        type: 'bar', // Changed to 'bar'
        data: {
            labels: {!! $chartLabels !!},
            datasets: [{
                label: 'Bookings',
                data: {!! $chartData !!},
                backgroundColor: gradientBar, // Use gradient for bars
                borderColor: '#4f46e5',
                borderWidth: 1,
                borderRadius: 5, // Rounded corners for bars
                hoverBackgroundColor: '#6366f1' // Lighter indigo on hover
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { stepSize: 1, color: '#94a3b8' },
                    grid: { color: '#f1f5f9' },
                    border: { display: false }
                },
                x: { 
                    grid: { display: false },
                    ticks: { color: '#94a3b8' },
                    border: { display: false }
                }
            }
        }
    });
</script>
{{-- ========================= --}}
{{-- SWEETALERT WELCOME POPUP --}}
{{-- ========================= --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
Swal.fire({
    title: "Welcome!",
    text: "{{ session('success') }}",
    icon: "success",
    confirmButtonColor: "#4f46e5"
});
</script>
@endif
@endpush