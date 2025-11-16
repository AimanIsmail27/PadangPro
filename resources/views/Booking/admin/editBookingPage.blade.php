@extends('layout.admin')

@section('title', 'Edit Booking - PadangPro Admin')

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold">Edit Booking</h1>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 md:p-10 max-w-3xl mx-auto -mt-16 relative">

    <!-- Slot Details -->
    <div class="mb-8 p-4 border rounded-lg bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800 mb-3">Original Slot Details</h2>
        <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-gray-700">
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->slot->slot_Date)->format('d M Y') }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->slot->slot_Time)->format('h:i A') }}</p>
            <p><strong>Field:</strong> {{ $booking->field->field_Label }}</p>
        </div>
    </div>

    <form action="{{ route('admin.booking.update', $booking->bookingID) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            {{-- LABEL UPDATED --}}
            <label for="booking_Name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="booking_Name" id="booking_Name" value="{{ old('booking_Name', $booking->booking_Name) }}" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
        </div>

        <div>
            {{-- LABEL UPDATED --}}
            <label for="booking_Email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="booking_Email" id="booking_Email" value="{{ old('booking_Email', $booking->booking_Email) }}" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                {{-- LABEL UPDATED --}}
                <label for="booking_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="booking_PhoneNumber" id="booking_PhoneNumber" value="{{ old('booking_PhoneNumber', $booking->booking_PhoneNumber) }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                {{-- LABEL UPDATED --}}
                <label for="booking_BackupNumber" class="block text-sm font-medium text-gray-700">Backup Phone Number (Optional)</label>
                <input type="text" name="booking_BackupNumber" id="booking_BackupNumber" value="{{ old('booking_BackupNumber', $booking->booking_BackupNumber) }}"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>
        
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                Save Changes
            </button>
            <a href="{{ route('admin.booking.viewAll') }}" class="py-2 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection