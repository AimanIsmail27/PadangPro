@extends('layout.admin')

@section('title', 'Edit Booking - PadangPro Admin')

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold">Edit Booking</h1>
    <p class="mt-2 text-yellow-100">Update the booking details below and save changes.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 max-w-3xl mx-auto -mt-16 relative">

    {{-- Slot Details --}}
    <div class="mb-8 p-6 border rounded-xl bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Original Slot Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
            <div>
                <p class="text-sm text-gray-500">Date</p>
                <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->slot->slot_Date)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Time</p>
                <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->slot->slot_Time)->format('h:i A') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Field</p>
                <p class="text-lg font-semibold text-gray-900">{{ $booking->field->field_Label }}</p>
            </div>
        </div>
    </div>

    {{-- Booking Form --}}
    <form action="{{ route('admin.booking.update', $booking->bookingID) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="booking_Name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="booking_Name" id="booking_Name" value="{{ old('booking_Name', $booking->booking_Name) }}" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
        </div>

        <div>
            <label for="booking_Email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="booking_Email" id="booking_Email" value="{{ old('booking_Email', $booking->booking_Email) }}" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="booking_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="booking_PhoneNumber" id="booking_PhoneNumber" value="{{ old('booking_PhoneNumber', $booking->booking_PhoneNumber) }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label for="booking_BackupNumber" class="block text-sm font-medium text-gray-700">Backup Phone Number</label>
                <input type="text" name="booking_BackupNumber" id="booking_BackupNumber" value="{{ old('booking_BackupNumber', $booking->booking_BackupNumber) }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-start md:items-center gap-4 pt-6 border-t">
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
