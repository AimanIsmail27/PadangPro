@extends('layout.staff')

@section('title', 'Generate Custom Report - PadangPro Staff')

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Generate a Custom Report</h1>
    <p class="mt-2 text-lime-100">Select your criteria to generate a new report.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 w-11/12 mx-auto -mt-16 relative">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Report Criteria</h2>

    <form action="{{ route('staff.reports.show') }}" method="GET" class="space-y-6">
        
        <div>
            <label for="report_type" class="block text-sm font-medium text-gray-700">1. Select Report Type</label>
            <select name="report_type" id="report_type" required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500">
                <option value="" disabled selected>-- Choose a report --</option>
                <optgroup label="Booking Reports">
                    <option value="booking_revenue">Booking Revenue</option>
                    <option value="booking_count">Booking Volume</option>
                    <option value="field_performance">Field Performance</option>
                    <option value="peak_hours">Peak Hours Analysis</option>
                </optgroup>
                <optgroup label="Rental Reports">
                    <option value="rental_revenue">Rental Revenue</option>
                    <option value="item_popularity">Item Popularity</option>
                </optgroup>
            </select>
        </div>

        <div id="date-range-container" style="display: none;">
            <label class="block text-sm font-medium text-gray-700">2. Select Date Range</label>
            <div class="mt-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-xs text-gray-500">Start Date</label>
                    <input type="date" name="start_date" id="start_date" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500">
                </div>
                <div>
                    <label for="end_date" class="block text-xs text-gray-500">End Date</label>
                    <input type="date" name="end_date" id="end_date" 
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500">
                </div>
            </div>
        </div>

        <div id="group-by-container" style="display: none;">
            <label for="group_by" class="block text-sm font-medium text-gray-700">3. Group Results By</label>
            <select name="group_by" id="group_by" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500">
                <option value="day">Daily</option>
                <option value="week">Weekly</option>
                <option value="month">Monthly</option>
            </select>
        </div>

        <div id="filters-container" style="display: none;">
            <label class="block text-sm font-medium text-gray-700">4. Optional Filters</label>
            <div class="mt-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="field-filter-container" style="display: none;">
                    <label for="field_id" class="block text-xs text-gray-500">Filter by Field</label>
                    <select name="field_id" id="field_id" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500">
                        <option value="">All Fields</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->fieldID }}">{{ $field->field_Label }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="item-filter-container" style="display: none;">
                    <label for="item_id" class="block text-xs text-gray-500">Filter by Rental Item</label>
                    <select name="item_id" id="item_id" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500">
                        <option value="">All Items</option>
                        @foreach($items as $item)
                            <option value="{{ $item->itemID }}">{{ $item->item_Name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t">
            <button type="submit" 
                    class="bg-lime-600 hover:bg-lime-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition transform hover:scale-105">
                Generate Report
            </button>
            <a href="{{ route('staff.reports.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reportTypeSelect = document.getElementById('report_type');
        
        // Main containers
        const dateRangeContainer = document.getElementById('date-range-container');
        const groupByContainer = document.getElementById('group-by-container');
        const filtersContainer = document.getElementById('filters-container');

        // Specific filter containers
        const fieldFilterContainer = document.getElementById('field-filter-container');
        const itemFilterContainer = document.getElementById('item-filter-container');

        // Input fields
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        // Pre-fill form from URL query
        const formDefaults = @json($formDefaults);
        if (formDefaults.report_type) {
            reportTypeSelect.value = formDefaults.report_type;
        }
        if (formDefaults.start_date) {
            startDateInput.value = formDefaults.start_date;
        }
        if (formDefaults.end_date) {
            endDateInput.value = formDefaults.end_date;
        }
        if (formDefaults.group_by) {
            document.getElementById('group_by').value = formDefaults.group_by;
        }
        if (formDefaults.field_id) {
            document.getElementById('field_id').value = formDefaults.field_id;
        }
        if (formDefaults.item_id) {
            document.getElementById('item_id').value = formDefaults.item_id;
        }

        function updateFormVisibility() {
            const selectedType = reportTypeSelect.value;

            // --- Reset: Hide everything first ---
            dateRangeContainer.style.display = 'none';
            groupByContainer.style.display = 'none';
            filtersContainer.style.display = 'none';
            fieldFilterContainer.style.display = 'none';
            itemFilterContainer.style.display = 'none';
            
            // Default to not required
            startDateInput.required = false;
            endDateInput.required = false;

            // --- Enable/disable based on selection ---
            if (!selectedType) return; // Do nothing if no report is selected

            // All our reports require a date range
            dateRangeContainer.style.display = 'block';
            startDateInput.required = true;
            endDateInput.required = true;

            // Reports that are trends over time need "Group By"
            if (['booking_revenue', 'booking_count', 'rental_revenue'].includes(selectedType)) {
                groupByContainer.style.display = 'block';
            }

            // Show filters container if any filter is applicable
            if (selectedType.startsWith('booking_') || selectedType === 'field_performance' || selectedType === 'peak_hours' || selectedType.startsWith('rental_') || selectedType === 'item_popularity') {
                filtersContainer.style.display = 'block';
            }

            // Show specific filters for booking reports
            if (selectedType.startsWith('booking_') || selectedType === 'field_performance' || selectedType === 'peak_hours') {
                if (selectedType !== 'field_performance') { // Field performance shows *all* fields, no filter needed
                    fieldFilterContainer.style.display = 'block';
                }
            }

            // Show specific filters for rental reports
            if (selectedType.startsWith('rental_') || selectedType === 'item_popularity') {
                 if (selectedType !== 'item_popularity') { // Item popularity shows *all* items, no filter needed
                    itemFilterContainer.style.display = 'block';
                }
            }
        }

        reportTypeSelect.addEventListener('change', updateFormVisibility);

        // Run on page load to set the initial correct state (for pre-filled forms)
        updateFormVisibility();
    });
</script>
@endpush