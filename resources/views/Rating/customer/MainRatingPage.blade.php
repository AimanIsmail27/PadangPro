@extends('layout.customer')

@section('title', 'Rating & Review')

@section('content')
<!-- Header Section -->
<div class="bg-[#1E2A78] rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
    <div class="text-white font-bold text-2xl px-8">
        Rating & Review
    </div>
</div>

<div class="container mx-auto px-6 py-6">

    <!-- ====== User's Own Review Section ====== -->
    <div class="bg-white rounded-xl shadow-md p-6 border hover:shadow-lg transition mb-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-[#1E2A78]">Your Submitted Review</h2>
            <div class="space-x-3">
                <button class="bg-[#1E2A78] text-white px-4 py-2 rounded-md hover:bg-[#2638a0] transition">Add New Review</button>
                <button class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition">Edit Review</button>
            </div>
        </div>

        @if(!empty($yourSubmittedReview))
        <div class="border-t pt-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-lg text-[#1E2A78]">Your Latest Review</h3>
                <span class="text-yellow-500 font-bold">
                    {{ str_repeat('★', $yourSubmittedReview['rating_Score']) }}
                    {{ str_repeat('☆', 5 - $yourSubmittedReview['rating_Score']) }}
                </span>
            </div>
            <p class="text-gray-700 mb-3 text-sm">“{{ $yourSubmittedReview['review_Given'] }}”</p>
            <p class="text-sm text-gray-500">
                Submitted on: {{ \Carbon\Carbon::parse($yourSubmittedReview['review_Date'])->format('d M Y') }}
            </p>
        </div>
        @else
        <p class="text-gray-500 italic">You haven't submitted any review yet.</p>
        @endif
    </div>

    <!-- ====== All Ratings & Reviews ====== -->
    <div class="bg-gray-50 p-6 rounded-lg shadow-inner border-2 border-[#1E2A78]/60">
        <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
            <h2 class="text-xl font-semibold text-gray-800">All Ratings & Reviews</h2>
            <div class="flex items-center space-x-3">
                <label for="filter" class="text-gray-700 font-medium">Sort By:</label>
                <select id="filter"
                        class="border-gray-300 rounded-md p-2 shadow-sm focus:ring-[#1E2A78] focus:border-[#1E2A78]"
                        onchange="window.location='{{ route('customer.rating.main') }}?filter=' + this.value">
                    <option value="latest" {{ $currentSort == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="oldest" {{ $currentSort == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="high_rating" {{ $currentSort == 'high_rating' ? 'selected' : '' }}>Highest Rating</option>
                    <option value="low_rating" {{ $currentSort == 'low_rating' ? 'selected' : '' }}>Lowest Rating</option>
                </select>
            </div>
        </div>

        <!-- Ratings List -->
        <div class="space-y-5">
            @foreach ($allRatings as $rating)
                <div class="bg-white p-5 rounded-lg shadow-md border hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-lg text-[#1E2A78]">User {{ $rating['userID'] }}</h3>
                        <span class="text-yellow-500 font-bold">
                            {{ str_repeat('★', $rating['rating_Score']) }}
                            {{ str_repeat('☆', 5 - $rating['rating_Score']) }}
                        </span>
                    </div>
                    <p class="text-gray-700 mb-3 text-sm">“{{ $rating['review_Given'] }}”</p>
                    <p class="text-sm text-gray-500">
                        Submitted on: {{ \Carbon\Carbon::parse($rating['review_Date'])->format('d M Y') }}
                    </p>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($allRatings->hasPages())
            <div class="mt-10 text-center">
                <p class="text-gray-600 mb-4">
                    Showing {{ $allRatings->firstItem() }} to {{ $allRatings->lastItem() }} of {{ $allRatings->total() }} results
                </p>
                <div class="flex justify-center space-x-2">
                    {{-- Previous Page --}}
                    @if ($allRatings->onFirstPage())
                        <span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">&lt;</span>
                    @else
                        <a href="{{ $allRatings->appends(['filter' => $currentSort])->previousPageUrl() }}" class="px-3 py-1 bg-[#1E2A78] text-white rounded hover:bg-[#2638a0]">&lt;</a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($allRatings->getUrlRange(1, $allRatings->lastPage()) as $page => $url)
                        @if ($page == $allRatings->currentPage())
                            <span class="px-3 py-1 bg-yellow-500 text-white rounded">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}&filter={{ $currentSort }}" class="px-3 py-1 bg-white border text-[#1E2A78] rounded hover:bg-gray-100">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($allRatings->hasMorePages())
                        <a href="{{ $allRatings->appends(['filter' => $currentSort])->nextPageUrl() }}" class="px-3 py-1 bg-[#1E2A78] text-white rounded hover:bg-[#2638a0]">&gt;</a>
                    @else
                        <span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">&gt;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
