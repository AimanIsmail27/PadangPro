@extends('layout.staff')

@section('title', 'Edit Rental Item')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Edit Rental Item</h1>
    <p class="mt-2 text-lime-100">Update the details of this rental item below.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 md:w-4/5 mx-auto -mt-16 relative">

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Update Item Details</h2>

    <form id="editItemForm" method="POST" action="{{ route('staff.rental.update', $item->itemID) }}" class="space-y-6">
        @csrf
        @method('PUT') <div>
            <label for="item_Name" class="block text-sm font-medium text-gray-700">Item Name</label>
            <input type="text" id="item_Name" name="item_Name" value="{{ old('item_Name', $item->item_Name) }}" 
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="item_Quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                <input type="number" id="item_Quantity" name="item_Quantity" min="1" value="{{ old('item_Quantity', $item->item_Quantity) }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500" required>
            </div>

            <div>
                <label for="item_Price" class="block text-sm font-medium text-gray-700">Price (RM)</label>
                <input type="number" id="item_Price" name="item_Price" step="0.01" min="0" value="{{ old('item_Price', $item->item_Price) }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500" required>
            </div>
        </div>

        <div>
            <label for="item_Description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="item_Description" name="item_Description" rows="4" 
                      class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">{{ old('item_Description', $item->item_Description) }}</textarea>
        </div>

        <div>
            <label for="item_Status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="item_Status" name="item_Status" 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
                <option value="Available" {{ old('item_Status', $item->item_Status) === 'Available' ? 'selected' : '' }}>Available</option>
                <option value="Unavailable" {{ old('item_Status', $item->item_Status) === 'Unavailable' ? 'selected' : '' }}>Unavailable</option>
            </select>
        </div>

        <div class="flex justify-end gap-4 pt-6 border-t">
            <a href="{{ route('staff.rental.main') }}" class="py-2 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                Cancel
            </a>
            <button type="submit" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 transition-all">
                Update Item
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
{{-- SweetAlert2 is already in the main layout --}}
<script>
document.getElementById('editItemForm').addEventListener('submit', function(e) {
    e.preventDefault(); // prevent normal form submission

    let formData = new FormData(this);
    // Manually add the _method since FormData doesn't pick it up
    formData.append('_method', 'PUT'); 

    axios.post("{{ route('staff.rental.update', $item->itemID) }}", formData)
        .then(function(response) {
            Swal.fire({
                title: 'Success!',
                text: "Item updated successfully.",
                icon: 'success',
                confirmButtonColor: '#166534', // green-800
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
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        });
});
</script>
@endpush