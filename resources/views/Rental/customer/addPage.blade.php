@extends('layout.customer')

@section('title', 'Rent Item')

@section('content')
<!-- Blue Header -->
<div class="bg-[#1E2A78] rounded-lg h-[120px] relative shadow-md">
    <div class="text-white font-bold text-xl px-8 py-6">
        RENT ITEM
        
    </div>
</div>

<!-- White Form Card (90% width, centered, overlapping like booking history) -->
<div class="bg-white rounded-xl shadow-md p-6 w-[90%] mx-auto relative -mt-10 z-10">
    <h3 class="text-lg font-semibold mb-4">{{ strtoupper($item->item_Name) }}</h3>
    <p class="text-gray-600 mb-2">{{ strtoupper($item->item_Description) }}</p>
    <p class="text-gray-700 font-semibold mb-4">
        Price: RM {{ number_format($item->item_Price, 2) }}
    </p>

    <form action="{{ route('customer.rental.process', ['itemID' => $item->itemID]) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Full Name</label>
                <input type="text" name="rental_name" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input type="email" name="rental_email" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Phone Number</label>
                <input type="text" name="rental_phone" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Backup Number</label>
                <input type="text" name="rental_backup" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-semibold mb-1">Start Date</label>
                <input type="date" name="rental_date" id="rental_start_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">End Date</label>
                <input type="date" name="rental_end_date" id="rental_end_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>

            <div>
                <label class="block font-semibold mb-1">Quantity (Maximum available quantity depends on the chosen rental date)</label>
                <input type="number" name="quantity" id="rental_quantity" min="1" max="{{ $availableQuantity }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <p class="text-gray-500 text-sm mt-1" id="max_quantity_text">
                    You can select up to {{ $availableQuantity }} items for the selected dates.
                </p>
            </div>
        </div>

        <div class="mt-6 text-center">
            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition">
                Continue
            </button>
        </div>
    </form>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('rental_start_date');
    const endDateInput = document.getElementById('rental_end_date');
    const quantityInput = document.getElementById('rental_quantity');
    const maxText = document.getElementById('max_quantity_text');

    function updateMaxQuantity() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

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
            maxText.textContent = `You can select up to ${maxQuantity} items for the selected dates.`;

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
@endsection
