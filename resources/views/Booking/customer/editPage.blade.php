@extends('layout.customer')

@section('title', 'Confirm Your Booking')

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Confirm Your Booking</h1>
    <p class="mt-2 text-indigo-100">Please review your details before proceeding to payment.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
        
        <div class="space-y-4">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Your Information</h2>
            <div>
                <label class="block text-sm font-medium text-gray-500">Full Name</label>
                <p class="text-lg font-semibold text-gray-900">{{ $booking->booking_Name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Email</label>
                <p class="text-lg font-semibold text-gray-900">{{ $booking->booking_Email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                <p class="text-lg font-semibold text-gray-900">{{ $booking->booking_PhoneNumber }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Backup Number</label>
                <p class="text-lg font-semibold text-gray-900">{{ $booking->booking_BackupNumber ?? '-' }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Booking Details</h2>
            <div>
                <label class="block text-sm font-medium text-gray-500">Field</label>
                @php
                    $fieldType = (str_contains($booking->field->field_Size, 'MINI SIZED')) 
                        ? '(Mini Field)' 
                        : '(Standard Field)';
                @endphp
                <p class="text-lg font-semibold text-gray-900">
                    {{ $booking->field->field_Label }} {{ $fieldType }}
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Date</label>
                <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->slot->slot_Date)->format('d M Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Time</label>
                <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->slot->slot_Time)->format('h:i A') }}</p>
            </div>
            
            <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-2 mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Total Price:</span>
                    <span class="text-lg font-bold text-indigo-600">RM {{ number_format($booking->slot->slot_Price, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Deposit (20%):</span>
                    <span class="text-lg font-bold text-green-600">RM {{ number_format($booking->slot->slot_Price * 0.2, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION BUTTONS FIX: Stack vertically on small screens (w-full) and center content --}}
    <div class="mt-8 pt-6 border-t flex flex-col sm:flex-row flex-wrap items-center gap-3 md:gap-4">
        
        {{-- Primary Action --}}
        <a href="{{ route('payment.create', $booking->bookingID) }}" 
           class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-all flex items-center justify-center">
            <i class="bi bi-credit-card-fill mr-2"></i>
            Continue to Payment
        </a>

        {{-- Secondary Action --}}
        <a href="{{ route('booking.edit', $booking->bookingID) }}" 
           class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-all flex items-center justify-center">
            <i class="bi bi-pencil-fill mr-2"></i>
            Edit Details
        </a>

        {{-- Danger Action --}}
        <button type="button" 
                class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-all flex items-center justify-center" 
                id="cancelBookingBtn">
            <i class="bi bi-trash-fill mr-2"></i>
            Cancel Booking
        </button>
        
        <form id="cancelBookingForm" action="{{ route('booking.cancel', $booking->bookingID) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('cancelBookingBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: "Your booking will be cancelled and this action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelBookingForm').submit();
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
            title: 'Payment Failed',
            text: "{{ session('error') }}",
            confirmButtonColor: '#d33'
        });
    @endif

    @if(session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Pending',
            text: "{{ session('warning') }}",
            confirmButtonColor: '#f59e0b'
        });
    @endif
</script>
@endpush