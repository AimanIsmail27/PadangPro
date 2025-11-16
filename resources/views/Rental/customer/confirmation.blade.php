@extends('layout.customer')

@section('title', 'Confirm Your Rental')

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Confirm Your Rental</h1>
    <p class="mt-2 text-indigo-100">Please review your rental details before proceeding to payment.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
        
        <div class="space-y-4">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Your Information</h2>
            <div>
                <label class="block text-sm font-medium text-gray-500">Full Name</label>
                <p class="text-lg font-semibold text-gray-900">{{ $rentalData->rental_Name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Email</label>
                <p class="text-lg font-semibold text-gray-900">{{ $rentalData->rental_Email ?: '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                <p class="text-lg font-semibold text-gray-900">{{ $rentalData->rental_PhoneNumber }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Backup Number</label>
                <p class="text-lg font-semibold text-gray-900">{{ $rentalData->rental_BackupNumber ?: '-' }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Rental Details</h2>
            <div>
                <label class="block text-sm font-medium text-gray-500">Item</label>
                <p class="text-lg font-semibold text-gray-900">{{ $rentalData->item->item_Name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Rental Period</label>
                <p class="text-lg font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($rentalData->rental_StartDate)->format('d M Y') }}
                    to
                    {{ \Carbon\Carbon::parse($rentalData->rental_EndDate)->format('d M Y') }}
                    <span class="text-sm font-normal text-gray-500">({{ $days }} {{ \Illuminate\Support\Str::plural('day', $days) }})</span>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Quantity</label>
                <p class="text-lg font-semibold text-gray-900">{{ $rentalData->quantity }}</p>
            </div>
            
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-2 mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Total Price:</span>
                    <span class="text-lg font-bold text-indigo-600">RM {{ number_format($total, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Price to Pay (Deposit - 20%):</span>
                    <span class="text-lg font-bold text-green-600">RM {{ number_format($deposit, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 pt-6 border-t flex flex-wrap items-center gap-4">
        <form action="{{ route('customer.rental.pay', $rentalData->rentalID) }}" method="POST" style="display:inline;">
            @csrf
            {{-- CRITICAL FIX: Changed 'total_amount' from $total to $deposit to match what the user is told they are paying --}}
            <input type="hidden" name="total_amount" value="{{ $deposit }}">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-all transform hover:scale-105">
                <i class="bi bi-credit-card-fill mr-2"></i>
                Continue to Payment
            </button>
        </form>

        <a href="{{ route('customer.rental.edit', $rentalData->rentalID) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-all">
            <i class="bi bi-pencil-fill mr-2"></i>
            Edit Rental
        </a>

        <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-all" id="cancelRentalBtn">
            <i class="bi bi-trash-fill mr-2"></i>
            Cancel Rental
        </button>
        <form id="cancelRentalForm" action="{{ route('customer.rental.destroy', $rentalData->rentalID) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('cancelRentalBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: "Your rental will be cancelled and this action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelRentalForm').submit();
        }
    });
});
</script>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            confirmButtonColor: '#4f46e5'
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Action Failed',
            text: "{{ session('error') }}",
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endpush