@extends('layout.customer')

@section('title', 'Add New Review')

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Add New Review</h1>
    <p class="mt-2 text-indigo-100">Share your experience with other players.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative">

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-bold">Action Not Allowed!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Please fix the following issues:</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="text-2xl font-bold text-gray-800 mb-4">Share Your Experience</h2>
    <p class="text-gray-600 mb-6">
        Tell us about your experience using PadangPro — how was the booking, field condition, or overall service?
    </p>

    <form action="{{ route('customer.rating.store') }}" method="POST" class="space-y-8">
        @csrf

        <div>
            <label class="block text-gray-700 font-medium mb-2">Your Rating</label>
            <div class="flex space-x-2">
                @for ($i = 1; $i <= 5; $i++)
                    <label>
                        <input type="radio" name="rating_Score" value="{{ $i }}" class="hidden peer" required>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor"
                             class="w-10 h-10 text-gray-300 hover:text-yellow-400 cursor-pointer peer-checked:text-yellow-400 transition">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11.48 3.499a.562.562 0 011.04 0l2.062 4.178 4.616.671a.562.562 0 01.312.959l-3.34 3.256.788 4.597a.562.562 0 01-.815.592L12 15.347l-4.138 2.177a.562.562 0 01-.815-.592l.788-4.597-3.34-3.256a.562.562 0 01.312-.959l4.616-.671 2.062-4.178z" />
                        </svg>
                    </label>
                @endfor
            </div>
            <p class="text-sm text-gray-500 mt-2">1 = Poor, 5 = Excellent</p>
        </div>

        <div>
            <label for="review_Given" class="block text-gray-700 font-medium mb-2">Your Review</label>
            <textarea name="review_Given" id="review_Given" rows="5" required
                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3"
                      placeholder="Share your thoughts about the field, booking process, or overall experience...">{{ old('review_Given') }}</textarea>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
            <a href="{{ route('customer.rating.main') }}"
               class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
               Cancel
            </a>
            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold shadow-md">
                Submit Review
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Note: Success popup is handled on the main rating page after redirect --}}
{{-- This is just for the "Action Not Allowed" error --}}
@if (session('error'))
    <script>
        Swal.fire({
            title: "Action Not Allowed",
            text: "{{ session('error') }}",
            icon: "error",
            confirmButtonColor: "#4f46e5",
        });
    </script>
@endif
@endpush