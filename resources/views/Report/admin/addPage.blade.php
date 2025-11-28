@extends('layout.admin')

@section('title', 'Generate Custom Report - PadangPro Admin')

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold">Generate a Custom Report</h1>
    <p class="mt-2 text-amber-100">Select your criteria to generate a new report.</p>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 w-11/12 mx-auto -mt-16 relative">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Report Criteria</h2>

    <form action="{{ route('admin.reports.show') }}" method="GET" class="space-y-6">
        
        <div>
            <label for="report_type" class="block text-sm font-medium text-gray-700">1. Select Report Type</label>
            <select name="report_type" id="report_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
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
                    <input type="date" name="start_date" id="start_date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label for="end_date" class="block text-xs text-gray-500">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <div id="group-by-container" style="display: none;">
            <label for="group_by" class="block text-sm font-medium text-gray-700">3. Group Results By</label>
            <select name="group_by" id="group_by" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
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
                    <select name="field_id" id="field_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Fields</option>
                        @foreach($fields as $field)
                            <option value="{{ $field->fieldID }}">{{ $field->field_Label }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="item-filter-container" style="display: none;">
                    <label for="item_id" class="block text-xs text-gray-500">Filter by Rental Item</label>
                    <select name="item_id" id="item_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">All Items</option>
                        @foreach($items as $item)
                            <option value="{{ $item->itemID }}">{{ $item->item_Name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- BUTTONS RESPONSIVE AREA --}}
        <div class="pt-4 border-t">
            
            {{-- Desktop & Tablet: buttons inline --}}
            <div class="hidden md:flex items-center gap-4">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 px-6 rounded-lg shadow-md transition transform hover:scale-105">
                    Generate Report
                </button>

                <a href="{{ route('admin.reports.index') }}" 
                   class="text-gray-600 hover:text-gray-900">
                    Cancel
                </a>
            </div>

            {{-- Mobile Layout: stacked full width --}}
            <div class="flex flex-col md:hidden gap-3 mt-2">
                <button type="submit" 
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-lg shadow-md transition">
                    Generate Report
                </button>

                <a href="{{ route('admin.reports.index') }}" 
                   class="text-center text-gray-600 hover:text-gray-900 underline">
                    Cancel
                </a>
            </div>

        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reportTypeSelect = document.getElementById('report_type');
        
        const dateRangeContainer = document.getElementById('date-range-container');
        const groupByContainer = document.getElementById('group-by-container');
        const filtersContainer = document.getElementById('filters-container');

        const fieldFilterContainer = document.getElementById('field-filter-container');
        const itemFilterContainer = document.getElementById('item-filter-container');

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function updateFormVisibility() {
            const selectedType = reportTypeSelect.value;

            dateRangeContainer.style.display = 'none';
            groupByContainer.style.display = 'none';
            filtersContainer.style.display = 'none';
            fieldFilterContainer.style.display = 'none';
            itemFilterContainer.style.display = 'none';

            if (!selectedType) return;

            dateRangeContainer.style.display = 'block';
            startDateInput.required = true;
            endDateInput.required = true;

            if (['booking_revenue', 'booking_count', 'rental_revenue'].includes(selectedType)) {
                groupByContainer.style.display = 'block';
            }

            if (selectedType.startsWith('booking') || 
                selectedType === 'field_performance' || 
                selectedType === 'peak_hours' || 
                selectedType.startsWith('rental') || 
                selectedType === 'item_popularity') {
                filtersContainer.style.display = 'block';
            }

            if (selectedType.startsWith('booking') || selectedType === 'field_performance' || selectedType === 'peak_hours') {
                fieldFilterContainer.style.display = 'block';
            }

            if (selectedType.startsWith('rental') || selectedType === 'item_popularity') {
                itemFilterContainer.style.display = 'block';
            }
        }

        reportTypeSelect.addEventListener('change', updateFormVisibility);
        updateFormVisibility();
    });
</script>
@endpush
