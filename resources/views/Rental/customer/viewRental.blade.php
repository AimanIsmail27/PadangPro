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

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative space-y-12">

    {{-- Filter Bar --}}
    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-8">
        <form action="{{ route('customer.rental.history') }}" method="GET" class="flex flex-wrap items-center space-x-4">
            <label for="month" class="text-sm font-medium text-gray-700">Filter by Month:</label>
            <select name="month" id="month" onchange="this.form.submit()" class="block w-full md:w-52 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @foreach($monthList as $value => $label)
                    <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            
            @php $currentMonth = \Carbon\Carbon::now('Asia/Kuala_Lumpur')->format('Y-m'); @endphp
            @if($selectedMonth != $currentMonth)
                <a href="{{ route('customer.rental.history', ['month' => $currentMonth]) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Go to Current Month
                </a>
            @endif
        </form>
    </div>

    @if($activeRentals->isEmpty() && $rentalHistory->isEmpty())
        <div class="text-center py-12">
            <i class="bi bi-box-seam text-6xl text-indigo-200"></i>
            <h3 class="mt-4 text-2xl font-bold text-gray-700">No Rentals Found</h3>
            <p class="mt-2 text-gray-500">You have not made any rentals for the selected month.</p>
            <a href="{{ route('customer.rental.main') }}" class="mt-6 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-lg shadow-md transition-all">
                Rent an Item
            </a>
        </div>
    @else
        
        {{-- =============================================== --}}
        {{-- SECTION 1: ACTIVE RENTALS (PAID & ONGOING) --}}
        {{-- =============================================== --}}
        @if($activeRentals->isNotEmpty())
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Active Rentals</h2>
            
            {{-- DESKTOP TABLE VIEW --}}
            <div class="hidden md:block overflow-x-auto shadow-md rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Deposit (RM)</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($activeRentals as $rental)
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ $rental->item->item_Name ?? 'Unknown' }}
                                    <div class="text-xs text-gray-500 font-normal">ID: {{ $rental->rentalID }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $rental->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-blue-700">
                                    RM {{ number_format($rental->deposit, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    {{-- Status Badge --}}
                                    @if(strtolower($rental->rental_Status) == 'paid')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            <i class="bi bi-wallet2 mr-1.5"></i> Deposit Paid
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($rental->rental_Status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if(strtolower($rental->rental_Status) == 'paid' && is_null($rental->return_Status))
                                        <form id="request-form-{{ $rental->rentalID }}" action="{{ route('customer.rental.requestApproval', $rental->rentalID) }}" method="POST">
                                            @csrf
                                            <button type="button"
                                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition text-xs font-bold"
                                                onclick="requestReturn('{{ $rental->rentalID }}')">
                                                Request Return
                                            </button>
                                        </form>
                                    @elseif($rental->return_Status)
                                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                            {{ ucfirst($rental->return_Status) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARD VIEW --}}
            <div class="md:hidden space-y-4">
                @foreach($activeRentals as $rental)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                        {{-- Top Row: Item Name / Deposit --}}
                        <div class="flex justify-between items-start mb-2 pb-2 border-b border-gray-100">
                            <div>
                                <h3 class="font-bold text-gray-800 text-base line-clamp-1">{{ $rental->item->item_Name ?? 'Unknown' }} (x{{ $rental->quantity }})</h3>
                                <p class="text-xs text-gray-500">ID: {{ $rental->rentalID }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-blue-700 block">RM {{ number_format($rental->deposit, 2) }}</span>
                                <span class="text-xs text-gray-500 block">Deposit</span>
                            </div>
                        </div>
                        {{-- Bottom Row: Dates / Action --}}
                        <div class="flex justify-between items-end mt-2 pt-2">
                            <div>
                                <p class="text-xs font-medium text-gray-700 mb-1">Rental Period</p>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M') }} - 
                                    {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if(strtolower($rental->rental_Status) == 'paid' && is_null($rental->return_Status))
                                    <form id="request-form-mobile-{{ $rental->rentalID }}" action="{{ route('customer.rental.requestApproval', $rental->rentalID) }}" method="POST">
                                        @csrf
                                        <button type="button"
                                            class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg shadow hover:bg-indigo-700 transition text-xs font-bold"
                                            onclick="requestReturn('{{ $rental->rentalID }}')">
                                            Return Item
                                        </button>
                                    </form>
                                @elseif($rental->return_Status)
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                        Return: {{ ucfirst($rental->return_Status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $activeRentals->links() }}
            </div>
        </div>
        @endif

        {{-- =============================================== --}}
        {{-- SECTION 2: RENTAL HISTORY --}}
        {{-- =============================================== --}}
        @if($rentalHistory->isNotEmpty())
        <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Rental History</h2>
            
            {{-- DESKTOP TABLE VIEW --}}
            <div class="hidden md:block overflow-x-auto shadow-md rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-slate-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-indigo-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Total (RM)</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Final Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-indigo-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($rentalHistory as $rental)
                            <tr class="hover:bg-slate-50/50 transition-all opacity-70">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ $rental->item->item_Name ?? 'Unknown' }}
                                    <div class="text-xs text-gray-500 font-normal">ID: {{ $rental->rentalID }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $rental->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                    RM {{ number_format($rental->total_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @include('rental.customer.partials.history_status_badge', ['rental' => $rental])
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @include('rental.customer.partials.history_action_button', ['rental' => $rental])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MOBILE CARD VIEW (FIXED STACKING) --}}
            <div class="md:hidden space-y-3">
                @foreach($rentalHistory as $rental)
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 opacity-80">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-gray-800 text-base line-clamp-1">{{ $rental->item->item_Name ?? 'Unknown' }} (x{{ $rental->quantity }})</h3>
                                <p class="text-xs text-gray-500">ID: {{ $rental->rentalID }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-indigo-600 block">RM {{ number_format($rental->total_cost, 2) }}</span>
                                <span class="text-xs text-gray-500 block">Total Cost</span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-100">
                            <div>
                                <p class="text-xs font-medium text-gray-700">Status</p>
                                @include('rental.customer.partials.history_status_badge', ['rental' => $rental])
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @include('rental.customer.partials.history_action_button', ['rental' => $rental])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $rentalHistory->links() }}
            </div>
        </div>
        @endif
        
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Success/Error Alerts and Request Return Logic --}}
<script>
// ... (Your existing script logic for SweetAlerts and requestReturn function) ...
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
            // Use the mobile form ID convention if available, otherwise default
            const formId = window.innerWidth < 768 ? 'request-form-mobile-' + rentalID : 'request-form-' + rentalID;
            document.getElementById(formId).submit();
        }
    });
}
</script>
<script>
    // --- Success Alert (Triggers after successful payment or rating submission) ---
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#4f46e5', // Indigo-600
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}",
            confirmButtonColor: '#d33'
        });
    @endif

    @if(session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Pending',
            text: "{{ session('warning') }}",
            confirmButtonColor: '#f59e0b'
        });
    @endif
</script>
@endpush