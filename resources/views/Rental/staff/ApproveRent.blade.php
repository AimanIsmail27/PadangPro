@extends('layout.staff')

@section('title', 'Pending Rental Returns')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Pending Return Approvals</h1>
    <p class="mt-2 text-lime-100">Review and approve or reject customer rental returns.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    <div class="space-y-4">
        @forelse($rentals as $rental)
            <div class="bg-white shadow-lg rounded-lg border border-gray-100 p-6 hover:shadow-xl transition flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-500 text-sm font-medium">Rental ID</p>
                    <p class="text-gray-900 font-bold text-lg">{{ $rental->rentalID }}</p>
                    
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Customer</p>
                            <p class="text-gray-900 font-medium">{{ $rental->user->full_name ?? 'N/A' }}</p> {{-- Assumes user relationship --}}
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Item</p>
                            <p class="text-gray-900 font-medium">{{ $rental->item->item_Name ?? 'N/A' }} (x{{ $rental->quantity }})</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Rental Dates</p>
                            <p class="text-gray-900 font-medium">
                                {{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                {{-- Action Button --}}
                <div class="flex-shrink-0">
                    <button onclick="handleApproval('{{ $rental->rentalID }}')" 
                            class="w-full md:w-auto bg-zinc-700 text-white font-semibold px-5 py-3 rounded-lg shadow hover:bg-zinc-800 transition-all transform hover:scale-105">
                        <i class="bi bi-clipboard-check-fill mr-2"></i> Give Result
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed">
                <i class="bi bi-check2-circle text-6xl text-green-300"></i>
                <h3 class="mt-4 text-2xl font-bold text-gray-700">All Clear!</h3>
                <p class="mt-2 text-gray-500">There are no pending rental returns at this time.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
{{-- SweetAlert2 is already in the main layout --}}
<script>
function handleApproval(rentalId) {
    Swal.fire({
        title: 'Return Approval',
        text: 'Do you want to approve or reject this return?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Approve',
        denyButtonText: 'Reject',
        confirmButtonColor: '#16a34a', // Green
        denyButtonColor: '#d33', // Red
    }).then((result) => {
        if (result.isConfirmed) {
            confirmAction(rentalId, 'approved');
        } else if (result.isDenied) {
            confirmAction(rentalId, 'rejected');
        }
    });
}

function confirmAction(rentalId, status) {
    let titleText = status === 'approved' ? 'Approve Return' : 'Reject Return';
    let confirmText = status === 'approved' ? 'Yes, approve it!' : 'Yes, reject it!';
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to " + status + " this return. This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: confirmText,
        confirmButtonColor: (status === 'approved' ? '#16a34a' : '#d33'),
        cancelButtonColor: '#6b7280',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/staff/rentals/return-approval/${rentalId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Updated!', 'The return has been ' + status + '.', 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message || 'An unknown error occurred.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Could not update the status.', 'error');
            });
        }
    });
}
</script>
@endpush