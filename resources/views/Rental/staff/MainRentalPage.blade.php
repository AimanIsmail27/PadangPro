@extends('layout.staff')

@section('title', 'Manage Rentals')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Manage Rentals</h1>
    <p class="mt-2 text-lime-100">Welcome to the rental management page. Staff can manage rental items here.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    {{-- Action Buttons --}}
    
    <div class="flex flex-col md:flex-row justify-end items-stretch gap-4 mb-8 pb-6 border-b border-gray-200">
        <a href="{{ route('staff.rentals.current') }}" 
        class="w-full md:w-auto text-center bg-zinc-700 text-white font-semibold px-4 py-2 rounded-lg shadow hover:bg-zinc-800 transition transform hover:scale-105">
        <i class="bi bi-box-arrow-up-right mr-2"></i> View Current Rent
        </a>

        <a href="{{ route('staff.rentals.returnApproval') }}" 
        class="w-full md:w-auto text-center bg-yellow-500 text-white font-semibold px-4 py-2 rounded-lg shadow hover:bg-yellow-600 transition transform hover:scale-105">
        <i class="bi bi-clock-history mr-2"></i> Pending Returns
        </a>

        <a href="{{ route('staff.rental.add') }}" 
        class="w-full md:w-auto text-center bg-lime-600 text-white font-semibold px-4 py-2 rounded-lg shadow hover:bg-lime-700 transition transform hover:scale-105">
        <i class="bi bi-plus-circle-fill mr-2"></i> Add New Item
        </a>
    </div>


    {{-- Available Items --}}
    @if($availableItems->count() > 0)
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-green-700 mb-4">Available Items</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableItems as $item)
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col h-full">
                    
                    {{-- Item Image --}}
                    <div class="relative h-56 bg-gray-200 overflow-hidden">
                        <img src="{{ $item->item_Image ? asset('storage/' . $item->item_Image) : 'https://images.unsplash.com/photo-1599058945522-28d584b6f0ff?q=80&w=800&auto=format&fit=crop' }}" 
                             alt="{{ $item->item_Name }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                        <div class="absolute top-3 left-3 z-10">
                            <span class="px-2.5 py-1 bg-emerald-500/90 text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm flex items-center gap-1">
                                <i class="bi bi-check-circle-fill"></i> Available
                            </span>
                        </div>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-5 flex flex-col flex-grow">
                        <h4 class="text-lg font-bold text-gray-800">{{ strtoupper($item->item_Name) }}</h4>
                        <p class="text-gray-500 text-sm mt-2 line-clamp-2">{{ strtoupper($item->item_Description) }}</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="font-semibold text-gray-700">Qty: {{ $item->item_Quantity }}</span>
                            <span class="font-semibold text-gray-700">Price: RM {{ number_format($item->item_Price, 2) }}</span>
                        </div>
                        <span class="inline-block mt-3 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            {{ strtoupper($item->item_Status) }}
                        </span>

                        {{-- Actions --}}
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
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Unavailable Items --}}
    @if($unavailableItems->count() > 0)
    <div>
        <h3 class="text-xl font-semibold text-red-700 mb-4">Unavailable Items</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($unavailableItems as $item)
                <div class="group bg-gray-100 rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col h-full">
                    
                    {{-- Item Image --}}
                    <div class="relative h-56 bg-gray-200 overflow-hidden">
                        <img src="{{ $item->item_Image ? asset('storage/' . $item->item_Image) : 'https://images.unsplash.com/photo-1599058945522-28d584b6f0ff?q=80&w=800&auto=format&fit=crop' }}" 
                             alt="{{ $item->item_Name }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                        <div class="absolute top-3 left-3 z-10">
                            <span class="px-2.5 py-1 bg-red-500/90 text-white rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm flex items-center gap-1">
                                <i class="bi bi-x-circle-fill"></i> Unavailable
                            </span>
                        </div>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-5 flex flex-col flex-grow">
                        <h4 class="text-lg font-bold text-gray-800">{{ strtoupper($item->item_Name) }}</h4>
                        <p class="text-gray-500 text-sm mt-2 line-clamp-2">{{ strtoupper($item->item_Description) }}</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="font-semibold text-gray-700">Qty: {{ $item->item_Quantity }}</span>
                            <span class="font-semibold text-gray-700">Price: RM {{ number_format($item->item_Price, 2) }}</span>
                        </div>
                        <span class="inline-block mt-3 px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                            {{ strtoupper($item->item_Status) }}
                        </span>

                        {{-- Actions --}}
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
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($availableItems->isEmpty() && $unavailableItems->isEmpty())
        <p class="text-gray-600">No rental items found. Please add a new item.</p>
    @endif
</div>
@endsection

@push('scripts')
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
                <div class="text-left space-y-2">
                    <p><strong>Name:</strong> ${name}</p>
                    <p><strong>Description:</strong> ${desc}</p>
                    <p><strong>Quantity:</strong> ${qty}</p>
                    <p><strong>Price:</strong> RM ${price}</p>
                    <p><strong>Status:</strong> ${status}</p>
                    <hr class="my-3">
                    <p class="font-semibold">Are you sure you want to delete this item?</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios({
                    method: 'delete',
                    url: "{{ url('staff/rental/delete') }}/" + itemID,
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                })
                .then(response => {
                    Swal.fire('Deleted!','Item has been deleted.','success').then(() => location.reload());
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    Swal.fire('Error!','Failed to delete the item.','error');
                });
            }
        });
    });
});

@if(session('success'))
    Swal.fire({icon: 'success', title: 'Success', text: '{{ session('success') }}', confirmButtonColor: '#166534', confirmButtonText: 'OK'});
@endif
@if(session('error'))
    Swal.fire({icon: 'error', title: 'Error!', text: '{{ session('error') }}', confirmButtonColor: '#d33', confirmButtonText: 'OK'});
@endif
</script>
@endpush
