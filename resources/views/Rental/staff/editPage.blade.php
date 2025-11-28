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

    {{-- Added enctype="multipart/form-data" for file upload --}}
    <form id="editItemForm" method="POST" action="{{ route('staff.rental.update', $item->itemID) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT') 

        <div>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="item_Status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="item_Status" name="item_Status" 
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
                    <option value="Available" {{ old('item_Status', $item->item_Status) === 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Unavailable" {{ old('item_Status', $item->item_Status) === 'Unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>

            {{-- NEW: Image Upload Section --}}
            <div>
                <label for="item_Image" class="block text-sm font-medium text-gray-700">Item Image (Optional)</label>
                
                {{-- Show current image preview if exists --}}
                @if($item->item_Image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $item->item_Image) }}" alt="Current Image" class="h-20 w-20 object-cover rounded border border-gray-200">
                        <p class="text-xs text-gray-500 mt-1">Current Image</p>
                    </div>
                @endif

                <input type="file" name="item_Image" id="item_Image" accept="image/*"
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-lime-50 file:text-lime-700
                              hover:file:bg-lime-100 cursor-pointer border border-gray-300 rounded-md">
                <p class="text-xs text-gray-500 mt-1">Upload new image to replace current one.</p>
            </div>
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
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('editItemForm').addEventListener('submit', function(e) {
    e.preventDefault(); // prevent normal form submission

    let formData = new FormData(this);
    // FormData automatically handles file inputs correctly

    axios.post("{{ route('staff.rental.update', $item->itemID) }}", formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
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
        console.error(error);
        let errorMsg = 'Something went wrong!';
        
        if (error.response && error.response.data && error.response.data.errors) {
            let errors = error.response.data.errors;
            errorMsg = '';
            for (let key in errors) {
                errorMsg += errors[key].join(', ') + '\n';
            }
        } else if (error.response && error.response.data && error.response.data.message) {
             errorMsg = error.response.data.message;
        }

        Swal.fire({
            title: 'Error!',
            text: errorMsg,
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    });
});
</script>
@endpush