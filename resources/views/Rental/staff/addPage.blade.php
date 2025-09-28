@extends('layout.staff')

@section('title', 'Add Rental Item')

@section('content')
<div class="bg-green-200 rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-2xl font-bold text-black">Add New Rental Item</h2>
    <p class="text-black mt-2">Fill out the form below to add a new item to the rental list.</p>
</div>

<div class="bg-white rounded-xl shadow-md p-6">
    <form id="addItemForm" method="POST">
        @csrf
        <!-- Item Name -->
        <div class="mb-4">
            <label for="item_Name" class="block text-sm font-medium text-gray-700">Item Name</label>
            <input type="text" id="item_Name" name="item_Name" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
        </div>

        <!-- Quantity -->
        <div class="mb-4">
            <label for="item_Quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
            <input type="number" id="item_Quantity" name="item_Quantity" min="1" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
        </div>

        <!-- Price -->
        <div class="mb-4">
            <label for="item_Price" class="block text-sm font-medium text-gray-700">Price (RM)</label>
            <input type="number" id="item_Price" name="item_Price" step="0.01" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500" required>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label for="item_Description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="item_Description" name="item_Description" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
        </div>

        <!-- Status -->
        <div class="mb-4">
            <label for="item_Status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="item_Status" name="item_Status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="Available" selected>Available</option>
                <option value="Unavailable">Unavailable</option>
            </select>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" class="bg-green-500 text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-green-600 transition">Save Item</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.getElementById('addItemForm').addEventListener('submit', function(e) {
    e.preventDefault(); // prevent normal form submission

    let formData = new FormData(this);

    axios.post("{{ route('staff.rental.store') }}", formData)
        .then(function(response) {
            Swal.fire({
                title: 'Success!',
                text: "Item registered successfully.",
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
