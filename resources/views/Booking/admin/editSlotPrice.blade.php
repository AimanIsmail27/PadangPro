@extends('layout.admin')

@section('content')
<div class="container py-4">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">Edit Slot Price</h3>

        <a href="{{ route('admin.booking.viewAll') }}" class="btn btn-outline-secondary">
            ← Back to Bookings
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="card-title mb-3">Slot Details</h5>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="text-muted small">Field</div>
                        <div class="fw-semibold">
                            {{ $slot->field->field_Label ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-light rounded">
                        <div class="text-muted small">Date</div>
                        <div class="fw-semibold">
                            {{ \Carbon\Carbon::parse($slot->slot_Date)->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-light rounded">
                        <div class="text-muted small">Time</div>
                        <div class="fw-semibold">
                            {{ \Carbon\Carbon::parse($slot->slot_Time)->format('h:i A') }}
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.booking.slot.price.update', $slot->slotID) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Current Price</label>
                    <input type="text" class="form-control" value="RM {{ number_format($slot->slot_Price ?? 0, 2) }}" disabled>
                </div>

                <div class="mb-3">
                    <label for="slot_Price" class="form-label">New Price (RM)</label>
                    <input
                        type="number"
                        step="0.01"
                        min="1"
                        name="slot_Price"
                        id="slot_Price"
                        class="form-control"
                        value="{{ old('slot_Price', $slot->slot_Price) }}"
                        required
                    >
                    <div class="form-text">
                        This will update the price for this slot only (if it is not booked).
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Save Price
                    </button>

                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
