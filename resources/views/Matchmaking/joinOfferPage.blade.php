@extends('layout.customer')

@section('content')

<!-- Blue Header -->
<div class="bg-[#1E2A78] rounded-lg h-[120px] relative shadow-md">
    <div class="text-white font-bold text-xl px-8 py-6">
        RENT ITEM
        
    </div>
</div>
<div class="container mx-auto max-w-lg mt-6">
    <h2 class="text-2xl font-bold mb-4">Request to Join</h2>

    <div class="bg-white p-6 rounded-xl shadow-md border">
        <p class="mb-3 text-gray-700">
            You are requesting to join the advertisement: 
            <span class="font-semibold">{{ $ad->ads_Name ?? 'Untitled Ad' }}</span>
        </p>

        <form method="POST" action="{{ route('matchmaking.joinStore', $ad->adsID) }}">
            @csrf

            <div class="mb-4">
                <label for="note" class="block text-sm font-medium text-gray-700">Your Note</label>
                <textarea id="note" name="note" rows="4" required
                          class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"></textarea>
                @error('note')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" 
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md">
                Submit Request
            </button>
        </form>
    </div>
</div>
@endsection
