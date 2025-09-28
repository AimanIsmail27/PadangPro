@extends('layout.staff')

@section('title', 'Manage Rentals')

@section('content')
<!-- Soft Green Header -->
<div class="bg-green-200 rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-2xl font-bold text-black">Manage Rentals</h2>
    <p class="text-black mt-2">Welcome to the rental management page. Staff can manage rental items here.</p>
</div>

<!-- Buttons Row -->
<div class="flex justify-end mb-6 space-x-2">
    <!-- View Current Rent -->
    <a href="{{ route('staff.rentals.current') }}" 
        class="bg-purple-500 text-white font-semibold px-4 py-2 rounded-lg shadow hover:bg-purple-600 transition">
        View Current Rent
    </a>


    <!-- Pending for Return Approval -->
    <a href="{{ route('staff.rentals.returnApproval') }}" 
       class="bg-yellow-500 text-white font-semibold px-4 py-2 rounded-lg shadow hover:bg-yellow-600 transition">
       Pending for Return Approval
    </a>

    <!-- Add New Item -->
    <a href="{{ route('staff.rental.add') }}" 
       class="bg-green-500 text-white font-semibold px-4 py-2 rounded-lg shadow hover:bg-green-600 transition">
       + Add New Item
    </a>
</div>

<!-- Available Items -->
@if($availableItems->count() > 0)
<div class="mb-8">
    <h3 class="text-xl font-semibold text-green-700 mb-4">Available Items</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($availableItems as $item)
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">
                <h4 class="text-lg font-bold text-gray-800">{{ strtoupper($item->item_Name) }}</h4>
                <p class="text-gray-600 mt-2">{{ strtoupper($item->item_Description) }}</p>
                <div class="flex justify-between items-center mt-4">
                    <span class="font-semibold text-gray-700">Qty: {{ strtoupper($item->item_Quantity) }}</span>
                    <span class="font-semibold text-gray-700">Price: RM {{ strtoupper(number_format($item->item_Price, 2)) }}</span>
                </div>
                <span class="inline-block mt-3 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    {{ strtoupper($item->item_Status) }}
                </span>

                <!-- Action Buttons -->
                <div class="mt-4 flex justify-end space-x-2">
                    <a href="{{ route('staff.rental.edit', ['itemID' => $item->itemID]) }}" 
                       class="bg-blue-500 text-white px-4 py-1 rounded shadow hover:bg-blue-600 transition">
                        Edit
                    </a>
                    <button class="bg-red-500 text-white px-4 py-1 rounded shadow hover:bg-red-600 transition delete-btn"
                        data-id="{{ $item->itemID }}"
                        data-name="{{ strtoupper($item->item_Name) }}"
                        data-desc="{{ strtoupper($item->item_Description) }}"
                        data-qty="{{ strtoupper($item->item_Quantity) }}"
                        data-price="{{ strtoupper(number_format($item->item_Price, 2)) }}"
                        data-status="{{ strtoupper($item->item_Status) }}">
                        Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Unavailable Items -->
@if($unavailableItems->count() > 0)
<div>
    <h3 class="text-xl font-semibold text-red-700 mb-4">Unavailable Items</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($unavailableItems as $item)
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition opacity-60">
                <h4 class="text-lg font-bold text-gray-800">{{ strtoupper($item->item_Name) }}</h4>
                <p class="text-gray-600 mt-2">{{ strtoupper($item->item_Description) }}</p>
                <div class="flex justify-between items-center mt-4">
                    <span class="font-semibold text-gray-700">Qty: {{ strtoupper($item->item_Quantity) }}</span>
                    <span class="font-semibold text-gray-700">Price: RM {{ strtoupper(number_format($item->item_Price, 2)) }}</span>
                </div>
                <span class="inline-block mt-3 px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                    {{ strtoupper($item->item_Status) }}
                </span>

                <!-- Action Buttons -->
                <div class="mt-4 flex justify-end space-x-2">
                    <a href="{{ route('staff.rental.edit', ['itemID' => $item->itemID]) }}" 
                       class="bg-blue-500 text-white px-4 py-1 rounded shadow hover:bg-blue-600 transition">
                        Edit
                    </a>
                    <button class="bg-red-500 text-white px-4 py-1 rounded shadow hover:bg-red-600 transition delete-btn"
                        data-id="{{ $item->itemID }}"
                        data-name="{{ strtoupper($item->item_Name) }}"
                        data-desc="{{ strtoupper($item->item_Description) }}"
                        data-qty="{{ strtoupper($item->item_Quantity) }}"
                        data-price="{{ strtoupper(number_format($item->item_Price, 2)) }}"
                        data-status="{{ strtoupper($item->item_Status) }}">
                        Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@if($availableItems->isEmpty() && $unavailableItems->isEmpty())
    <p class="text-gray-600">No rental items found. Please add a new item.</p>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        let itemID = this.dataset.id;
        let name = this.dataset.name;
        let desc = this.dataset.desc;
        let qty = this.dataset.qty;
        let price = this.dataset.price;
        let status = this.dataset.status;

        Swal.fire({
            title: 'Confirm Deletion',
            html: `
                <p><strong>Name:</strong> ${name}</p>
                <p><strong>Description:</strong> ${desc}</p>
                <p><strong>Quantity:</strong> ${qty}</p>
                <p><strong>Price:</strong> RM ${price}</p>
                <p><strong>Status:</strong> ${status}</p>
                <p>Are you sure you want to delete this item?</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/staff/rental/delete/${itemID}`)
                    .then(response => {
                        Swal.fire(
                            'Deleted!',
                            'Item has been deleted.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            'Error!',
                            'Failed to delete the item.',
                            'error'
                        );
                    });
            }
        });
    });
});
</script>
@endsection
