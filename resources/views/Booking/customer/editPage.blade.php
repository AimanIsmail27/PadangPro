@extends('layout.customer')

@section('content')
<style>
    .main {
        padding: 30px;
    }
    .profile-section {
        background: #1c2d6e;
        border-radius: 8px;
        height: 120px;
        position: relative;
    }
    .profile-header {
        color: white;
        font-size: 20px;
        font-weight: bold;
        padding: 20px 30px;
    }
    .profile-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 30px;
        width: 80%;
        margin: 0 auto;
        position: relative;
        top: -40px;
    }
    .booking-details p {
        margin: 5px 0;
        font-size: 16px;
    }
    .btn-container {
        margin-top: 20px;
    }
    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        border: none;
        margin-right: 10px;
    }
    .btn-payment {
        background: #28a745;
        color: white;
    }
    .btn-edit {
        background: #007bff;
        color: white;
    }
    .btn-cancel {
        background: #dc3545;
        color: white;
    }
</style>

<div class="main">
    <!-- Blue header -->
    <div class="profile-section">
        <div class="profile-header">CONFIRM YOUR BOOKING</div>
    </div>

    <!-- White card -->
    <div class="profile-card">
        <div class="booking-details">
            <h2 class="font-bold text-lg mb-4">Booking Summary</h2>
            <p><strong>Name:</strong> {{ $booking->booking_Name }}</p>
            <p><strong>Email:</strong> {{ $booking->booking_Email }}</p>
            <p><strong>Phone Number:</strong> {{ $booking->booking_PhoneNumber }}</p>
            <p><strong>Backup Number:</strong> {{ $booking->booking_BackupNumber ?? '-' }}</p>
            <p><strong>Field:</strong> {{ $booking->field->field_Label }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->slot->slot_Date)->format('d M Y') }}</p>
            <p><strong>Time:</strong> {{ $booking->slot->slot_Time }}</p>
            <p><strong>Price:</strong> RM {{ number_format($booking->slot->slot_Price, 2) }}</p>
            <p><strong>Deposit Needed (20%):</strong> RM {{ number_format($booking->slot->slot_Price * 0.2, 2) }}</p>
        </div>

        <!-- Buttons -->
        <div class="btn-container">
            <!-- Payment -->
            <a href="{{ route('payment.create', $booking->bookingID) }}" class="btn btn-payment">
                Continue to Payment
            </a>


            <!-- Edit -->
            <a href="{{ route('booking.edit', $booking->bookingID) }}" class="btn btn-edit">Edit Booking</a>

            <!-- Cancel (with SweetAlert) -->
            <button type="button" class="btn btn-cancel" id="cancelBookingBtn">Cancel Booking</button>
            <form id="cancelBookingForm" action="{{ route('booking.cancel', $booking->bookingID) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('cancelBookingBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: "Your booking will be cancelled and this action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, cancel it'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelBookingForm').submit();
        }
    });
});
</script>
<!-- SweetAlert for Payment Status -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            confirmButtonColor: '#28a745'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Payment Failed',
            text: "{{ session('error') }}",
            confirmButtonColor: '#dc3545'
        });
    @endif

    @if(session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Pending',
            text: "{{ session('warning') }}",
            confirmButtonColor: '#ffc107'
        });
    @endif
</script>

@endsection
