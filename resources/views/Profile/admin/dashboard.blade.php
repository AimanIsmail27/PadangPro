@extends('layout.admin')

@section('title', 'Admin Dashboard')

@push('styles')
{{-- Bootstrap icons are not needed if we use Lucide --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"> --}}
@endpush

@section('content')
<div class="space-y-10"> {{-- Removed bg-gray-100, p-8, etc. to use the layout's <main> --}}

    <div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white p-10 rounded-2xl shadow-lg">
        <h1 class="text-3xl font-bold">Welcome back, {{ $fullName }} 👋</h1>
        <p class="mt-2 text-amber-100">Here’s what’s happening with your system this month.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-lg shadow-gray-200/50 border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-slate-500 font-medium text-sm mb-1 uppercase">Total Revenue (Month)</h3>
                    <h3 class="text-3xl font-bold text-slate-800">RM {{ number_format($kpi_totalRevenue, 2) }}</h3>
                </div>
                <div class="p-3 bg-green-100 rounded-xl text-green-600">
                    <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-lg shadow-gray-200/50 border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-slate-500 font-medium text-sm mb-1 uppercase">Total Bookings (Month)</h3>
                    <h3 class="text-3xl font-bold text-slate-800">{{ $kpi_totalBookings }}</h3>
                </div>
                <div class="p-3 bg-blue-100 rounded-xl text-blue-600">
                    <i data-lucide="calendar-check" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-lg shadow-gray-200/50 border border-slate-100">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-slate-500 font-medium text-sm mb-1 uppercase">Total Rentals (Month)</h3>
                    <h3 class="text-3xl font-bold text-slate-800">{{ $kpi_totalRentals }}</h3>
                </div>
                <div class="p-3 bg-indigo-100 rounded-xl text-indigo-600">
                    <i data-lucide="tags" class="w-6 h-6"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white p-6 rounded-2xl shadow-lg shadow-gray-200/50 border border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Revenue Overview</h3>
                    <span class="text-xs font-medium bg-gray-100 text-gray-500 px-3 py-1 rounded-full">Last 6 Months</span>
                </div>
                <div class="h-[350px] w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg shadow-gray-200/50 border border-slate-100">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Recently Joined Customers</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                <th class="pb-3 font-medium">Name</th>
                                <th class="pb-3 font-medium">Email</th>
                                <th class="pb-3 font-medium">Phone</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentCustomers as $customer)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-sm font-bold">
                                                {{ substr($customer->customer_FullName, 0, 1) }}
                                            </div>
                                            <span>{{ $customer->customer_FullName }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->user->user_Email ?? 'N/A' }}</td>
                                    <td class="py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->customer_PhoneNumber }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-gray-400 text-sm">
                                        <i data-lucide="users" class="w-8 h-8 mx-auto"></i>
                                        <p class="mt-2">No new customers found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-8">
            
            <div class="bg-white p-6 rounded-2xl shadow-lg shadow-gray-200/50 border border-slate-100">
                <h3 class="text-xl font-bold text-gray-800 mb-2">Field Popularity</h3>
                <p class="text-sm text-gray-400 mb-6">Booking distribution by field.</p>
                <div class="h-[300px] relative flex items-center justify-center">
                    <canvas id="fieldPopularityChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg shadow-gray-200/50 border border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Pending Returns</h3>
                    @if($pendingApprovals->count() > 0)
                        <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-md">{{ $pendingApprovals->count() }}</span>
                    @endif
                </div>
                
                <div class="space-y-4">
                    @forelse($pendingApprovals as $task)
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <div class="mt-1 text-yellow-600 bg-yellow-100 p-2 rounded-lg">
                            <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-700 line-clamp-1">{{ $task->item->item_Name ?? 'Item' }} (x{{ $task->quantity }})</p>
                            <p class="text-xs text-gray-500 mb-2">By: {{ $task->customer->customer_FullName ?? 'N/A' }}</p>
                            <span class="text-xs font-medium text-yellow-700 bg-yellow-50 px-2 py-1 rounded">Needs Staff Review</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-gray-400 text-sm">
                        <i data-lucide="check-check" class="w-8 h-8 mx-auto"></i>
                        <p class="mt-2">No pending returns.</p>
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
        
        // --- 1. Run Lucide Icons ---
        lucide.createIcons();

        // --- 2. Revenue Bar Chart ---
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctxRevenue.createLinearGradient(0, 0, 0, 350);
        gradient.addColorStop(0, 'rgba(245, 158, 11, 0.6)'); // Amber-500
        gradient.addColorStop(1, 'rgba(245, 158, 11, 0.1)'); // Transparent

        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: {!! $chartLabels !!},
                datasets: [
                    {
                        label: 'Booking Revenue',
                        data: {!! $chartBookingData !!},
                        backgroundColor: 'rgba(217, 119, 6, 1)', // Amber-600
                        borderColor: 'rgba(217, 119, 6, 1)',
                        borderRadius: 5,
                    },
                    {
                        label: 'Rental Revenue',
                        data: {!! $chartRentalData !!},
                        backgroundColor: 'rgba(59, 130, 246, 1)', // Blue-500
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        ticks: { callback: function(value) { return 'RM ' + value; }, color: '#64748b' },
                        grid: { color: '#f1f5f9' },
                        border: { display: false }
                    },
                    x: { 
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#64748b' }
                    }
                },
                plugins: {
                    legend: { position: 'top', labels: { color: '#374151' } },
                    tooltip: { 
                        backgroundColor: '#111827',
                        titleColor: '#e5e7eb',
                        bodyColor: '#e5e7eb',
                        padding: 10,
                        cornerRadius: 4,
                        callbacks: { label: function(context) { return `${context.dataset.label}: RM ${context.parsed.y.toFixed(2)}`; } } 
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });

        // --- 3. Field Popularity Doughnut Chart ---
        const ctxDoughnut = document.getElementById('fieldPopularityChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: {!! $fieldPopularityLabels !!},
                datasets: [{
                    label: 'Bookings',
                    data: {!! $fieldPopularityData !!},
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.9)', // Amber-500
                        'rgba(59, 130, 246, 0.9)', // Blue-500
                        'rgba(16, 185, 129, 0.9)', // Green-500
                        'rgba(139, 92, 246, 0.9)'  // Violet-500
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 4,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // Thinner ring
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: { size: 12 },
                            color: '#374151'
                        }
                    }
                }
            }
        });

    });
</script>
@endpush