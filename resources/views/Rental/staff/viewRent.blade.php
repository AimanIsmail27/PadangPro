@extends('layout.staff')

@section('title', 'Rental Records')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Rental Records</h1>
    <p class="mt-2 text-lime-100">View all upcoming, active, and completed rentals.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative space-y-12">

    {{-- Filter Form --}}
    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
        <form action="{{ route('staff.rentals.current') }}" method="GET" class="flex flex-wrap items-center gap-4">

            {{-- Month Filter --}}
            <div>
                <label for="month" class="text-sm font-medium text-gray-700">Filter by Month:</label>
                <select name="month" id="month" class="block w-52 rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500 sm:text-sm mt-1">
                    <option value="">All Months</option>
                    @foreach($monthList as $value => $label)
                        <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date Filter --}}
            <div>
                <label for="rental_date" class="text-sm font-medium text-gray-700">Search by Date:</label>
                <input type="date" name="rental_date" id="rental_date" value="{{ request('rental_date') }}"
                       class="block w-52 rounded-md border-gray-300 shadow-sm focus:border-lime-500 focus:ring-lime-500 sm:text-sm mt-1">
            </div>

            {{-- Search Button --}}
            <div class="mt-6">
                <button type="submit" class="bg-zinc-700 hover:bg-zinc-800 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition">
                    Search
                </button>
                <a href="{{ route('staff.rentals.current') }}" class="text-sm text-gray-600 hover:text-gray-900 ml-3">Clear Filters</a>
            </div>

        </form>
    </div>

    @if($rentals->isEmpty())
        <div class="text-center py-12">
            <i class="bi bi-box-seam text-6xl text-gray-300"></i>
            <h3 class="mt-4 text-2xl font-bold text-gray-700">No Rentals Found</h3>
            <p class="mt-2 text-gray-500">No rentals found for the selected filters.</p>
        </div>
    @else
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto shadow-md rounded-xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Rental ID / Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-lime-300 uppercase tracking-wider">Item Details</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-lime-300 uppercase tracking-wider">Total (RM)</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-lime-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-lime-300 uppercase tracking-wider">Return Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-lime-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rentals as $rental)
                        <tr class="hover:bg-slate-50/50 transition-all">
                            {{-- ID & Customer --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $rental->rentalID }}</div>
                                <div class="text-sm text-gray-500">{{ $rental->user->full_name ?? 'Unknown' }}</div>
                            </td>

                            {{-- Item & Dates --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $rental->item->item_Name ?? 'N/A' }} (x{{ $rental->quantity }})</div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M') }} - 
                                    {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M') }}
                                </div>
                            </td>

                            {{-- Cost --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-700">
                                RM {{ number_format($rental->total_cost, 2) }}
                            </td>

                            {{-- Payment Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(strtolower($rental->rental_Status) == 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Paid Full
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Deposit Paid
                                    </span>
                                @endif
                            </td>

                            {{-- Return Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($rental->return_Status)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ strtolower($rental->return_Status) == 'approved' ? 'bg-green-100 text-green-800' : 
                                          (strtolower($rental->return_Status) == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($rental->return_Status) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs italic">Active</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if(strtolower($rental->return_Status) == 'approved' && strtolower($rental->rental_Status) != 'completed')
                                    <form action="{{ route('staff.payment.markRentalCompleted', $rental->rentalID) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="button" class="inline-flex items-center px-3 py-1.5 rounded text-xs font-medium bg-green-600 text-white hover:bg-green-700 transition shadow-sm record-cash-btn">
                                            <i class="bi bi-cash-coin mr-1.5"></i> Record Cash
                                        </button>
                                    </form>
                                @elseif(strtolower($rental->rental_Status) == 'completed')
                                    <span class="text-green-600 text-xs font-bold"><i class="bi bi-check-all"></i> Closed</span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden space-y-4">
            @foreach($rentals as $rental)
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-bold text-gray-900">{{ $rental->rentalID }}</div>
                        <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M') }}</div>
                    </div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Customer:</strong> {{ $rental->user->full_name ?? 'Unknown' }}</div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Item:</strong> {{ $rental->item->item_Name ?? 'N/A' }} (x{{ $rental->quantity }})</div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Total:</strong> RM {{ number_format($rental->total_cost, 2) }}</div>
                    <div class="text-sm text-gray-500 mb-1"><strong>Rental Dates:</strong> {{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M') }} - {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M') }}</div>
                    <div class="mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                            {{ strtolower($rental->rental_Status) == 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ strtolower($rental->rental_Status) == 'completed' ? 'Paid Full' : 'Deposit Paid' }}
                        </span>
                        @if($rental->return_Status)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                {{ strtolower($rental->return_Status) == 'approved' ? 'bg-green-100 text-green-800' : (strtolower($rental->return_Status) == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($rental->return_Status) }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Active</span>
                        @endif
                    </div>
                    @if(strtolower($rental->return_Status) == 'approved' && strtolower($rental->rental_Status) != 'completed')
                        <form action="{{ route('staff.payment.markRentalCompleted', $rental->rentalID) }}" method="POST">
                            @csrf
                            <button type="button" class="w-full inline-flex justify-center items-center px-4 py-2 rounded-lg text-sm font-semibold bg-green-600 text-white hover:bg-green-700 transition shadow-md record-cash-btn">
                                <i class="bi bi-cash-coin mr-1.5"></i> Record Cash
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $rentals->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({ icon: 'success', title: 'Success!', text: '{{ session('success') }}', confirmButtonColor: '#166534' });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({ icon: 'error', title: 'Error!', text: '{{ session('error') }}', confirmButtonColor: '#d33' });
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cashButtons = document.querySelectorAll('.record-cash-btn');
    cashButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Confirm Cash Payment',
                text: "Mark this rental as fully paid (cash) and completed?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endpush
