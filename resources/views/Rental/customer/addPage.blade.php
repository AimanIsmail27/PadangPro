@extends('layout.customer')

@section('title', 'Rent Item - ' . $item->item_Name)

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Rent Item</h1>
    <p class="mt-2 text-indigo-100">Fill in your details to rent this item.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative">

    <div class="mb-8 p-6 border rounded-xl bg-slate-50">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Item Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Item</p>
                <p class="text-lg font-semibold text-indigo-700">{{ $item->item_Name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Price (per day)</p>
                <p class="text-lg font-semibold text-gray-900">RM {{ number_format($item->item_Price, 2) }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Description</p>
                <p class="text-sm text-gray-700">{{ $item->item_Description }}</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Please fix the following issues:</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="text-xl font-bold text-gray-800 mb-6">Your Rental Information</h2>
    
    <form action="{{ route('customer.rental.process', ['itemID' => $item->itemID]) }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="rental_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="rental_name" id="rental_name" value="{{ old('rental_name') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="rental_email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="rental_email" id="rental_email" value="{{ old('rental_email') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="rental_phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="rental_phone" id="rental_phone" value="{{ old('rental_phone') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="rental_backup" class="block text-sm font-medium text-gray-700">Backup Number (Optional)</label>
                <input type="text" name="rental_backup" id="rental_backup" value="{{ old('rental_backup') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="rental_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="rental_date" id="rental_start_date" value="{{ old('rental_date', $startDate) }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="rental_end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="rental_end_date" id="rental_end_date" value="{{ old('rental_end_date', $endDate) }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
        </div>

        <div>
            <label for="rental_quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
            <input type="number" name="quantity" id="rental_quantity" value="{{ old('quantity', 1) }}"
                   min="1" max="{{ $availableQuantity }}" 
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            <p class="text-gray-500 text-sm mt-2" id="max_quantity_text">
                Available for selected dates: <span class="font-semibold text-green-600">{{ $availableQuantity }}</span>
            </p>
        </div>

        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-all transform hover:scale-105">
                Continue
            </button>
            <a href="{{ route('customer.rental.main') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('rental_start_date');
    const endDateInput = document.getElementById('rental_end_date');
    const quantityInput = document.getElementById('rental_quantity');
    const maxText = document.getElementById('max_quantity_text');
    const today = new Date().toISOString().split('T')[0];

    // Set minimum date for start date to today
    if (!startDateInput.value) {
        startDateInput.value = today;
    }
    startDateInput.min = today;
    
    // Set minimum date for end date
    if (!endDateInput.value) {
        endDateInput.value = today;
    }
    endDateInput.min = today;

    function updateMaxQuantity() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        // Ensure end date is not before start date
        if (endDate < startDate) {
            endDateInput.value = startDate;
        }
        // Set min attribute of end date to be the start date
        endDateInput.min = startDate;

        if (!startDate || !endDate) return;

        axios.get("{{ route('customer.rental.checkAvailability', ['itemID' => $item->itemID]) }}", {
            params: {
                start_date: startDate,
                end_date: endDate
            }
        })
        .then(response => {
            const maxQuantity = response.data.max_quantity;
            quantityInput.max = maxQuantity;
            
            // Update text with a more friendly message
            if (maxQuantity > 0) {
                maxText.innerHTML = `Available for selected dates: <span class="font-semibold text-green-600">${maxQuantity}</span>`;
                quantityInput.disabled = false;
            } else {
                maxText.innerHTML = `<span class="font-semibold text-red-600">This item is fully booked for the selected dates.</span>`;
                quantityInput.disabled = true;
            }

            if(quantityInput.value > maxQuantity){
                quantityInput.value = maxQuantity;
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not check availability. Please try again later.',
                confirmButtonColor: '#d33'
            });
        });
    }

    startDateInput.addEventListener('change', updateMaxQuantity);
    endDateInput.addEventListener('change', updateMaxQuantity);
    
    // Run once on page load to set initial state
    updateMaxQuantity();
});
</script>

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Booking Failed!',
        text: "{{ session('error') }}",
        confirmButtonColor: '#d33',
        confirmButtonText: 'OK'
    });
</script>
@endif
@endpush