@extends('layout.staff')

@section('content')

<!-- Soft Green Header -->
<div class="bg-green-200 rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-2xl font-bold text-black">Current Rental Record</h2>
    <p class="text-black mt-2">Welcome to the rental management page. Staff can manage rental items here.</p>
</div>

<div class="container mx-auto p-6">

    <div class="overflow-x-auto shadow-lg rounded-lg">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-green-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="py-3 px-4 text-left">Rental ID</th>
                    <th class="py-3 px-4 text-left">Item</th>
                    <th class="py-3 px-4 text-left">Start Date</th>
                    <th class="py-3 px-4 text-left">End Date</th>
                    <th class="py-3 px-4 text-center">Quantity</th>
                    <th class="py-3 px-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($rentals as $rental)
                <tr class="border-b hover:bg-green-50 transition">
                    <td class="py-3 px-4 font-medium">{{ $rental->rentalID }}</td>
                    <td class="py-3 px-4">{{ $rental->item->item_Name ?? 'N/A' }}</td>
                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M Y') }}</td>
                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M Y') }}</td>
                    <td class="py-3 px-4 text-center">{{ $rental->quantity }}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            {{ strtolower($rental->rental_Status) == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($rental->rental_Status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500 italic">
                        No current or upcoming rentals.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
