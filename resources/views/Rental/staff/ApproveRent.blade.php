@extends('layout.staff')

@section('content')

<!-- Soft Green Header -->
<div class="bg-green-200 rounded-xl shadow-md p-6 mb-6">
    <h2 class="text-2xl font-bold text-black">Return Approval</h2>
    <p class="text-black mt-2">Welcome to the return approval page. Staff can manage return approval here.</p>
</div>

<div class="container mx-auto p-6 space-y-4">

    @forelse($rentals as $rental)
    <div class="bg-white shadow-md rounded-lg border border-gray-200 p-4 hover:shadow-lg transition flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="mb-2 md:mb-0">
            <p class="text-gray-700 font-semibold">Rental ID: <span class="text-gray-900">{{ $rental->rentalID }}</span></p>
            <p class="text-gray-700">Item: <span class="text-gray-900">{{ $rental->item->item_Name ?? 'N/A' }}</span></p>
            <p class="text-gray-700">Dates: 
                <span class="text-gray-900">{{ \Carbon\Carbon::parse($rental->rental_StartDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($rental->rental_EndDate)->format('d M Y') }}</span>
            </p>
            <p class="text-gray-700">Quantity: <span class="text-gray-900">{{ $rental->quantity }}</span></p>
            <p class="text-gray-700">Customer: <span class="text-gray-900">{{ $rental->userID }}</span></p>
        </div>
        <div class="flex space-x-2 mt-2 md:mt-0">
            <button onclick="handleApproval('{{ $rental->rentalID }}')" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition font-semibold">
                Give Result
            </button>
        </div>
    </div>
    @empty
    <div class="bg-white shadow-md rounded-lg border border-gray-200 p-6 text-center text-gray-500 italic">
        No return approvals pending.
    </div>
    @endforelse

</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function handleApproval(rentalId) {
    Swal.fire({
        title: 'Return Approval',
        text: 'Do you want to approve or reject this return?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Approve',
        denyButtonText: 'Reject',
    }).then((result) => {
        if (result.isConfirmed) {
            confirmAction(rentalId, 'approved');
        } else if (result.isDenied) {
            confirmAction(rentalId, 'rejected');
        }
    });
}

function confirmAction(rentalId, status) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to " + status + " this return.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, confirm it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/staff/rentals/return-approval/${rentalId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Updated!', 'The return has been ' + status + '.', 'success')
                        .then(() => location.reload());
                }
            });
        }
    });
}
</script>

@endsection