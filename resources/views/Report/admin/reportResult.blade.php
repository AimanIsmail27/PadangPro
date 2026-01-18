@extends('layout.admin')

@section('title', 'Custom Report Result')

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold">Custom Report Result</h1>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 w-11/12 mx-auto -mt-16 relative">

    {{-- HEADER --}}
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h2 class="text-xl font-bold text-gray-800">{{ $customReportTitle }}</h2>

        <div class="flex flex-col sm:flex-row gap-3">
            @if(!$request->has('view_only'))
                <button id="publishReportBtn"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                    Publish this Report
                </button>

                <a href="{{ route('admin.reports.create', $request->query()) }}"
                   class="text-sm text-amber-600 font-semibold text-center">
                    Edit Criteria
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   class="text-sm text-gray-600 text-center">
                    ← Back to Dashboard
                </a>
            @else
                <a href="{{ route('admin.reports.published') }}"
                   class="text-sm text-gray-600 text-center">
                    ← Back to Published List
                </a>
            @endif
        </div>
    </div>

    {{-- EMPTY STATE --}}
    @if(empty(json_decode($customChartData)) || count(json_decode($customChartData)) === 0)
        <div class="text-center py-12 bg-gray-50 border-2 border-dashed rounded-lg">
            <p class="text-gray-500">No data found for the selected criteria.</p>
        </div>
    @else

        {{-- MAIN CONTENT --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- CHART --}}
            <div class="lg:col-span-2 bg-gray-50/70 p-4 rounded-lg border h-[450px]">
                <canvas id="customReportChart"></canvas>
            </div>

            {{-- SUMMARY --}}
            <div class="bg-gray-50 p-6 rounded-lg border">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                    Report Summary
                </h3>

                <div class="space-y-4 text-sm">
                    @foreach($summaryData as $label => $value)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 capitalize">
                                {{ str_replace('_',' ', $label) }}:
                            </span>
                            <span class="font-bold text-gray-900">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RENTAL-SPECIFIC INSIGHT --}}
        @if(in_array($reportType, ['rental_revenue', 'item_popularity']))
        <div class="mt-10 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="text-lg font-bold text-blue-800 mb-3">
                📊 Rental Insights
            </h3>

            <ul class="space-y-2 text-sm text-blue-900">
                <li>
                    • Rental activity during this period shows
                    <strong>consistent demand</strong>, indicating stable usage of rental items.
                </li>

                @if(isset($summaryData['peak_period']))
                <li>
                    • The highest rental contribution occurred during
                    <strong>{{ $summaryData['peak_period'] }}</strong>,
                    suggesting a peak usage window.
                </li>
                @endif

                @if(isset($summaryData['average']))
                <li>
                    • Average daily rental revenue suggests
                    <strong>predictable income flow</strong>,
                    useful for inventory and pricing planning.
                </li>
                @endif

                <li>
                    • Popular rental items can be prioritized for
                    <strong>restocking and maintenance</strong>
                    to maximize availability.
                </li>
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

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Chart
    const ctx = document.getElementById('customReportChart').getContext('2d');
    const labels = {!! $customChartLabels !!};
    const data = {!! $customChartData !!};
    const type = '{{ $reportType }}';

    let config = {
        type: type === 'item_popularity' ? 'bar' : 'line',
        data: {
            labels,
            datasets: [{
                label: 'Value',
                data,
                backgroundColor: 'rgba(59,130,246,0.4)',
                borderColor: 'rgba(59,130,246,1)',
                borderWidth: 2,
                tension: 0.3,
                borderRadius: 6,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    };

    if(type === 'item_popularity') {
        config.options.indexAxis = 'y';
    }

    new Chart(ctx, config);

    // Publish
    const publishBtn = document.getElementById('publishReportBtn');
    if (publishBtn) {
        publishBtn.addEventListener('click', function () {
            Swal.fire({
                title: 'Publish Report',
                input: 'text',
                inputValue: '{{ addslashes($customReportTitle) }}',
                showCancelButton: true,
                confirmButtonText: 'Publish',
                inputValidator: v => !v && 'Report title required'
            }).then(res => {
                if(res.isConfirmed){
                    document.getElementById('reportTitleInput').value = res.value;
                    document.getElementById('publishForm').submit();
                }
            });
        });
    }
});
</script>
@endpush
