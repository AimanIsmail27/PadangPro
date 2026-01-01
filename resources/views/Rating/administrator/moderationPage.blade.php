@extends('layout.admin')

@section('title', 'Review Moderation')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

{{-- Page Header --}}
<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-3xl shadow-2xl mb-6">
    <h1 class="text-2xl md:text-3xl font-bold">REVIEW MODERATION</h1>
    <p class="mt-2 text-gray-100">Manage flagged or under-review customer feedback</p>
</div>

{{-- Review Moderation Container (keep original, not rounded) --}}
<div class="bg-white shadow-md border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-200 text-green-800 rounded-2xl shadow">
            {{ session('success') }}
        </div>
    @endif

    @if($reviews->isEmpty())
        <div class="text-center py-10 px-6 bg-gray-50 rounded-2xl">
            <i class="bi bi-chat-quote text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">No flagged or under-review reviews at the moment.</p>
        </div>
    @else

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 shadow-md rounded-3xl overflow-hidden">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-amber-300 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-amber-300 uppercase tracking-wider">Type / Related</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-amber-300 uppercase tracking-wider">Review</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-amber-300 uppercase tracking-wider">Flag Reason</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-amber-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-amber-300 uppercase tracking-wider rounded-tr-3xl">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($reviews as $review)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $review->customer->customer_FullName ?? 'Anonymous' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($review->booking)
                                    <span class="font-semibold text-gray-900">Booking:</span> {{ $review->booking->field->field_Label }}
                                @elseif($review->rental)
                                    <span class="font-semibold text-gray-900">Rental:</span> {{ $review->rental->item->item_Name }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-normal text-sm text-gray-700 italic">{{ $review->review_Given }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                @php
                                    $reason = 'Detected inappropriate word';
                                    if ($review->admin_action) {
                                        $actionData = json_decode($review->admin_action, true);
                                        if(isset($actionData['reason'])) $reason = $actionData['reason'];
                                    }
                                @endphp
                                {{ $reason }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusColors = [
                                        'flagged' => 'bg-yellow-100 text-yellow-800',
                                        'under_review' => 'bg-orange-100 text-orange-800',
                                        'removed_by_admin' => 'bg-red-100 text-red-800',
                                        'normal' => 'bg-green-100 text-green-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusColors[$review->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center items-center gap-2 flex-wrap">

                                    {{-- Approve Button with SweetAlert2 --}}
                                    <button type="button" onclick="approveReview('{{ $review->ratingID }}', '{{ route('admin.reviews.approve', $review) }}')" class="inline-flex items-center px-3 py-1.5 rounded-2xl text-xs font-semibold bg-green-600 text-white hover:bg-green-700 transition shadow">
                                        <i class="bi bi-check-circle-fill mr-1"></i> Approve
                                    </button>

                                    {{-- Remove Button with SweetAlert2 --}}
                                    <button type="button" onclick="removeReview('{{ $review->ratingID }}', '{{ route('admin.reviews.remove', $review) }}')" class="inline-flex items-center px-3 py-1.5 rounded-2xl text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition shadow">
                                        <i class="bi bi-trash-fill mr-1"></i> Remove
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden space-y-4">
            @foreach($reviews as $review)
                <div class="bg-gray-50 p-4 rounded-3xl border border-gray-200 shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-bold text-gray-900">{{ $review->customer->customer_FullName ?? 'Anonymous' }}</div>
                        <div class="text-xs text-gray-500 uppercase">{{ ucfirst($review->status) }}</div>
                    </div>
                    <div class="text-sm text-gray-700 mb-1">
                        @if($review->booking)
                            <span class="font-semibold">Booking:</span> {{ $review->booking->field->field_Label }}
                        @elseif($review->rental)
                            <span class="font-semibold">Rental:</span> {{ $review->rental->item->item_Name }}
                        @endif
                    </div>
                    <div class="text-sm text-gray-700 italic mb-1">{{ $review->review_Given }}</div>
                    <div class="text-sm text-red-600 font-medium mb-2">
                        @php
                            $reason = 'Detected inappropriate word';
                            if ($review->admin_action) {
                                $actionData = json_decode($review->admin_action, true);
                                if(isset($actionData['reason'])) $reason = $actionData['reason'];
                            }
                        @endphp
                        {{ $reason }}
                    </div>
                    <div class="flex gap-2 flex-wrap mt-2">
                        <button type="button" onclick="approveReview('{{ $review->ratingID }}', '{{ route('admin.reviews.approve', $review) }}')" class="w-full inline-flex justify-center items-center px-3 py-2 rounded-2xl text-sm font-semibold bg-green-600 text-white hover:bg-green-700 transition shadow">
                            <i class="bi bi-check-circle-fill mr-1"></i> Approve
                        </button>
                        <button type="button" onclick="removeReview('{{ $review->ratingID }}', '{{ route('admin.reviews.remove', $review) }}')" class="w-full inline-flex justify-center items-center px-3 py-2 rounded-2xl text-sm font-semibold bg-red-600 text-white hover:bg-red-700 transition shadow">
                            <i class="bi bi-trash-fill mr-1"></i> Remove
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Remove Review
function removeReview(id, url) {
    Swal.fire({
        title: 'Remove Review',
        input: 'text',
        inputLabel: 'Reason',
        inputPlaceholder: 'Enter reason for removal',
        showCancelButton: true,
        confirmButtonText: 'Remove',
        cancelButtonText: 'Cancel',
        inputValidator: (value) => {
            if (!value) return 'You need to provide a reason!'
        },
        icon: 'warning',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const reason = document.createElement('input');
            reason.type = 'hidden';
            reason.name = 'reason';
            reason.value = result.value;
            form.appendChild(reason);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Approve Review with Confirmation
function approveReview(id, url) {
    Swal.fire({
        title: 'Approve Review?',
        text: 'Are you sure you want to approve this review?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
