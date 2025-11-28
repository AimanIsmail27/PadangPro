@extends('layout.customer')

@section('title', 'Rent Item - ' . $item->item_Name)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Rent Item: {{ $item->item_Name }}</h1>
    <div class="flex flex-col md:flex-row md:items-center mt-2 text-indigo-100 gap-4">
        <p>Complete the form below to rent this item.</p>
        
        {{-- Star Rating Badge --}}
        <div class="flex items-center bg-white/20 backdrop-blur-md px-3 py-1 rounded-full w-fit">
            <div class="flex text-yellow-400 text-sm mr-2">
                @for($i=1; $i<=5; $i++)
                    @if($i <= round($averageRating))
                        <i class="bi bi-star-fill"></i>
                    @else 
                        <i class="bi bi-star text-gray-400"></i>
                    @endif
                @endfor
            </div>
            <span class="text-white font-bold text-sm">{{ number_format($averageRating, 1) }} ({{ $totalReviews }})</span>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 md:w-4/5 mx-auto -mt-16 relative space-y-12">

    {{-- Top Section: Item Info & Form --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        {{-- Left Column: Item Details --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 sticky top-24">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Item Details</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Item</p>
                        <p class="text-lg font-semibold text-indigo-700">{{ $item->item_Name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Price (per day)</p>
                        <p class="text-lg font-semibold text-gray-900">RM {{ number_format($item->item_Price, 2) }}</p>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-sm text-gray-500">Description</p>
                        <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $item->item_Description }}</p>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-sm text-gray-500">Available Stock</p>
                        <p class="text-lg font-bold text-green-600" id="static_available_qty">{{ $availableQuantity }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Rental Form --}}
        <div class="md:col-span-2">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Rental Information</h2>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    {{ session('error') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer.rental.process', ['itemID' => $item->itemID]) }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="rental_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="rental_name" id="rental_name" value="{{ old('rental_name', session('full_name')) }}" required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="rental_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="rental_email" id="rental_email" value="{{ old('rental_email') }}"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label for="rental_phone" class="block text-sm font-medium text-gray-700">Contact Number</label>
                        <input type="text" name="rental_phone" id="rental_phone" value="{{ old('rental_phone') }}" required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="rental_backup" class="block text-sm font-medium text-gray-700">Backup Number (Optional)</label>
                        <input type="text" name="rental_backup" id="rental_backup" value="{{ old('rental_backup') }}"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="rental_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="rental_date" id="rental_start_date" value="{{ old('rental_date', $startDate) }}" required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="rental_end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="rental_end_date" id="rental_end_date" value="{{ old('rental_end_date', $endDate) }}" required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="rental_quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="quantity" id="rental_quantity" min="1" max="{{ $availableQuantity }}" value="{{ old('quantity', 1) }}" required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-gray-500 text-sm mt-2" id="max_quantity_text">
                        Available for selected dates: <span class="font-semibold text-green-600">{{ $availableQuantity }}</span>
                    </p>
                </div>

                <div class="flex justify-end gap-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('customer.rental.main') }}" class="px-6 py-2 text-gray-600 hover:text-gray-900 font-medium transition">Cancel</a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow hover:bg-indigo-700 transition font-bold transform hover:-translate-y-0.5">
                        Proceed to Confirmation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <hr class="border-gray-200">

    {{-- =============================================== --}}
    {{-- REVIEWS SECTION --}}
    {{-- =============================================== --}}
    <div id="reviews-section">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="bi bi-chat-quote-fill text-indigo-600"></i> Customer Reviews
        </h3>

        @if($reviews->isEmpty())
            <div class="bg-gray-50 rounded-xl p-8 text-center border border-dashed border-gray-300">
                <p class="text-gray-500 italic">No reviews yet for this item. Be the first to rent and rate it!</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($reviews as $review)
                    <div class="bg-white p-5 rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($review->customer->customer_FullName ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $review->customer->customer_FullName ?? 'Anonymous' }}</p>
                                    
                                    {{-- TIMEZONE FIX IS HERE --}}
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($review->review_Time, 'Asia/Kuala_Lumpur')->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex text-yellow-400 text-xs">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi bi-star-fill {{ $i <= $review->rating_Score ? '' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm italic ml-11">"{{ $review->review_Given }}"</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reviews->fragment('reviews-section')->links() }}
            </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('rental_start_date');
    const endDateInput = document.getElementById('rental_end_date');
    const quantityInput = document.getElementById('rental_quantity');
    const maxText = document.getElementById('max_quantity_text');
    const staticDisplay = document.getElementById('static_available_qty');
    
    const today = new Date().toISOString().split('T')[0];

    if (!startDateInput.value) { startDateInput.value = today; }
    startDateInput.min = today;
    
    if (!endDateInput.value) { endDateInput.value = today; }
    endDateInput.min = today;

    function updateMaxQuantity() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;

        if (endDate < startDate) {
            endDateInput.value = startDate;
        }
        endDateInput.min = startDate;

        if (!startDate || !endDate) return;

        axios.get("{{ route('customer.rental.checkAvailability', ['itemID' => $item->itemID]) }}", {
            params: { start_date: startDate, end_date: endDate }
        })
        .then(response => {
            const maxQuantity = response.data.max_quantity;
            quantityInput.max = maxQuantity;
            
            if (maxQuantity > 0) {
                maxText.innerHTML = `Available for selected dates: <span class="font-semibold text-green-600">${maxQuantity}</span>`;
                if(staticDisplay) staticDisplay.innerText = maxQuantity;
                quantityInput.disabled = false;
            } else {
                maxText.innerHTML = `<span class="font-semibold text-red-600">Fully booked for selected dates.</span>`;
                if(staticDisplay) staticDisplay.innerText = 0;
                quantityInput.disabled = true;
            }

            if(parseInt(quantityInput.value) > maxQuantity){
                quantityInput.value = maxQuantity;
            }
        })
        .catch(error => {
            console.error(error);
        });
    }

    startDateInput.addEventListener('change', updateMaxQuantity);
    endDateInput.addEventListener('change', updateMaxQuantity);
    
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