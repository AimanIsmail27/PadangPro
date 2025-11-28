@extends('layout.admin')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    /* Animated gradient background */
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .animated-gradient {
        background: linear-gradient(-45deg, #f59e0b, #fbbf24, #fcd34d, #f59e0b);
        background-size: 400% 400%;
        animation: gradient-shift 8s ease infinite;
    }

    /* KPI Card hover effects */
    .kpi-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    
    .kpi-card:hover::before {
        left: 100%;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    /* Icon pulse animation */
    @keyframes pulse-icon {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .icon-pulse {
        animation: pulse-icon 2s ease-in-out infinite;
    }

    /* Chart container with subtle background */
    .chart-container {
        background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
    }

    /* Pending task animation */
    @keyframes slide-in {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .pending-item {
        animation: slide-in 0.5s ease-out;
    }

    /* Table row hover effect */
    .table-row-hover {
        transition: all 0.2s ease;
    }
    
    .table-row-hover:hover {
        background: linear-gradient(90deg, #fef3c7 0%, #ffffff 100%);
        transform: scale(1.01);
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .kpi-card {
            padding: 1rem;
        }
        .kpi-card h3 {
            font-size: 0.75rem;
        }
        .kpi-card .text-3xl {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6 md:space-y-10">

    <!-- Enhanced Welcome Banner with Animation -->
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white p-6 md:p-10 rounded-2xl shadow-lg">
        <h1 class="text-2xl md:text-3xl font-bold">Welcome back, {{ $fullName }} 👋</h1>
        <p class="mt-2 text-sm md:text-base text-amber-100">Here's what's happening with your system this month.</p>
    </div>

    <!-- Enhanced KPI Cards with Icons and Animations -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="kpi-card bg-gradient-to-br from-green-50 to-white p-5 md:p-6 rounded-2xl shadow-lg border border-green-100">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-green-600 font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Total Revenue</h3>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1">RM {{ number_format($kpi_totalRevenue, 2) }}</h3>
                    <p class="text-xs text-green-600 font-medium">This month</p>
                </div>
                <div class="p-3 md:p-4 bg-green-500 rounded-xl text-white shadow-lg icon-pulse">
                    <i data-lucide="trending-up" class="w-6 h-6 md:w-7 md:h-7"></i>
                </div>
            </div>
        </div>

        <div class="kpi-card bg-gradient-to-br from-blue-50 to-white p-5 md:p-6 rounded-2xl shadow-lg border border-blue-100">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-blue-600 font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Total Bookings</h3>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1">{{ $kpi_totalBookings }}</h3>
                    <p class="text-xs text-blue-600 font-medium">This month</p>
                </div>
                <div class="p-3 md:p-4 bg-blue-500 rounded-xl text-white shadow-lg icon-pulse">
                    <i data-lucide="calendar-check" class="w-6 h-6 md:w-7 md:h-7"></i>
                </div>
            </div>
        </div>

        <div class="kpi-card bg-gradient-to-br from-purple-50 to-white p-5 md:p-6 rounded-2xl shadow-lg border border-purple-100">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-purple-600 font-semibold text-xs md:text-sm mb-2 uppercase tracking-wide">Total Rentals</h3>
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1">{{ $kpi_totalRentals }}</h3>
                    <p class="text-xs text-purple-600 font-medium">This month</p>
                </div>
                <div class="p-3 md:p-4 bg-purple-500 rounded-xl text-white shadow-lg icon-pulse">
                    <i data-lucide="package" class="w-6 h-6 md:w-7 md:h-7"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6 md:space-y-8">
            
            <!-- Enhanced Revenue Chart -->
            <div class="bg-white p-4 md:p-6 rounded-2xl shadow-xl border border-gray-100">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 md:mb-6">
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 text-amber-500"></i>
                            Revenue Overview
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Track your monthly performance</p>
                    </div>
                    <span class="text-xs font-semibold bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 px-4 py-2 rounded-full w-fit shadow-sm">
                        Last 6 Months
                    </span>
                </div>
                <div class="chart-container p-4 rounded-xl">
                    <div class="h-[250px] sm:h-[300px] md:h-[350px] w-full">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Enhanced Recent Customers Table -->
            <div class="bg-white p-4 md:p-6 rounded-2xl shadow-xl border border-gray-100">
                <div class="flex items-center gap-2 mb-4 md:mb-6">
                    <i data-lucide="users" class="w-5 h-5 text-indigo-500"></i>
                    <h3 class="text-lg md:text-xl font-bold text-gray-800">Recently Joined Customers</h3>
                </div>
                <div class="overflow-x-auto -mx-4 md:mx-0">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-gray-500 text-xs uppercase tracking-wider border-b-2 border-gray-200">
                                        <th class="pb-3 font-semibold px-4 md:px-0">Name</th>
                                        <th class="pb-3 font-semibold px-4 md:px-0 hidden sm:table-cell">Email</th>
                                        <th class="pb-3 font-semibold px-4 md:px-0">Phone</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($recentCustomers as $customer)
                                        <tr class="table-row-hover">
                                            <td class="py-3 md:py-4 px-4 md:px-0 text-sm font-medium text-gray-900">
                                                <div class="flex items-center gap-2 md:gap-3">
                                                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 text-white flex items-center justify-center text-sm md:text-base font-bold flex-shrink-0 shadow-md">
                                                        {{ substr($customer->customer_FullName, 0, 1) }}
                                                    </div>
                                                    <span class="truncate font-semibold">{{ $customer->customer_FullName }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3 md:py-4 px-4 md:px-0 text-sm text-gray-600 hidden sm:table-cell">
                                                <span class="truncate block max-w-[200px]">{{ $customer->user->user_Email ?? 'N/A' }}</span>
                                            </td>
                                            <td class="py-3 md:py-4 px-4 md:px-0 text-sm text-gray-600 whitespace-nowrap font-medium">{{ $customer->customer_PhoneNumber }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-12 text-center">
                                                <i data-lucide="user-x" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                                                <p class="text-gray-400 text-sm">No new customers found.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-1 space-y-6 md:space-y-8">
            
            <!-- Enhanced Field Popularity Chart -->
            <div class="bg-white p-4 md:p-6 rounded-2xl shadow-xl border border-gray-100">
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-blue-500"></i>
                    <h3 class="text-lg md:text-xl font-bold text-gray-800">Field Popularity</h3>
                </div>
                <p class="text-xs md:text-sm text-gray-500 mb-4 md:mb-6">Booking distribution across all fields</p>
                <div class="chart-container p-4 rounded-xl">
                    <div class="h-[250px] md:h-[280px] relative flex items-center justify-center">
                        <canvas id="fieldPopularityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Enhanced Pending Returns -->
            <div class="bg-white p-4 md:p-6 rounded-2xl shadow-xl border border-gray-100">
                <div class="flex justify-between items-center mb-4 md:mb-6">
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                        <h3 class="text-lg md:text-xl font-bold text-gray-800">Pending Returns</h3>
                    </div>
                    @if($pendingApprovals->count() > 0)
                        <span class="bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse">
                            {{ $pendingApprovals->count() }}
                        </span>
                    @endif
                </div>
                
                <div class="space-y-3 md:space-y-4 max-h-[400px] overflow-y-auto">
                    @forelse($pendingApprovals as $index => $task)
                    <div class="pending-item flex items-start gap-2 md:gap-3 p-3 md:p-4 rounded-xl bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 hover:shadow-md transition-all" style="animation-delay: {{ $index * 0.1 }}s">
                        <div class="mt-1 text-yellow-600 bg-yellow-100 p-2 rounded-lg flex-shrink-0 shadow-sm">
                            <i data-lucide="arrow-left-right" class="w-4 h-4 md:w-5 md:h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs md:text-sm font-bold text-gray-800 truncate">{{ $task->item->item_Name ?? 'Item' }} <span class="text-yellow-600">(x{{ $task->quantity }})</span></p>
                            <p class="text-xs text-gray-600 mb-2 truncate">By: <span class="font-medium">{{ $task->customer->customer_FullName ?? 'N/A' }}</span></p>
                            <span class="text-xs font-semibold text-yellow-800 bg-yellow-200 px-3 py-1 rounded-full inline-block">Needs Review</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i data-lucide="check-circle" class="w-16 h-16 mx-auto text-green-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">All caught up!</p>
                        <p class="text-gray-400 text-sm mt-1">No pending returns</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Initialize Lucide Icons
        lucide.createIcons();

        // Revenue Bar Chart with Enhanced Styling
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: {!! $chartLabels !!},
                datasets: [
                    {
                        label: 'Booking Revenue',
                        data: {!! $chartBookingData !!},
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                        borderColor: 'rgba(217, 119, 6, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                    {
                        label: 'Rental Revenue',
                        data: {!! $chartRentalData !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(37, 99, 235, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        ticks: { 
                            callback: function(value) { return 'RM ' + value; }, 
                            color: '#64748b',
                            font: { size: window.innerWidth < 640 ? 10 : 12, weight: '500' }
                        },
                        grid: { color: '#f1f5f9', drawBorder: false },
                        border: { display: false }
                    },
                    x: { 
                        grid: { display: false },
                        border: { display: false },
                        ticks: { 
                            color: '#64748b',
                            font: { size: window.innerWidth < 640 ? 10 : 12, weight: '500' }
                        }
                    }
                },
                plugins: {
                    legend: { 
                        position: 'top', 
                        labels: { 
                            color: '#374151',
                            font: { size: window.innerWidth < 640 ? 11 : 13, weight: '600' },
                            padding: window.innerWidth < 640 ? 10 : 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        } 
                    },
                    tooltip: { 
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#fbbf24',
                        bodyColor: '#e5e7eb',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: { 
                            label: function(context) { 
                                return `${context.dataset.label}: RM ${context.parsed.y.toFixed(2)}`; 
                            } 
                        } 
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });

        // Field Popularity Doughnut Chart with Enhanced Colors
        const ctxDoughnut = document.getElementById('fieldPopularityChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: {!! $fieldPopularityLabels !!},
                datasets: [{
                    label: 'Bookings',
                    data: {!! $fieldPopularityData !!},
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.9)',
                        'rgba(59, 130, 246, 0.9)',
                        'rgba(16, 185, 129, 0.9)',
                        'rgba(139, 92, 246, 0.9)',
                        'rgba(236, 72, 153, 0.9)',
                        'rgba(20, 184, 166, 0.9)'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 12,
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: window.innerWidth < 640 ? 12 : 20,
                            font: { size: window.innerWidth < 640 ? 10 : 12, weight: '600' },
                            color: '#374151',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#fbbf24',
                        bodyColor: '#e5e7eb',
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            }
        });

    });
</script>
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