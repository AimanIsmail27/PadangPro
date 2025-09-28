@extends('layout.customer')

@section('content')

<!-- Rental Header -->
<div class="bg-[#1E2A78] rounded-lg h-[120px] relative shadow-md mb-8">
    <div class="text-white font-bold text-xl px-8 py-6">
        YOUR RENTAL HISTORY
    </div>
</div>

<div class="container mx-auto mt-6">
    @if($rentals->isEmpty())
        <p class="text-gray-500 text-center text-lg">You have not made any rentals yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse bg-white shadow-lg rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gradient-to-r from-blue-100 to-blue-200 text-gray-700 uppercase text-sm tracking-wider">
                        <th class="px-6 py-4 text-left">Rental ID</th>
                        <th class="px-6 py-4 text-left">Item</th>
                        <th class="px-6 py-4 text-left">Dates</th>
                        <th class="px-6 py-4 text-center">Quantity</th>
                        <th class="px-6 py-4 text-center">Total Amount (RM)</th>
                        <th class="px-6 py-4 text-center">Deposit Amount (RM)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Return Approval</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @foreach($rentals as $rental)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $rental->rentalID }}</td>
                            <td class="px-6 py-4">{{ $rental->item->item_Name ?? 'Unknown' }}</td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M Y') }}
                                –
                                {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">{{ $rental->quantity }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-blue-700">
                                RM {{ number_format($rental->total_cost, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center font-semibold text-purple-700">
                                RM {{ number_format($rental->deposit, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="
                                    inline-block px-3 py-1 text-xs font-semibold rounded-full
                                    {{ strtolower($rental->rental_Status) == 'paid' 
                                        ? 'bg-green-100 text-green-700 border border-green-300' 
                                        : 'bg-yellow-100 text-yellow-700 border border-yellow-300' }}
                                ">
                                    {{ ucfirst($rental->rental_Status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(strtolower($rental->rental_Status) == 'paid' && is_null($rental->return_Status))
                                    <form id="request-form-{{ $rental->rentalID }}" action="{{ route('customer.rental.requestApproval', $rental->rentalID) }}" method="POST">
                                        @csrf
                                        <button 
    type="button"
    class="bg-blue-600 text-white w-40 px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition text-sm font-medium"
    onclick="requestReturn('{{ $rental->rentalID }}')">
    Request for Approval
</button>

                                    </form>
                                @elseif($rental->return_Status)
                                    <span class="inline-block w-40 px-3 py-2 text-sm font-semibold rounded-lg text-center
                                        {{ strtolower($rental->return_Status) == 'approved' 
                                            ? 'bg-green-100 text-green-700 border border-green-300' 
                                            : (strtolower($rental->return_Status) == 'rejected' 
                                                ? 'bg-red-100 text-red-700 border border-red-300' 
                                                : 'bg-yellow-100 text-yellow-700 border border-yellow-300') }}
                                    ">
                                        {{ ucfirst($rental->return_Status) }}
                                    </span>
                                @else
                                    <span class="inline-block w-40 text-gray-400 italic">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- SweetAlert script --}}
<script>
function requestReturn(rentalID) {
    Swal.fire({
        title: "Confirm Return Request?",
        text: "Do you want to request approval for returning this item?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, request it!"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('request-form-' + rentalID).submit();
        }
    });
}
</script>

@endsection
