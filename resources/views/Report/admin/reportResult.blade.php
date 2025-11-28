@extends('layout.admin')

@section('title', 'Custom Report Result')

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold">Custom Report Result</h1>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 w-11/12 mx-auto -mt-16 relative">

    {{-- TITLE + BUTTONS SECTION --}}
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">

        <h2 class="text-xl font-bold text-gray-800">{{ $customReportTitle }}</h2>

        {{-- BUTTON GROUP --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 gap-3 w-full sm:w-auto">

            @if(!$request->has('view_only'))

                {{-- Publish Button (full-width on mobile) --}}
                <button 
                    type="button" 
                    id="publishReportBtn" 
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md 
                           transition transform hover:scale-105 w-full sm:w-auto text-center">
                    Publish this Report
                </button>

                {{-- Edit Criteria (full-width on mobile) --}}
                <a href="{{ route('admin.reports.create', $request->query()) }}"
                    class="text-sm text-amber-600 hover:text-amber-800 font-semibold 
                           w-full sm:w-auto text-center block">
                    Edit Criteria
                </a>

                {{-- Back to Dashboard (full-width on mobile) --}}
                <a href="{{ route('admin.reports.index') }}"
                    class="text-sm text-gray-600 hover:text-gray-900 
                           w-full sm:w-auto text-center block">
                    &larr; Back to Dashboard
                </a>

            @else

                {{-- VIEWING PUBLISHED REPORT (full-width on mobile) --}}
                <a href="{{ route('admin.reports.published') }}"
                    class="text-sm text-gray-600 hover:text-gray-900 
                           w-full sm:w-auto text-center block">
                    &larr; Back to Published List
                </a>

            @endif
        </div>
    </div>

    {{-- EMPTY RESULT HANDLING --}}
    @if(empty(json_decode($customChartData)) || count(json_decode($customChartData)) === 0)
        <div class="text-center py-12 bg-gray-50 border-2 border-dashed rounded-lg">
            <p class="text-gray-500">No data found for the selected criteria.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-gray-50/70 p-4 rounded-lg border h-[450px]">
                <canvas id="customReportChart"></canvas>
            </div>
            <div class="lg:col-span-1 bg-gray-50 p-6 rounded-lg border">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Report Summary</h3>
                <div class="space-y-4 text-sm">
                    @if(isset($summaryData['total']))
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Value:</span>
                            <span class="font-bold text-gray-900 text-base">{{ $summaryData['total'] }}</span>
                        </div>
                    @endif
                    @if(isset($summaryData['average']))
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Average / Day:</span>
                            <span class="font-bold text-gray-900">{{ $summaryData['average'] }}</span>
                        </div>
                    @endif
                    @if(isset($summaryData['peak_period']))
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Peak Period:</span>
                            <span class="font-bold text-green-600">{{ $summaryData['peak_period'] }}</span>
                        </div>
                    @endif
                    @if(isset($summaryData['most_popular']))
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Most Popular:</span>
                            <span class="font-bold text-blue-600">{{ $summaryData['most_popular'] }}</span>
                        </div>
                    @endif
                    @if(isset($summaryData['busiest_time']))
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Busiest Time:</span>
                            <span class="font-bold text-purple-600">{{ $summaryData['busiest_time'] }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@if(!$request->has('view_only'))
<form id="publishForm" action="{{ route('admin.reports.publish') }}" method="POST" style="display: none;">
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

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Chart Rendering
    @if($customChartData && (is_array(json_decode($customChartData)) && count(json_decode($customChartData)) > 0))
        const ctxCustom = document.getElementById('customReportChart').getContext('2d');
        const customLabels = {!! $customChartLabels !!};
        const customData = {!! $customChartData !!};
        let chartConfig;
        const reportType = '{{ $reportType }}';

        if (reportType === 'field_performance') {
            chartConfig = { 
                type: 'doughnut',
                data: {
                    labels: customLabels,
                    datasets: [{
                        label: 'Number of Bookings',
                        data: customData,
                        backgroundColor: [
                            'rgba(59,130,246,0.7)',
                            'rgba(245,158,11,0.7)',
                            'rgba(16,185,129,0.7)'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 3
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            };
        }
        else if (reportType === 'peak_hours') {
            chartConfig = {
                type: 'bar',
                data: {
                    labels: customLabels,
                    datasets: [{
                        label: 'Total Bookings',
                        data: customData,
                        backgroundColor: 'rgba(16,185,129,0.7)',
                        borderColor: 'rgba(13,148,136,1)',
                        borderWidth: 2,
                        borderRadius: 5
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            };
        }
        else if (reportType === 'item_popularity') {
            customLabels.reverse(); customData.reverse();
            chartConfig = {
                type: 'bar',
                data: {
                    labels: customLabels,
                    datasets: [{
                        label: 'Quantity Rented',
                        data: customData,
                        backgroundColor: 'rgba(20,184,166,0.7)',
                        borderColor: 'rgba(13,148,136,1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: { indexAxis:'y', responsive:true, maintainAspectRatio:false }
            };
        }
        else {
            chartConfig = {
                type:'line',
                data:{
                    labels: customLabels,
                    datasets:[{
                        label:'Value',
                        data:customData,
                        fill:true,
                        backgroundColor:'rgba(236,72,153,0.1)',
                        borderColor:'rgba(236,72,153,1)',
                        tension:0.3
                    }]
                },
                options:{ responsive:true, maintainAspectRatio:false }
            };
        }

        new Chart(ctxCustom, chartConfig);
    @endif

    // Publish Logic
    const publishBtn = document.getElementById('publishReportBtn');
    if (publishBtn) {
        const publishForm = document.getElementById('publishForm');
        const reportTitleInput = document.getElementById('reportTitleInput');

        publishBtn.addEventListener('click', function () {
            Swal.fire({
                title: 'Publish Report',
                text: 'Please enter a name for this report configuration:',
                input: 'text',
                inputValue: '{{ addslashes($customReportTitle) }}',
                showCancelButton: true,
                confirmButtonText: 'Save & Publish',
                confirmButtonColor: '#16a34a',
                inputValidator: (value) => { if (!value) return 'You need to enter a name!' }
            })
            .then((result) => {
                if (result.isConfirmed) {
                    reportTitleInput.value = result.value;
                    publishForm.submit();
                }
            });
        });
    }
});
</script>
@endpush
