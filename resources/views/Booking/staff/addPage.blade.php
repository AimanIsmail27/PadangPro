@extends('layout.staff')

@section('title', 'Create Walk-in Booking - PadangPro Staff')

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Staff Booking Form</h1>
    <p class="mt-2 text-lime-100">Create a new walk-in or internal booking.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 md:w-4/5 mx-auto -mt-16 relative">

    <div class="mb-8 p-6 border rounded-xl bg-slate-50">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Slot Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
            <div>
                <p class="text-sm text-gray-500">Field</p>
                <p class="text-lg font-semibold text-gray-900">{{ $field->field_Label }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Price</p>
                <p class="text-lg font-semibold text-gray-900">RM {{ number_format($slot->slot_Price, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Date</p>
                <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($slot->slot_Date)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Time</p>
                <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($slot->slot_Time)->format('h:i A') }}</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="text-xl font-bold text-gray-800 mb-6">Customer Information</h2>

    <form action="{{ route('staff.booking.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <input type="hidden" name="fieldID" value="{{ $field->fieldID }}">
        <input type="hidden" name="slotID" value="{{ $slot->slotID }}">
        {{-- No userID is needed, controller will get it from session --}}

        <div>
            <label for="booking_Name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="booking_Name" id="booking_Name" value="{{ old('booking_Name') }}"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500" required>
        </div>

        <div>
            <label for="booking_Email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="booking_Email" id="booking_Email" value="{{ old('booking_Email') }}"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="booking_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="booking_PhoneNumber" id="booking_PhoneNumber" value="{{ old('booking_PhoneNumber') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500" required>
            </div>
            <div>
                <label for="booking_BackupNumber" class="block text-sm font-medium text-gray-700">Backup Phone Number</label>
                <input type="text" name="booking_BackupNumber" id="booking_BackupNumber" value="{{ old('booking_BackupNumber') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
            </div>
        </div>

        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" 
                    class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 transition-all">
                Confirm Reservation
            </button>
            <a href="{{ route('staff.booking.manage') }}" 
               class="py-2 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for session errors (e.g., slot taken) --}}
@if(session('error'))
    <script>
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    </script>
@endif
@endpush