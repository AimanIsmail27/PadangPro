@extends('layout.customer')

@section('title', 'Write a Review')

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Rate Your Experience</h1>
    <p class="mt-2 text-indigo-100">
        You are reviewing your {{ $type }} for <span class="font-bold text-white">{{ $name }}</span>.
    </p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative">

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Error!</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="text-2xl font-bold text-gray-800 mb-4">Share Your Feedback</h2>
    <p class="text-gray-600 mb-8">
        How was your experience? Your rating helps others make better choices.
    </p>

    <form action="{{ route('customer.rating.store_specific') }}" method="POST" class="space-y-8">
        @csrf
        
        {{-- Hidden Fields for Logic --}}
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="id" value="{{ $type === 'booking' ? $target->bookingID : $target->rentalID }}">

        <div>
            <label class="block text-gray-700 font-medium mb-2">Your Rating</label>
            <div class="flex space-x-2 flex-row-reverse justify-end">
                @for ($i = 5; $i >= 1; $i--)
                    <label class="cursor-pointer group">
                        <input type="radio" name="rating_Score" value="{{ $i }}" class="hidden peer" required>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                             class="w-10 h-10 text-gray-300 peer-checked:text-yellow-400 peer-checked:fill-yellow-400 group-hover:text-yellow-400 group-hover:fill-yellow-400 transition duration-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.062 4.178 4.616.671a.562.562 0 01.312.959l-3.34 3.256.788 4.597a.562.562 0 01-.815.592L12 15.347l-4.138 2.177a.562.562 0 01-.815-.592l.788-4.597-3.34-3.256a.562.562 0 01.312-.959l4.616-.671 2.062-4.178z" />
                        </svg>
                    </label>
                @endfor
            </div>
            <p class="text-sm text-gray-500 mt-2">Select a star rating</p>
        </div>

        <div>
            <label for="review_Given" class="block text-gray-700 font-medium mb-2">Your Review</label>
            <textarea name="review_Given" id="review_Given" rows="5" required
                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-4 text-gray-700"
                      placeholder="Tell us what you liked or didn't like about this experience...">{{ old('review_Given') }}</textarea>
        </div>

        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
            <a href="{{ url()->previous() }}"
               class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium border border-gray-300">
               Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold shadow-md transform hover:scale-105">
                Submit Review
            </button>
        </div>
    </form>
</div>
@endsection