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
        <div class="profile-header">CONFIRM YOUR RENTAL</div>
    </div>

    <!-- White card -->
    <div class="profile-card">
        <div class="booking-details">
            <h2 class="font-bold text-lg mb-4">Rental Summary</h2>
                <p><strong>Name:</strong> {{ $rentalData->rental_Name }}</p>
                <p><strong>Email:</strong> {{ $rentalData->rental_Email ?: '-' }}</p>
                <p><strong>Phone Number:</strong> {{ $rentalData->rental_PhoneNumber }}</p>
                <p><strong>Backup Number:</strong> {{ $rentalData->rental_BackupNumber ?: '-' }}</p>
                <p><strong>Item:</strong> {{ $rentalData->item->item_Name }}</p>
                <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($rentalData->rental_StartDate)->format('d M Y') }}</p>
                <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($rentalData->rental_EndDate)->format('d M Y') }}</p>
                <p><strong>Number of Days:</strong> {{ $days }}</p>
                <p><strong>Quantity:</strong> {{ $rentalData->quantity }}</p>
                <p><strong>Price per Item:</strong> RM {{ number_format($rentalData->item->item_Price, 2) }}</p>
                <p><strong>Total Price:</strong> RM {{ number_format($total, 2) }}</p>
                <p><strong>Price to Pay (Deposit - 20%):</strong> RM {{ number_format($deposit, 2) }}</p>
    </div>

        <!-- Buttons -->
        <div class="btn-container">
            <form action="{{ route('customer.rental.pay', $rentalData->rentalID) }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="total_amount" value="{{ $total }}">
                <button type="submit" class="btn btn-payment">Continue to Payment</button>
            </form>


            <!-- Edit -->
            <a href="{{ route('customer.rental.edit', $rentalData->rentalID) }}" class="btn btn-edit">Edit Rental</a>

            <!-- Cancel (with SweetAlert) -->
            <button type="button" class="btn btn-cancel" id="cancelRentalBtn">Cancel Rental</button>
            <form id="cancelRentalForm" action="{{ route('customer.rental.destroy', $rentalData->rentalID) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('cancelRentalBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'Are you sure?',
        text: "Your rental will be cancelled and this action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, cancel it'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelRentalForm').submit();
        }
    });
});

// Success message if rental was deleted
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: "{{ session('success') }}",
        confirmButtonColor: '#1c2d6e'
    });
@endif
</script>
@endsection
