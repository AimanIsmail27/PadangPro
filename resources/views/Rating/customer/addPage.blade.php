@extends('layout.customer')

@section('title', 'Add New Review')

@section('content')

<!-- =================== PAGE HEADER =================== -->
<div class="bg-[#1E2A78] rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
    <div class="text-white font-bold text-3xl px-8">
        Add New Review
    </div>
</div>

<!-- =================== MAIN FORM CARD =================== -->
<div class="bg-white rounded-xl shadow-md p-8 max-w-3xl mx-auto">

    <!-- Success & Error Messages -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <h2 class="text-2xl font-semibold text-[#1E2A78] mb-4">Share Your Experience</h2>
    <p class="text-gray-600 mb-6">
        Tell us about your experience using PadangPro — how was the booking, field condition, or overall service?
    </p>

    <form action="{{ route('customer.rating.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- =================== RATING STARS =================== -->
        <div>
            <label class="block text-gray-700 font-medium mb-2">Your Rating</label>
            <div class="flex space-x-2">
                @for ($i = 1; $i <= 5; $i++)
                    <label>
                        <input type="radio" name="rating_Score" value="{{ $i }}" class="hidden peer" required>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor"
                             class="w-9 h-9 text-gray-300 hover:text-yellow-400 cursor-pointer peer-checked:text-yellow-400 transition">
                             <path stroke-linecap="round" stroke-linejoin="round"
                                   d="M11.48 3.499a.562.562 0 011.04 0l2.062 4.178 4.616.671a.562.562 0 01.312.959l-3.34 3.256.788 4.597a.562.562 0 01-.815.592L12 15.347l-4.138 2.177a.562.562 0 01-.815-.592l.788-4.597-3.34-3.256a.562.562 0 01.312-.959l4.616-.671 2.062-4.178z" />
                        </svg>
                    </label>
                @endfor
            </div>
            @error('rating_Score')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-sm text-gray-500 mt-2">1 = Poor, 5 = Excellent</p>
        </div>

        <!-- =================== REVIEW TEXT =================== -->
        <div>
            <label for="review_Given" class="block text-gray-700 font-medium mb-2">Your Review</label>
            <textarea name="review_Given" id="review_Given" rows="5" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-[#1E2A78] focus:border-[#1E2A78] p-3"
                placeholder="Share your thoughts about the field, booking process, or overall experience...">{{ old('review_Given') }}</textarea>
            @error('review_Given')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- =================== BUTTONS =================== -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('customer.rating.main') }}"
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
               Cancel
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-[#1E2A78] text-white rounded-md hover:bg-[#2638a0] transition">
                Submit Review
            </button>
        </div>

    </form>
</div>

@endsection
<!-- =================== SWEETALERT2 =================== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ✅ Success Popup
    @if (session('success'))
        Swal.fire({
            title: "Review Submitted!",
            text: "{{ session('success') }}",
            icon: "success",
            confirmButtonColor: "#1E2A78",
            confirmButtonText: "Go to Main Page",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('customer.rating.main') }}";
            }
        });
    @endif

    // ⚠️ Error Popup
    @if (session('error'))
        Swal.fire({
            title: "Action Not Allowed",
            text: "{{ session('error') }}",
            icon: "error",
            confirmButtonColor: "#1E2A78",
        });
    @endif
</script>
