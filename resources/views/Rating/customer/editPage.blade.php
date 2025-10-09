@extends('layout.customer')

@section('title', 'Edit Your Review')

@section('content')
<!-- =================== PAGE HEADER =================== -->
<div class="bg-[#1E2A78] rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
    <div class="text-white font-bold text-3xl px-8">
        Edit Your Review
    </div>
</div>

<!-- =================== MAIN FORM CARD =================== -->
<div class="bg-white rounded-xl shadow-md p-8 max-w-3xl mx-auto">

    <h2 class="text-2xl font-semibold text-[#1E2A78] mb-4">Update Your Experience</h2>
    <p class="text-gray-600 mb-6">
        You can adjust your previous rating or edit your review message here.
    </p>

    <form action="{{ route('customer.rating.update', $review->ratingID) }}" method="POST" class="space-y-8">
        @csrf

        <!-- =================== RATING STARS =================== -->
        <div>
            <label class="block text-gray-700 font-medium mb-2">Your Rating</label>
            <div class="flex space-x-2">
                @for ($i = 1; $i <= 5; $i++)
                    <label>
                        <input type="radio" name="rating_Score" value="{{ $i }}" 
                            class="hidden peer"
                            {{ $review->rating_Score == $i ? 'checked' : '' }} required>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor"
                             class="w-9 h-9 text-gray-300 hover:text-yellow-400 cursor-pointer peer-checked:text-yellow-400 transition">
                             <path stroke-linecap="round" stroke-linejoin="round"
                                   d="M11.48 3.499a.562.562 0 011.04 0l2.062 4.178 4.616.671a.562.562 0 01.312.959l-3.34 3.256.788 4.597a.562.562 0 01-.815.592L12 15.347l-4.138 2.177a.562.562 0 01-.815-.592l.788-4.597-3.34-3.256a.562.562 0 01.312-.959l4.616-.671 2.062-4.178z" />
                        </svg>
                    </label>
                @endfor
            </div>
            <p class="text-sm text-gray-500 mt-2">1 = Poor, 5 = Excellent</p>
        </div>

        <!-- =================== REVIEW TEXT =================== -->
        <div>
            <label for="review_Given" class="block text-gray-700 font-medium mb-2">Your Review</label>
            <textarea name="review_Given" id="review_Given" rows="5" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1E2A78] focus:border-[#1E2A78] p-3"
                placeholder="Update your thoughts...">{{ old('review_Given', $review->review_Given) }}</textarea>
        </div>

        <!-- =================== BUTTONS =================== -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('customer.rating.main') }}"
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
               Cancel
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-[#1E2A78] text-white rounded-md hover:bg-[#2638a0] transition">
                Update Review
            </button>
        </div>
    </form>
</div>
@endsection
