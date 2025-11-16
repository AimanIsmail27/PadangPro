@extends('layout.staff')

@section('title', 'Staff Dashboard - PadangPro')

@section('content')
    
<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-28 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Staff Dashboard</h1>
    <p class="mt-2 text-lime-100">Here's your operational overview for today.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative space-y-12">

    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Today's Key Stats</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <div data-tippy-content="Total number of rentals waiting for return approval."
                 class="bg-white rounded-xl shadow-lg p-6 relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 border-t-4 border-yellow-500">
                <i class="bi bi-exclamation-triangle-fill absolute -right-4 -bottom-4 text-8xl text-yellow-500 opacity-10"></i>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pending Approvals</p>
                <p class="text-5xl font-bold text-gray-800 mt-2">{{ $kpi_pendingApprovals }}</p>
            </div>

            <div data-tippy-content="All paid bookings scheduled for today or in the future."
                 class="bg-white rounded-xl shadow-lg p-6 relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 border-t-4 border-green-500">
                <i class="bi bi-calendar-check-fill absolute -right-4 -bottom-4 text-8xl text-green-500 opacity-10"></i>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Active Bookings</g>
                <p class="text-5xl font-bold text-gray-800 mt-2">{{ $kpi_activeBookings }}</p>
            </div>

            <div data-tippy-content="Total items currently rented out (today is between start and end date)."
                 class="bg-white rounded-xl shadow-lg p-6 relative overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1 border-t-4 border-blue-500">
                <i class="bi bi-person-badge-fill absolute -right-4 -bottom-4 text-8xl text-blue-500 opacity-10"></i>
                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Current Rentals</g>
                <p class="text-5xl font-bold text-gray-800 mt-2">{{ $kpi_currentRentals }}</p>
            </div>

        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Pending Tasks (Rental Returns)</h2>
                <div class="divide-y divide-gray-200">
                    @forelse($pendingTasks as $task)
                    <div class="flex items-center justify-between py-4 group">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 transition-all group-hover:scale-110">
                                <i class="bi bi-arrow-return-left text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Return approval needed for {{ $task->rentalID }}</p>
                                <p class="text-sm text-gray-500">Customer: {{ $task->customer->customer_FullName ?? 'N/A' }} ({{ $task->quantity }} {{ $task->item->item_Name ?? 'item' }})</p>
                            </div>
                        </div>
                        <a href="{{ route('staff.rentals.returnApproval') }}" class="text-sm font-medium text-lime-600 hover:text-lime-800 opacity-0 group-hover:opacity-100 transition-all">Review &rarr;</a>
                    </div>
                    @empty
                    <div class="text-center py-10">
                        <i class="bi bi-check2-circle text-4xl text-green-500"></i>
                        <p class="mt-2 text-gray-500">No pending tasks. All caught up!</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Upcoming Paid Bookings (Next 4)</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Field</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($upcomingBookings as $booking)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->full_name ?? $booking->booking_Name }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->user->user_Email ?? $booking->booking_Email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $booking->field->field_Label }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($booking->slot->slot_Date)->format('D, M jS') }}
                                        at {{ \Carbon\Carbon::parse($booking->slot->slot_Time)->format('h:i A') }}
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

        <div class="lg:col-span-1 space-y-8">
            
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Bookings (Last 6 Months)</h2>
                <div class="h-[250px]">
                    <canvas id="bookingChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Links</h2>
                <div class="space-y-4">
                    <a href="{{ route('staff.booking.manage') }}" class="group flex items-center justify-between p-4 bg-slate-50 hover:bg-lime-50 border border-slate-200 rounded-lg transition-all">
                        <div>
                            <p class="font-semibold text-gray-800">New Walk-in Booking</p>
                            <p class="text-sm text-gray-500">Book a slot for a customer</p>
                        </div>
                        <i class="bi bi-arrow-right-short text-xl text-gray-400 group-hover:text-lime-600 transition-all"></i>
                    </a>
                    <a href="{{ route('staff.rental.main') }}" class="group flex items-center justify-between p-4 bg-slate-50 hover:bg-lime-50 border border-slate-200 rounded-lg transition-all">
                        <div>
                            <p class="font-semibold text-gray-800">Manage Rental Items</p>
                            <p class="text-sm text-gray-500">Add or edit item stock</p>
                        </div>
                        <i class="bi bi-arrow-right-short text-xl text-gray-400 group-hover:text-lime-600 transition-all"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Initialize Tippy.js
        tippy('[data-tippy-content]', {
            animation: 'scale-subtle',
            theme: 'translucent',
        });

        // 2. Initialize Chart.js
        const ctx = document.getElementById('bookingChart');
        if (ctx) {
            const chartLabels = {!! $chartLabels !!};
            const chartData = {!! $chartData !!};

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(101, 163, 13, 0.7)'); // Lime-600
            gradient.addColorStop(1, 'rgba(101, 163, 13, 0.1)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Bookings',
                        data: chartData,
                        backgroundColor: gradient,
                        borderColor: 'rgba(101, 163, 13, 1)', // Lime-600
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