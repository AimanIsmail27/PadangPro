@extends('layout.customer')

@section('content')
<div class="container">
    <h2>Your Bookings</h2>

    @if($bookings->isEmpty())
        <p>You have no bookings yet.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Field</th>
                    <th>Slot</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>{{ $booking->bookingID }}</td>

                    {{-- Field (safe check) --}}
                    <td>{{ $booking->field->field_Name ?? 'N/A' }}</td>

                    {{-- Slot (safe check) --}}
                    <td>
                        @if($booking->slot)
                            {{ \Carbon\Carbon::parse($booking->slot->slot_Date)->format('d M Y') }} |
                            {{ \Carbon\Carbon::parse($booking->slot->slot_StartTime)->format('h:i A') }}
                            -
                            {{ \Carbon\Carbon::parse($booking->slot->slot_EndTime)->format('h:i A') }}
                        @else
                            <em>Slot not available</em>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td>{{ ucfirst($booking->booking_Status ?? 'pending') }}</td>

                    {{-- Created At (safe check) --}}
                    <td>
                        @if($booking->created_at)
                            {{ $booking->created_at->format('d M Y, h:i A') }}
                        @else
                            <em>Unknown</em>
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
