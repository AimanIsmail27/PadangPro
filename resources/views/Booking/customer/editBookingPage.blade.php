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
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        font-weight: bold;
        display: block;
        margin-bottom: 6px;
    }
    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }
    .save-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
    }
    .cancel-btn {
        background: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        margin-left: 10px;
        font-size: 16px;
    }
</style>

<div class="main">
    <!-- Blue header -->
    <div class="profile-section">
        <div class="profile-header">EDIT BOOKING</div>
    </div>

    <!-- White card with details + form -->
    <div class="profile-card">
        <!-- Slot & Field Details -->
        <div class="mb-6">
            <h2 class="text-lg font-bold mb-4">Slot Details</h2>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->slot->slot_Date)->format('d M Y') }}</p>
            <p><strong>Time:</strong> {{ $booking->slot->slot_Time ?? '-' }}</p>
            <p><strong>Field:</strong> {{ $booking->field->field_Label }}</p>
            <p><strong>Price:</strong> RM {{ number_format($booking->slot->slot_Price, 2) }}</p>
        </div>

        <!-- Edit Booking Form -->
        <form action="{{ route('booking.update', $booking->bookingID) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" name="fullName" id="fullName" value="{{ old('fullName', $booking->booking_Name) }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $booking->booking_Email) }}" required>
            </div>

            <div class="form-group">
                <label for="phoneNumber">Phone Number</label>
                <input type="text" name="phoneNumber" id="phoneNumber" value="{{ old('phoneNumber', $booking->booking_PhoneNumber) }}" required>
            </div>

            <div class="form-group">
                <label for="backupPhone">Backup Phone Number</label>
                <input type="text" name="backupPhone" id="backupPhone" value="{{ old('backupPhone', $booking->booking_BackupNumber) }}">
            </div>

            <button type="submit" class="save-btn">Save Changes</button>
            <a href="{{ route('customer.dashboard') }}" class="cancel-btn">Cancel Booking</a>
        </form>
    </div>
</div>
@endsection
