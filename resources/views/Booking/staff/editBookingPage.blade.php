@extends('layout.staff')

@section('title', 'Edit Booking - PadangPro Staff')

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Edit Booking</h1>
    <p class="mt-2 text-lime-100">Update the contact details for this reservation.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 md:w-4/5 mx-auto -mt-16 relative">

    <div class="mb-8 p-6 border rounded-xl bg-slate-50">
        <h2 class="text-xl font-bold text-gray-800 mb-3">Original Slot Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-gray-700">
            <p><strong>Field:</strong> {{ $booking->field->field_Label }}</p>
            <p><strong>Price:</strong> RM {{ number_format($booking->slot->slot_Price, 2) }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->slot->slot_Date)->format('d M Y') }}</p>
            <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->slot->slot_Time)->format('h:i A') }}</p>
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

    <form action="{{ route('staff.booking.update', $booking->bookingID) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label for="booking_Name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="booking_Name" id="booking_Name" value="{{ old('booking_Name', $booking->booking_Name) }}" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
        </div>

        <div>
            <label for="booking_Email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="booking_Email" id="booking_Email" value="{{ old('booking_Email', $booking->booking_Email) }}" required
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="booking_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="booking_PhoneNumber" id="booking_PhoneNumber" value="{{ old('booking_PhoneNumber', $booking->booking_PhoneNumber) }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
            </div>
            <div>
                <label for="booking_BackupNumber" class="block text-sm font-medium text-gray-700">Backup Phone Number (Optional)</label>
                <input type="text" name="booking_BackupNumber" id="booking_BackupNumber" value="{{ old('booking_BackupNumber', $booking->booking_BackupNumber) }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
            </div>
        </div>
        
        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 transition-all">
                Save Changes
            </button>
            <a href="{{ route('staff.booking.viewAll') }}" class="py-2 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection