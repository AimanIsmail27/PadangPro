@extends('layout.customer')

@section('title', 'Rental Items')

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
    <div class="text-white font-bold text-2xl px-8">
        ITEM FOR RENTAL
    </div>
</div>
<div class="flex items-center justify-between mb-6">
    <form action="{{ route('customer.rental.main') }}" method="GET" class="flex items-center space-x-2">
        <input type="date" name="rental_date" 
               value="{{ request()->get('rental_date', date('Y-m-d')) }}" 
               class="border rounded px-3 py-2">
        <button type="submit" 
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
            Check Availability
        </button>
    </form>

    <a href="{{ route('customer.rental.history') }}"
       class="bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition">
       View Rental History
    </a>
</div>


@if(count($availableItems) > 0)
<div class="mb-8">
    <h3 class="text-xl font-semibold text-green-700 mb-4">Available Items</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($availableItems as $item)
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                <h4 class="text-lg font-bold text-gray-800">{{ strtoupper($item->item_Name) }}</h4>
                <p class="text-gray-600 mt-2">{{ strtoupper($item->item_Description) }}</p>
                <div class="flex justify-between items-center mt-4">
                    <span class="font-semibold text-gray-700">Qty: {{ $item->available_quantity }}</span>
                    <span class="font-semibold text-gray-700">Price: RM {{ number_format($item->item_Price, 2) }}</span>
                </div>
                <span class="inline-block mt-3 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    Available
                </span>

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('customer.rental.rent', ['itemID' => $item->itemID, 'rental_date' => request()->get('rental_date', date('Y-m-d'))]) }}"
                       class="bg-green-500 text-white px-4 py-1 rounded shadow hover:bg-green-600 transition">
                        Rent Now
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@else
    <p class="text-gray-600">No rental items available for the selected date.</p>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    @endif
</script>
@endpush