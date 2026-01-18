@extends('layout.admin')

@section('title', 'Custom Report Result')

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-6 sm:px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold">Custom Report Result</h1>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-5 sm:p-8 w-[95%] sm:w-11/12 mx-auto -mt-16 relative overflow-hidden">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-800 break-words">{{ $customReportTitle }}</h2>
            <p class="text-sm text-gray-500 mt-1 break-words">
                Generated on {{ \Carbon\Carbon::now('Asia/Kuala_Lumpur')->format('d M Y, h:i A') }}
                @if($request->start_date && $request->end_date)
                    • Range: {{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} → {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}
                @endif
                @if($request->field_id)
                    • Field filter applied
                @endif
                @if($request->item_id)
                    • Item filter applied
                @endif
            </p>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
        {{-- DOWNLOAD PDF (ALWAYS AVAILABLE) --}}
        <button id="downloadPdfBtn"
                class="bg-slate-600 hover:bg-slate-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition w-full sm:w-auto">
            Download PDF
        </button>
    
        @if(!$request->has('view_only'))
                <button id="publishReportBtn"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md transition w-full sm:w-auto">
                    Publish this Report
                </button>
                

                <a href="{{ route('admin.reports.create', $request->query()) }}"
                   class="text-sm text-amber-700 hover:text-amber-900 font-semibold text-center w-full sm:w-auto">
                    Edit Criteria
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   class="text-sm text-gray-600 hover:text-gray-900 text-center w-full sm:w-auto">
                    ← Back to Dashboard
                </a>
            @else
                <a href="{{ route('admin.reports.published') }}"
                   class="text-sm text-gray-600 hover:text-gray-900 text-center w-full sm:w-auto">
                    ← Back to Published List
                </a>
            @endif
        </div>
    </div>

    {{-- EMPTY STATE --}}
    @php
        $decodedData = is_string($customChartData) ? json_decode($customChartData, true) : $customChartData;
        $decodedLabels = is_string($customChartLabels) ? json_decode($customChartLabels, true) : $customChartLabels;
        $hasData = is_array($decodedData) && count($decodedData) > 0;
    @endphp

    @if(!$hasData)
        <div class="text-center py-12 bg-gray-50 border-2 border-dashed rounded-lg">
            <p class="text-gray-600 font-semibold">No data found for the selected criteria.</p>
            <p class="text-sm text-gray-500 mt-2">
                Try widening the date range or removing optional filters (Field/Item).
            </p>
        </div>
    @else

        {{-- QUICK KPI STRIP --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

            {{-- TOTAL --}}
            <div class="rounded-xl border bg-amber-50/60 p-4 overflow-hidden">
                <p class="text-xs text-amber-700 font-semibold uppercase tracking-wide">Total</p>
                <p class="text-2xl font-extrabold text-amber-900 mt-1 break-words">
                    {{ $summaryData['total'] ?? '—' }}
                </p>
                <p class="text-xs text-amber-700/80 mt-1">
                    Overall result for selected period
                </p>
            </div>

            {{-- DISTRIBUTION (REPLACES AVERAGE) --}}
            <div class="rounded-xl border bg-sky-50/60 p-4 overflow-hidden">
                <p class="text-xs text-sky-700 font-semibold uppercase tracking-wide">Distribution</p>

                @php
                    // Heuristic: if there is a named peak period/time, treat as more concentrated.
                    // Otherwise, treat as more evenly distributed.
                    $distributionLabel = (isset($summaryData['peak_period']) || isset($summaryData['busiest_time']))
                        ? 'Peak Concentrated'
                        : 'Evenly Distributed';
                @endphp

                <p class="text-lg font-extrabold text-sky-900 mt-1 break-words">
                    {{ $distributionLabel }}
                </p>
                <p class="text-xs text-sky-700/80 mt-1">
                    Indicates demand spread across the period
                </p>
            </div>

            {{-- PEAK --}}
            <div class="rounded-xl border bg-emerald-50/60 p-4 overflow-hidden">
                <p class="text-xs text-emerald-700 font-semibold uppercase tracking-wide">Peak</p>
                <p class="text-base sm:text-lg font-extrabold text-emerald-900 mt-1 break-words">
                    @if(isset($summaryData['peak_period']))
                        {{ $summaryData['peak_period'] }}
                    @elseif(isset($summaryData['most_popular']))
                        {{ $summaryData['most_popular'] }}
                    @elseif(isset($summaryData['busiest_time']))
                        {{ $summaryData['busiest_time'] }}
                    @else
                        —
                    @endif
                </p>
                <p class="text-xs text-emerald-700/80 mt-1">
                    Key demand concentration indicator
                </p>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- CHART --}}
            <div class="lg:col-span-2 bg-gray-50/70 p-4 rounded-xl border h-[460px] overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800">Visualization</p>
                        <p class="text-xs text-gray-500 break-words">
                            Interpreting trend and distribution for the selected criteria.
                        </p>
                    </div>

                    <span class="text-xs px-3 py-1 rounded-full bg-white border text-gray-600 shrink-0">
                        Report Type: <span class="font-semibold">{{ ucwords(str_replace('_',' ', $reportType)) }}</span>
                    </span>
                </div>

                <canvas id="customReportChart"></canvas>
            </div>

            {{-- SUMMARY --}}
            <div class="bg-white rounded-xl border p-6 overflow-hidden">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                    Report Summary
                </h3>

                <div class="space-y-3 text-sm">
                    @forelse($summaryData as $label => $value)
                        @if($label !== 'average')
                            <div class="flex justify-between items-start gap-4">
                                <span class="text-gray-600 capitalize whitespace-nowrap">
                                    {{ str_replace('_',' ', $label) }}:
                                </span>
                                <span class="font-bold text-gray-900 text-right break-words max-w-[65%]">
                                    {{ $value }}
                                </span>
                            </div>
                        @endif
                    @empty
                        <p class="text-gray-500">No summary available.</p>
                    @endforelse
                </div>

                {{-- A MORE “REPORT-LIKE” EXPLANATION --}}
                <div class="mt-6 rounded-lg bg-gray-50 border p-4">
                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">How to read this</p>
                    <ul class="mt-2 text-sm text-gray-600 space-y-2 leading-relaxed">
                        <li>• <strong>Total</strong> shows the overall performance for the selected date range.</li>
                        <li>• <strong>Distribution</strong> indicates whether demand is evenly spread or concentrated at specific periods.</li>
                        <li>• <strong>Peak</strong> highlights the period/category with the highest demand contribution.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- BOOKING INSIGHT --}}
        @if(in_array($reportType, ['booking_revenue','booking_count','field_performance','peak_hours']))
            <div class="mt-10 bg-emerald-50 border border-emerald-200 rounded-xl p-6 overflow-hidden">
                <h3 class="text-lg font-bold text-emerald-800 mb-3">📈 Booking Insights</h3>

                <ul class="space-y-2 text-sm text-emerald-900 leading-relaxed break-words">
                    @if($reportType === 'booking_revenue')
                        <li>• The total booking revenue reflects the <strong>overall financial performance</strong> of pitch reservations in this period.</li>
                        @if(isset($summaryData['peak_period']))
                            <li>• Revenue peaked at <strong>{{ $summaryData['peak_period'] }}</strong>, suggesting demand concentration that may support <strong>pricing optimisation</strong> (peak vs off-peak).</li>
                        @endif
                        <li>• The distribution indicator helps highlight whether bookings are clustered around certain dates (spike-heavy) or stable across the period.</li>
                        <li>• Consider promotions for low-performing days to improve utilisation without affecting peak demand.</li>
                    @endif

                    @if($reportType === 'booking_count')
                        <li>• Total bookings indicate the <strong>volume of customer activity</strong> for this period.</li>
                        @if(isset($summaryData['peak_period']))
                            <li>• The busiest period is <strong>{{ $summaryData['peak_period'] }}</strong>, which may require better slot management and on-site coordination.</li>
                        @endif
                        <li>• If demand is peak-concentrated, planning staff and facility readiness around those spikes can reduce congestion.</li>
                    @endif

                    @if($reportType === 'field_performance')
                        <li>• Field performance shows <strong>which pitch is most frequently used</strong>.</li>
                        @if(isset($summaryData['most_popular']))
                            <li>• The most utilised field is <strong>{{ $summaryData['most_popular'] }}</strong>, which may require more frequent maintenance to preserve quality.</li>
                        @endif
                        <li>• Lower-performing fields can be targeted with promos or bundled booking packages.</li>
                    @endif

                    @if($reportType === 'peak_hours')
                        <li>• Peak hour analysis identifies the <strong>time slots with highest booking demand</strong>.</li>
                        @if(isset($summaryData['busiest_time']))
                            <li>• The busiest time is <strong>{{ $summaryData['busiest_time'] }}</strong>, suitable for peak pricing or allocating more staff.</li>
                        @endif
                        <li>• Off-peak hours can be improved using discounted rates or loyalty rewards.</li>
                    @endif
                </ul>
            </div>
        @endif

        {{-- RENTAL INSIGHT --}}
        @if(in_array($reportType, ['rental_revenue', 'item_popularity']))
            <div class="mt-10 bg-blue-50 border border-blue-200 rounded-xl p-6 overflow-hidden">
                <h3 class="text-lg font-bold text-blue-800 mb-3">📊 Rental Insights</h3>

                <ul class="space-y-2 text-sm text-blue-900 leading-relaxed break-words">
                    <li>• Rental activity during this period indicates <strong>customer demand for equipment</strong>, supporting inventory decisions.</li>

                    @if(isset($summaryData['peak_period']))
                        <li>• Rental contribution peaked at <strong>{{ $summaryData['peak_period'] }}</strong>, indicating a window of higher equipment usage.</li>
                    @endif

                    @if(isset($summaryData['most_popular']))
                        <li>• The most popular item is <strong>{{ $summaryData['most_popular'] }}</strong>, which should be prioritised for <strong>restocking and maintenance</strong>.</li>
                    @endif

                    <li>• If demand is peak-concentrated, consider preparing inventory before those peak windows to avoid shortages.</li>
                    <li>• Consider adjusting rental prices for high-demand items to improve profitability while maintaining availability.</li>
                </ul>
            </div>
        @endif

    @endif
</div>

{{-- PUBLISH FORM --}}
@if(!$request->has('view_only'))
<form id="publishForm" action="{{ route('admin.reports.publish') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="report_Title" id="reportTitleInput">
    <input type="hidden" name="report_type" value="{{ $request->report_type }}">
    <input type="hidden" name="start_date" value="{{ $request->start_date }}">
    <input type="hidden" name="end_date" value="{{ $request->end_date }}">
    <input type="hidden" name="group_by" value="{{ $request->group_by }}">
    <input type="hidden" name="field_id" value="{{ $request->field_id }}">
    <input type="hidden" name="item_id" value="{{ $request->item_id }}">
</form>
@endif

{{-- PDF DOWNLOAD FORM --}}
<form id="pdfForm" action="{{ route('admin.reports.pdf') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="chart_image" id="pdf_chart_image">
    <input type="hidden" name="title" value="{{ $customReportTitle }}">
    <input type="hidden" name="report_type" value="{{ $reportType }}">
    <input type="hidden" name="summary" value='@json($summaryData)'>
    <input type="hidden" name="meta" value='@json($request->all())'>
</form>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Chart
    const canvas = document.getElementById('customReportChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const labels = {!! $customChartLabels !!};
    const data = {!! $customChartData !!};
    const type = '{{ $reportType }}';

    let chartType = 'line';
    if (type === 'field_performance') chartType = 'doughnut';
    if (type === 'peak_hours') chartType = 'bar';
    if (type === 'item_popularity') chartType = 'bar';

    const datasetBase = {
        label: 'Value',
        data: data,
        borderWidth: 2,
        tension: 0.3,
        borderRadius: 6
    };

    let config = {
        type: chartType,
        data: {
            labels: labels,
            datasets: [{
                ...datasetBase,
                backgroundColor: chartType === 'doughnut'
                    ? ['rgba(59,130,246,0.7)','rgba(245,158,11,0.7)','rgba(16,185,129,0.7)','rgba(236,72,153,0.7)','rgba(99,102,241,0.7)']
                    : 'rgba(59,130,246,0.20)',
                borderColor: chartType === 'doughnut'
                    ? '#ffffff'
                    : 'rgba(59,130,246,1)',
                fill: chartType === 'line'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: chartType === 'doughnut' ? 'bottom' : 'top' }
            },
            scales: chartType === 'doughnut' ? {} : {
                y: { beginAtZero: true }
            }
        }
    };

    if (type === 'item_popularity') {
        config.options.indexAxis = 'y';
        config.data.datasets[0].backgroundColor = 'rgba(20,184,166,0.35)';
        config.data.datasets[0].borderColor = 'rgba(13,148,136,1)';
        config.data.datasets[0].fill = false;
    }

    if (type === 'peak_hours') {
        config.data.datasets[0].backgroundColor = 'rgba(16,185,129,0.35)';
        config.data.datasets[0].borderColor = 'rgba(13,148,136,1)';
        config.data.datasets[0].fill = false;
    }

    new Chart(ctx, config);

    // Publish
    const publishBtn = document.getElementById('publishReportBtn');
    if (publishBtn) {
        publishBtn.addEventListener('click', function () {
            Swal.fire({
                title: 'Publish Report',
                text: 'Please enter a name for this report configuration:',
                input: 'text',
                inputValue: '{{ addslashes($customReportTitle) }}',
                showCancelButton: true,
                confirmButtonText: 'Save & Publish',
                confirmButtonColor: '#16a34a',
                inputValidator: v => !v && 'Report title required'
            }).then(res => {
                if(res.isConfirmed){
                    document.getElementById('reportTitleInput').value = res.value;
                    document.getElementById('publishForm').submit();
                }
            });
        });
    }
// ===== DOWNLOAD PDF =====
const downloadBtn = document.getElementById('downloadPdfBtn');

if (downloadBtn) {
    downloadBtn.addEventListener('click', function () {
        const canvas = document.getElementById('customReportChart');
        if (!canvas) return;

        const imageData = canvas.toDataURL('image/png');
        document.getElementById('pdf_chart_image').value = imageData;

        document.getElementById('pdfForm').submit();
    });
}

});
</script>
@endpush
