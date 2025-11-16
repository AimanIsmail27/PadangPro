@extends('layout.admin')

@section('title', 'Reports Dashboard - PadangPro Admin')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-32 px-10 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold">Reports Dashboard</h1>
   
</div>

<div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-24 relative space-y-12">

    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">This Month's Performance ({{ \Carbon\Carbon::now('Asia/Kuala_Lumpur')->format('F Y') }})</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500"><div class="flex items-center justify-between"><div><p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Booking Revenue</p><p class="text-4xl font-bold text-gray-800 mt-2">RM {{ number_format($kpi_revenue, 0) }}</p></div><div class="p-4 rounded-full bg-green-100 text-green-600"><i class="bi bi-cash-stack text-3xl"></i></div></div></div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500"><div class="flex items-center justify-between"><div><p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Bookings</p><p class="text-4xl font-bold text-gray-800 mt-2">{{ $kpi_bookings }}</p></div><div class="p-4 rounded-full bg-blue-100 text-blue-600"><i class="bi bi-calendar2-check text-3xl"></i></div></div></div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-teal-500"><div class="flex items-center justify-between"><div><p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Rental Revenue</p><p class="text-4xl font-bold text-gray-800 mt-2">RM {{ number_format($kpi_rental_revenue, 0) }}</p></div><div class="p-4 rounded-full bg-teal-100 text-teal-600"><i class="bi bi-receipt text-3xl"></i></div></div></div>
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500"><div class="flex items-center justify-between"><div><p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Items Rented</p><p class="text-4xl font-bold text-gray-800 mt-2">{{ $kpi_items_rented }}</p></div><div class="p-4 rounded-full bg-indigo-100 text-indigo-600"><i class="bi bi-tags-fill text-3xl"></i></div></div></div>
        </div>
    </div>

    <div>
        <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Automated Reports</h2>
            <a href="{{ route('admin.reports.create') }}" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition transform hover:scale-105">
                Generate New Report
            </a>
        </div>
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Last 6 Months Booking Revenue</h3>
                <div class="bg-gray-50/70 p-4 rounded-lg border h-[400px]">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Top Rented Items (Last 90 Days)</h3>
                <div class="bg-gray-50/70 p-4 rounded-lg border h-[400px]">
                    <canvas id="topItemsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="pt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            <i class="bi bi-robot text-amber-500"></i> AI Generated: Next 7-Day Booking Demand Forecast
        </h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-gray-50/70 p-4 rounded-lg border h-[400px]">
                <canvas id="forecastChart"></canvas>
            </div>

            <div class="lg:col-span-1 bg-gray-50 p-6 rounded-lg border">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Key Insights</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 flex items-center"><i class="bi bi-calendar-week mr-2"></i>Total Forecast (7 days):</span>
                        <span id="forecast-total" class="font-bold text-gray-900 text-base">Calculating...</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 flex items-center"><i class="bi bi-graph-up-arrow text-green-600 mr-2"></i>Predicted Busiest Day:</span>
                        <span id="forecast-peak" class="font-bold text-green-600">Calculating...</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 flex items-center"><i class="bi bi-graph-down-arrow text-red-600 mr-2"></i>Predicted Quietest Day:</span>
                        <span id="forecast-low" class="font-bold text-red-600">Calculating...</span>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mt-6 mb-4 border-b pb-2">AI Recommendation</h3>
                <div id="forecast-recommendation" class="text-sm text-gray-700 bg-amber-50 border border-amber-200 p-3 rounded-md">
                    <p>Analyzing trends...</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- SCRIPT FOR BOOKING REVENUE CHART ---
        const ctxBooking = document.getElementById('monthlyRevenueChart').getContext('2d');
        const chartLabels = {!! $chartLabels !!};
        const chartData = {!! $chartData !!};
        new Chart(ctxBooking, { type: 'bar', data: { labels: chartLabels, datasets: [{ label: 'Total Revenue (RM)', data: chartData, backgroundColor: 'rgba(252, 211, 77, 0.7)', borderColor: 'rgba(245, 158, 11, 1)', borderWidth: 2, borderRadius: 8, hoverBackgroundColor: 'rgba(245, 158, 11, 0.9)' }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, grid: { color: '#e5e7eb' }, ticks: { callback: function(value) { return 'RM ' + value.toLocaleString(); } } }, x: { grid: { display: false } } }, plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1f2937', callbacks: { label: function(context) { return 'Revenue: RM ' + context.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } } } } } });

        // --- SCRIPT FOR TOP RENTED ITEMS CHART ---
        const ctxRental = document.getElementById('topItemsChart').getContext('2d');
        const topItemsLabels = {!! $topItemsLabels !!};
        const topItemsQuantities = {!! $topItemsQuantities !!};
        new Chart(ctxRental, { type: 'doughnut', data: { labels: topItemsLabels, datasets: [{ label: 'Quantity Rented', data: topItemsQuantities, backgroundColor: [ 'rgba(59, 130, 246, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(139, 92, 246, 0.7)', 'rgba(239, 68, 68, 0.7)' ], borderColor: '#ffffff', borderWidth: 3 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: function(context) { return ` ${context.label}: ${context.parsed} items rented`; } } } } } });
        
        // ===============================================
        // UPGRADED SCRIPT FOR AI FORECAST
        // ===============================================
        const ctxForecast = document.getElementById('forecastChart').getContext('2d');
        
        fetch('{{ route("admin.reports.forecast") }}')
            .then(response => {
                if (!response.ok) { throw new Error('Network response was not ok'); }
                return response.json();
            })
            .then(data => {
                // Draw the chart
                new Chart(ctxForecast, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Predicted Bookings', data: data.data, fill: true, backgroundColor: 'rgba(59, 130, 246, 0.1)', borderColor: 'rgba(59, 130, 246, 1)', tension: 0.3, pointRadius: 5, pointBackgroundColor: 'rgba(59, 130, 246, 1)'
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }, plugins: { legend: { display: false } } }
                });

                // Calculate and Display Insights
                if (data.data && data.data.length > 0) {
                    const totalBookings = data.data.reduce((sum, value) => sum + value, 0);
                    const peakValue = Math.max(...data.data);
                    const lowValue = Math.min(...data.data);
                    const peakIndex = data.data.indexOf(peakValue);
                    const lowIndex = data.data.indexOf(lowValue);
                    const peakDay = data.labels[peakIndex];
                    const lowDay = data.labels[lowIndex];

                    let recommendation = "Demand appears to be average. Monitor bookings as usual.";
                    if (peakValue >= 8) {
                        recommendation = `High demand is predicted for <strong>${peakDay}</strong>. Consider ensuring adequate staff and checking rental inventory.`;
                    } else if (lowValue <= 2 && lowValue > 0) {
                        recommendation = `Low demand is predicted for <strong>${lowDay}</strong>. This could be a good opportunity to run a promotion or schedule maintenance.`;
                    }

                    document.getElementById('forecast-total').innerText = `${totalBookings} Bookings`;
                    document.getElementById('forecast-peak').innerText = `${peakDay} (~${peakValue} bookings)`;
                    document.getElementById('forecast-low').innerText = `${lowDay} (~${lowValue} bookings)`;
                    document.getElementById('forecast-recommendation').innerHTML = `<p>${recommendation}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching forecast data:', error);
                const forecastRecommendation = document.getElementById('forecast-recommendation');
                if (forecastRecommendation) {
                    forecastRecommendation.innerHTML = `<p class="text-red-600 font-semibold">Could not load forecast data. Please ensure the AI server is running and accessible.</p>`;
                }
                ctxForecast.font = "16px Arial";
                ctxForecast.fillStyle = "red";
                ctxForecast.textAlign = "center";
                ctxForecast.fillText("Forecast data unavailable.", ctxForecast.canvas.width / 2, ctxForecast.canvas.height / 2);
            });
    });
</script>
@endpush