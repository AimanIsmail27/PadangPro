@extends('layout.staff')

@section('title', 'All Ratings & Reviews')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">All Ratings & Reviews</h1>
    <p class="mt-2 text-lime-100">Review all customer feedback here.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 md:p-8 w-11/12 mx-auto -mt-16 relative">

    <div class="flex justify-between items-center mb-6 flex-wrap gap-3 border-b pb-4">
        <h2 class="text-2xl font-bold text-gray-800">Customer Feedback</h2>
        <div class="flex items-center space-x-3">
            <label for="filter" class="text-gray-700 font-medium">Sort By:</label>
            <select id="filter"
                    class="border-gray-300 rounded-md p-2 shadow-sm focus:ring-lime-500 focus:border-lime-500"
                    onchange="window.location='{{ route('staff.rating.view') }}?filter=' + this.value">
                <option value="latest" {{ $currentSort == 'latest' ? 'selected' : '' }}>Latest</option>
                <option value="oldest" {{ $currentSort == 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="high_rating" {{ $currentSort == 'high_rating' ? 'selected' : '' }}>Highest Rating</option>
                <option value="low_rating" {{ $currentSort == 'low_rating' ? 'selected' : '' }}>Lowest Rating</option>
            </select>
        </div>
    </div>

    @if($allRatings->isEmpty())
        <div class="text-center py-12 px-6 bg-slate-50 rounded-lg">
            <i class="bi bi-chat-quote text-6xl text-gray-300"></i>
            <h3 class="mt-4 text-xl font-bold text-gray-700">No Reviews Found</h3>
            <p class="mt-2 text-gray-500">No reviews have been submitted by customers yet.</p>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($allRatings as $rating)
                <div class="bg-white p-5 rounded-lg shadow-md border hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-2">
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
@endsection

@push('scripts')
{{-- SweetAlert2 for success/error messages, if any --}}
@if(session('success') || session('error'))
    <script>
        @if(session('success'))
            Swal.fire({ 
                title: "Success!", 
                text: "{{ session('success') }}", 
                icon: "success",
                confirmButtonColor: '#166534' // green-800
            });
        @endif
        @if(session('error'))
            Swal.fire({ 
                title: "Error!", 
                text: "{{ session('error') }}", 
                icon: "error",
                confirmButtonColor: '#d33'
            });
        @endif
    </script>
@endif
@endpush