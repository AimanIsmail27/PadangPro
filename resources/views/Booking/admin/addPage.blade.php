@extends('layout.admin')

@section('title', 'Create Walk-in Booking - PadangPro Admin')

@section('content')
<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold">ADMIN BOOKING FORM</h1>
    <p class="mt-2 text-yellow-100">Please fill in the details to confirm the walk-in reservation.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative">

    {{-- Slot Details --}}
    <div class="mb-8 p-6 border rounded-xl bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Selected Slot Details</h2>
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

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Please fix the following issues:</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Booking Form --}}
    <form action="{{ route('admin.booking.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="fieldID" value="{{ $field->fieldID }}">
        <input type="hidden" name="slotID" value="{{ $slot->slotID }}">

        <div>
            <label for="booking_Name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="booking_Name" id="booking_Name" value="{{ old('booking_Name') }}"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500" required>
        </div>

        <div>
            <label for="booking_Email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="booking_Email" id="booking_Email" value="{{ old('booking_Email') }}"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="booking_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="booking_PhoneNumber" id="booking_PhoneNumber" value="{{ old('booking_PhoneNumber') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500" required>
            </div>
            <div>
                <label for="booking_BackupNumber" class="block text-sm font-medium text-gray-700">Backup Phone Number</label>
                <input type="text" name="booking_BackupNumber" id="booking_BackupNumber" value="{{ old('booking_BackupNumber') }}"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-start md:items-center gap-4 pt-6 border-t">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-lg shadow-md transition-all transform hover:scale-105">
                Confirm Reservation
            </button>
            <a href="{{ route('admin.booking.manage') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
