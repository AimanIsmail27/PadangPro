@extends('layout.staff')

@section('title', 'Edit Rental Item')

@section('content')
<div class="bg-green-200 rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-2xl font-bold text-black">Edit Rental Item</h2>
    <p class="text-black mt-2">Update the details of this rental item below.</p>
</div>

<div class="bg-white rounded-xl shadow-md p-6">
    <form id="editItemForm" method="POST">
        @csrf
        @method('PUT') <!-- Required for Laravel to recognize PUT request -->

        <!-- Item Name -->
        <div class="mb-4">
            <label for="item_Name" class="block text-sm font-medium text-gray-700">Item Name</label>
            <input type="text" id="item_Name" name="item_Name" value="{{ $item->item_Name }}" 
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
        </div>

        <!-- Quantity -->
        <div class="mb-4">
            <label for="item_Quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
            <input type="number" id="item_Quantity" name="item_Quantity" min="1" value="{{ $item->item_Quantity }}"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
        </div>

        <!-- Price -->
        <div class="mb-4">
            <label for="item_Price" class="block text-sm font-medium text-gray-700">Price (RM)</label>
            <input type="number" id="item_Price" name="item_Price" step="0.01" min="0" value="{{ $item->item_Price }}"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label for="item_Description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="item_Description" name="item_Description" rows="4" 
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">{{ $item->item_Description }}</textarea>
        </div>

        <!-- Status -->
        <div class="mb-4">
            <label for="item_Status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="item_Status" name="item_Status" 
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="Available" {{ $item->item_Status === 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Unavailable" {{ $item->item_Status === 'Unavailable' ? 'selected' : '' }}>Unavailable</option>
            </select>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" class="bg-green-500 text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-green-600 transition">Update Item</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.getElementById('editItemForm').addEventListener('submit', function(e) {
    e.preventDefault(); // prevent normal form submission

    let formData = new FormData(this);

    axios.post("{{ route('staff.rental.update', $item->itemID) }}", formData)
        .then(function(response) {
            Swal.fire({
                title: 'Success!',
                text: "Item updated successfully.",
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('staff.rental.main') }}";
                }
            });
        })
        .catch(function(error) {
            let errors = error.response.data.errors || {};
            let errorMsg = '';
            for (let key in errors) {
                errorMsg += errors[key].join(', ') + '\n';
            }
            Swal.fire({
                title: 'Error!',
                text: errorMsg || 'Something went wrong!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
});
</script>
@endsection
