@extends('layout.customer')

@section('title', 'Rating & Review')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Rating & Review</h1>
    <p class="mt-2 text-indigo-100">See what other players are saying.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative space-y-10">

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Your Submitted Review</h2>
            <div class="flex-shrink-0 space-x-3">
                @if(empty($yourSubmittedReview))
                    <a href="{{ route('customer.rating.add') }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition-all transform hover:scale-105">
                        <i class="bi bi-plus-circle-fill mr-1"></i> Add New Review
                    </a>
                @else
                    <a href="{{ route('customer.rating.edit', $yourSubmittedReview['ratingID']) }}" 
                       class="bg-yellow-500 text-white font-semibold px-5 py-2 rounded-lg shadow-md hover:bg-yellow-600 transition-all">
                        <i class="bi bi-pencil-fill mr-1"></i> Edit Review
                    </a>
                    <a href="javascript:void(0);" 
                       onclick="confirmDelete('{{ route('customer.rating.delete', $yourSubmittedReview['ratingID']) }}')" 
                       class="bg-red-600 text-white font-semibold px-5 py-2 rounded-lg shadow-md hover:bg-red-700 transition-all">
                       <i class="bi bi-trash-fill mr-1"></i> Delete Review
                    </a>
                @endif
            </div>
        </div>

        <div class="border-t pt-6">
            @if(!empty($yourSubmittedReview))
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-lg text-indigo-700">Your Latest Review</h3>
                    <span class="text-yellow-500 font-bold text-lg">
                        {{ str_repeat('★', $yourSubmittedReview['rating_Score']) }}
                        {{ str_repeat('☆', 5 - $yourSubmittedReview['rating_Score']) }}
                    </span>
                </div>
                <p class="text-gray-700 mb-3 text-lg italic">“{{ $yourSubmittedReview['review_Given'] }}”</p>
                <p class="text-sm text-gray-500">
                    Submitted on: {{ \Carbon\Carbon::parse($yourSubmittedReview['review_Date'])->format('d M Y') }}
                </p>
            @else
                <p class="text-gray-500 italic">You haven't submitted any review yet.</p>
            @endif
        </div>
    </div>

    <div class="bg-slate-50 p-6 rounded-lg shadow-inner border border-slate-200">
        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">All Ratings & Reviews</h2>
            <div class="flex items-center space-x-3">
                <label for="filter" class="text-gray-700 font-medium">Sort By:</label>
                <select id="filter"
                        class="border-gray-300 rounded-md p-2 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        onchange="window.location='{{ route('customer.rating.main') }}?filter=' + this.value">
                    <option value="latest" {{ $currentSort == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="oldest" {{ $currentSort == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="high_rating" {{ $currentSort == 'high_rating' ? 'selected' : '' }}>Highest Rating</option>
                    <option value="low_rating" {{ $currentSort == 'low_rating' ? 'selected' : '' }}>Lowest Rating</option>
                </select>
            </div>
        </div>

        <div class="space-y-5">
            @foreach ($allRatings as $rating)
                <div class="bg-white p-5 rounded-lg shadow-md border hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg text-gray-800">{{ $rating->customer->customer_FullName ?? 'Anonymous User' }}</h3>
                        <span class="text-yellow-500 font-bold text-lg">
                            {{ str_repeat('★', $rating['rating_Score']) }}
                            {{ str_repeat('☆', 5 - $rating['rating_Score']) }}
                        </span>
                    </div>
                    <p class="text-gray-700 mb-3 italic">“{{ $rating['review_Given'] }}”</p>
                    <p class="text-sm text-gray-500">
                        Submitted on: {{ \Carbon\Carbon::parse($rating['review_Date'])->format('d M Y') }}
                    </p>
                </div>
            @endforeach
        </div>

        @if ($allRatings->hasPages())
            <div class="mt-10">
                {{-- This uses the built-in, styled Tailwind paginator --}}
                {{ $allRatings->appends(['filter' => $currentSort])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if (session('success'))
    Swal.fire({
        title: "Success!",
        text: "{{ session('success') }}",
        icon: "success",
        confirmButtonColor: "#4f46e5", // Indigo-600
    });
@endif

@if (session('error'))
    Swal.fire({
        title: "Action Not Allowed",
        text: "{{ session('error') }}",
        icon: "error",
        confirmButtonColor: "#4f46e5",
    });
@endif

function confirmDelete(url) {
    Swal.fire({
        title: "Are you sure?",
        text: "This action will permanently delete your review.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
@endpush