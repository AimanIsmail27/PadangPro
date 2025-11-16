@extends('layout.staff')

@section('title', 'Current Rental Record')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Current Rental Record</h1>
    <p class="mt-2 text-lime-100">View all upcoming and currently active rentals.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    @if($rentals->isEmpty())
        <div class="text-center py-12">
            <i class="bi bi-box-seam text-6xl text-gray-300"></i>
            <h3 class="mt-4 text-2xl font-bold text-gray-700">No Current Rentals</h3>
            <p class="mt-2 text-gray-500">There are no current or upcoming rentals at this time.</p>
        </div>
    @else
        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-zinc-800">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">
                            Rental ID
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">
                            Item
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">
                            Start Date
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">
                            End Date
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-lime-300 uppercase tracking-wider">
                            Quantity
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-lime-300 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rentals as $rental)
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $rental->rentalID }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $rental->item->item_Name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                {{ $rental->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                    {{ strtolower($rental->rental_Status) == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($rental->rental_Status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection