@extends('layout.customer')

@section('title', 'My Rental History')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">My Rental History</h1>
    <p class="mt-2 text-indigo-100">A record of all your equipment rentals.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    @if($rentals->isEmpty())
        <div class="text-center py-12">
            <i class="bi bi-box-seam text-6xl text-indigo-200"></i>
            <h3 class="mt-4 text-2xl font-bold text-gray-700">No Rentals Found</h3>
            <p class="mt-2 text-gray-500">You have not made any rentals yet.</p>
            <a href="{{ route('customer.rental.main') }}" class="mt-6 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-lg shadow-md transition-all">
                Rent an Item
            </a>
        </div>
    @else
        <div class="overflow-x-auto shadow-md rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-800">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Rental ID</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Item</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Dates</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Total (RM)</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Deposit (RM)</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Return Approval</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rentals as $rental)
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $rental->rentalID }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $rental->item->item_Name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $rental->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-blue-700">
                                RM {{ number_format($rental->total_cost, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-purple-700">
                                RM {{ number_format($rental->deposit, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ strtolower($rental->rental_Status) == 'paid' 
                                        ? 'bg-green-100 text-green-800' 
                                        : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($rental->rental_Status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(strtolower($rental->rental_Status) == 'paid' && is_null($rental->return_Status))
                                    <form id="request-form-{{ $rental->rentalID }}" action="{{ route('customer.rental.requestApproval', $rental->rentalID) }}" method="POST">
                                        @csrf
                                        <button 
                                            type="button"
                                            class="bg-indigo-600 text-white w-40 px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition text-sm font-medium"
                                            onclick="requestReturn('{{ $rental->rentalID }}')">
                                            Request for Approval
                                        </button>
                                    </form>
                                @elseif($rental->return_Status)
                                    <span class="inline-block w-40 px-3 py-2 text-sm font-semibold rounded-lg text-center
                                        {{ strtolower($rental->return_Status) == 'approved' 
                                            ? 'bg-green-100 text-green-700' 
                                            : (strtolower($rental->return_Status) == 'rejected' 
                                                ? 'bg-red-100 text-red-700' 
                                                : 'bg-yellow-100 text-yellow-700') }}
                                    ">
                                        {{ ucfirst($rental->return_Status) }}
                                    </span>
                                @else
                                    <span class="inline-block w-40 text-gray-400 italic text-sm">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

@push('scripts')
{{-- SweetAlert2 is already in the main layout --}}
<script>
function requestReturn(rentalID) {
    Swal.fire({
        title: "Confirm Return Request?",
        text: "Do you want to request approval for returning this item?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5", // Indigo-600
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, request it!"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('request-form-' + rentalID).submit();
        }
    });
}
</script>
@endpush