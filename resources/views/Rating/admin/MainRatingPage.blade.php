@extends('layout.admin')

@section('title', 'All Ratings & Reviews')

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold">ALL RATING & REVIEW</h1>
    
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    <div class="flex justify-between items-center mb-6 flex-wrap gap-3 border-b pb-4">
        <h2 class="text-xl font-semibold text-gray-800">Customer Feedback</h2>
        <div class="flex items-center space-x-3">
            <label for="filter" class="text-gray-700 font-medium">Sort By:</label>
            <select id="filter"
                    class="border-gray-300 rounded-md p-2 shadow-sm focus:ring-amber-500 focus:border-amber-500"
                    onchange="window.location='{{ route('admin.rating.view') }}?filter=' + this.value">
                <option value="latest" {{ $currentSort == 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="oldest" {{ $currentSort == 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="high_rating" {{ $currentSort == 'high_rating' ? 'selected' : '' }}>Highest Rating</option>
                <option value="low_rating" {{ $currentSort == 'low_rating' ? 'selected' : '' }}>Lowest Rating</option>
            </select>
        </div>
    </div>

    @if($allRatings->isEmpty())
        <div class="text-center py-10 px-6 bg-gray-50 rounded-lg">
            <i class="bi bi-chat-quote text-4xl text-gray-400"></i>
            <p class="mt-4 text-gray-500">No reviews have been submitted yet.</p>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($allRatings as $rating)
                <div class="bg-white p-5 rounded-lg shadow-md border hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-2">
                        {{-- Safely access user's full name --}}
                        <h3 class="font-semibold text-lg text-gray-800">{{ $rating->customer->customer_FullName ?? 'Anonymous User' }}</h3>
                        <span class="text-yellow-500 font-bold text-lg">
                            {{ str_repeat('★', $rating->rating_Score) }}{{ str_repeat('☆', 5 - $rating->rating_Score) }}
                        </span>
                    </div>
                    <p class="text-gray-600 mb-3 italic">“{{ $rating->review_Given }}”</p>
                    <p class="text-sm text-gray-400">
                        Submitted on: {{ \Carbon\Carbon::parse($rating->review_Date)->format('d M Y') }}
                    </p>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $allRatings->links() }}
        </div>
    @endif
</div>

{{-- SweetAlert2 for success/error messages, if any --}}
@if(session('success') || session('error'))
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({ title: "Success!", text: "{{ session('success') }}", icon: "success" });
        @endif
        @if(session('error'))
            Swal.fire({ title: "Error!", text: "{{ session('error') }}", icon: "error" });
        @endif
    </script>
    @endpush
@endif

@endsection