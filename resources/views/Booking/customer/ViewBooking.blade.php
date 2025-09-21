@extends('layout.customer')

@section('content')
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

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
        width: 90%;
        margin: 0 auto;
        position: relative;
        top: -40px;
    }
    /* Table Styling */
    .booking-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
    }
    .booking-table thead {
        background: #1c2d6e;
        color: white;
    }
    .booking-table th {
        padding: 14px;
        font-size: 15px;
        text-align: center;
    }
    .booking-table tbody tr {
        background: #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        border-radius: 8px;
    }
    .booking-table td {
        padding: 14px;
        font-size: 15px;
        text-align: center;
        vertical-align: middle;
    }
    .booking-table tbody tr:hover {
        background: #f8f9fc;
        transition: 0.3s;
    }
    /* Badge style */
    .badge {
        font-size: 13px;
        padding: 6px 12px;
        border-radius: 20px;
    }
    /* Icon style */
    .table-icon {
        margin-right: 6px;
        font-size: 1rem;
        color: #1c2d6e;
    }
    /* Expired row */
    .expired-row {
        background: #f5f5f5 !important;
        color: #888 !important;
    }
</style>

<!-- Blue header -->
<div class="profile-section">
    <div class="profile-header">YOUR BOOKING HISTORY</div>
</div>

<div class="profile-card">
    @if($bookings->isEmpty())
        <p class="text-center text-muted">You have no paid bookings yet.</p>
    @else
        <table class="booking-table">
            <thead>
                <tr>
                    <th><i class="bi bi-building table-icon"></i>Field</th>
                    <th><i class="bi bi-calendar-date table-icon"></i>Date</th>
                    <th><i class="bi bi-clock table-icon"></i>Time</th>
                    <th><i class="bi bi-cash-coin table-icon"></i>Price (RM)</th>
                    <th><i class="bi bi-activity table-icon"></i>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr class="{{ $booking->isExpired ? 'expired-row' : '' }}">
                        {{-- Field --}}
                        <td>{{ $booking->field->field_Label ?? 'N/A' }}</td>

                        {{-- Date --}}
                        <td>{{ $booking->formattedDate }}</td>

                        {{-- Time --}}
                        <td>{{ $booking->formattedTime }}</td>

                        {{-- Price --}}
                        <td>{{ $booking->formattedPrice }}</td>

                        {{-- Status --}}
                        <td>
                            @if($booking->isExpired)
                                <span class="badge bg-secondary"><i class="bi bi-clock-history"></i> Expired</span>
                            @else
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Paid</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- SweetAlert2 --}}
@if(session('payment_success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            title: 'Booking Confirmed!',
            text: "{{ session('payment_success') }}",
            icon: 'success',
            confirmButtonText: 'Okay'
        });
    </script>
@endif
@endsection
