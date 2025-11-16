@extends('layout.customer')

@section('title', 'View Requests for ' . $ad->ads_Name)

@section('content')
<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
    <div class="text-white font-bold text-2xl px-8">
        Incoming Requests for "{{ $ad->ads_Name }}"
    </div>
    
</div>
<div class="container mx-auto">
        @if($requests->isEmpty())
            <div class="text-center py-10">
                <p class="text-gray-600 text-lg">No incoming requests yet.</p>
            </div>
        @else
            @php
                // Separate approved and others
                $approved = collect($requests)->filter(fn($r) => strtolower($r->status) === 'approved');
                $others = collect($requests)->filter(fn($r) => strtolower($r->status) !== 'approved');
                // Group pending/rejected by date (KL timezone)
                $grouped = $others->groupBy(fn($r) => \Carbon\Carbon::parse($r->created_at)->timezone('Asia/Kuala_Lumpur')->toDateString());
            @endphp

            @if($approved->isNotEmpty())
                <div class="mb-10">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">✅ Approved Requests</h2>
                    <div class="bg-white rounded-lg shadow-md border-2 border-green-500">
                        <ul class="divide-y divide-gray-200">
                            @foreach($approved as $request)
                                <li class="p-4 flex justify-between items-center transition duration-300 ease-in-out hover:bg-green-50 hover:shadow-md">
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ $request->customer->customer_FullName ?? 'Unknown User' }}
                                        </p>
                                        <p class="text-gray-500 text-sm">
                                            📞 {{ $request->customer->customer_PhoneNumber ?? 'No phone number' }}
                                        </p>
                                        <p class="text-gray-500 text-sm mt-1">
                                            {{ $request->note ?? 'No message provided' }}
                                        </p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-md bg-green-100 text-green-700 border border-green-500">
                                        Approved
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="relative border-l-2 border-gray-300 ml-6">
                @foreach($grouped as $date => $items)
                    <div class="mb-6 ml-6">
                        <span class="text-gray-600 text-sm font-medium">
                            {{ \Carbon\Carbon::parse($date)->timezone('Asia/Kuala_Lumpur')->format('d M Y') }}
                        </span>
                    </div>

                    @foreach($items as $request)
                        <div class="mb-8 ml-6 relative">
                            <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full bg-gray-400 border-2 border-gray-600"></span>

                            <div class="bg-white rounded-lg shadow-md border-2 border-gray-300 p-4 transition duration-300 ease-in-out hover:shadow-lg hover:border-gray-500">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h3 class="text-base font-semibold text-gray-800">
                                            {{ $request->customer->customer_FullName ?? 'Unknown User' }}
                                        </h3>
                                        <p class="text-gray-500 text-sm">
                                            📞 {{ $request->customer->customer_PhoneNumber ?? 'No phone number' }}
                                        </p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-md 
                                        {{ strtolower($request->status) === 'pending' ? 'bg-yellow-100 text-yellow-700 border border-yellow-400' : '' }}
                                        {{ strtolower($request->status) === 'rejected' ? 'bg-red-100 text-red-700 border border-red-400' : '' }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>

                                <p class="text-gray-600 mt-2 text-sm">
                                    {{ $request->note ?? 'No message provided' }}
                                </p>

                                <p class="text-gray-400 text-xs mt-1">
                                    {{ \Carbon\Carbon::parse($request->created_at)->timezone('Asia/Kuala_Lumpur')->format('h:i A') }}
                                </p>

                                @if(strtolower($request->status) === 'pending')
                                    <div class="mt-3 flex gap-2">
                                        @if($request->canApprove)
                                            <form method="POST" action="{{ route('applications.accept', $request->applicationID) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded text-xs shadow-sm accept-btn">
                                                    Accept
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('applications.reject', $request->applicationID) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs shadow-sm reject-btn">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Accept / Reject confirmation
    document.querySelectorAll('.accept-btn, .reject-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            let form = this.closest('form');
            let isAccept = this.classList.contains('accept-btn');

            Swal.fire({
                title: isAccept ? 'Accept this request?' : 'Reject this request?',
                text: "Are you sure? Action cannot be undone.",
                icon: isAccept ? 'question' : 'warning',
                showCancelButton: true,
                confirmButtonColor: isAccept ? '#16a34a' : '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: isAccept ? 'Yes, accept' : 'Yes, reject'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

@if(session('swal_success'))
<script>
Swal.fire({
    title: 'Success!',
    text: "{{ session('swal_success') }}",
    icon: 'success',
    confirmButtonColor: '#4f46e5' // Indigo-600
});
</script>
@endif
@endpush