@extends('layout.customer')

@section('title', 'Edit Matchmaking Advertisement')

@section('content')
    <!-- Page Header -->
    <div class="bg-[#1E2A78] rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
        <div class="text-white font-bold text-2xl px-8">
            Edit Matchmaking Advertisement
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white shadow-2xl rounded-3xl p-10 max-w-4xl mx-auto space-y-8">
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-lg text-center font-medium">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('matchmaking.update', $ad->adsID) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Ad Name -->
            <div>
                <label for="ads_Name" class="block text-gray-700 font-semibold mb-2">Ad Title <span class="text-red-500">*</span></label>
                <input type="text" id="ads_Name" name="ads_Name" 
                       value="{{ old('ads_Name', $ad->ads_Name) }}"
                       class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition"
                       placeholder="e.g., Looking for Striker">
                @error('ads_Name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ad Type (readonly) -->
            <div>
                <label for="ads_Type" class="block text-gray-700 font-semibold mb-2">Ad Type</label>
                <input type="text" id="ads_Type" name="ads_Type"
                       value="{{ $ad->ads_Type }}"
                       readonly
                       class="w-full px-5 py-3 border bg-gray-100 text-gray-600 rounded-xl shadow-sm">
            </div>

            <!-- Price -->
            <div>
                <label for="ads_Price" class="block text-gray-700 font-semibold mb-2">Price (Optional)</label>
                <input type="number" id="ads_Price" name="ads_Price" 
                       value="{{ old('ads_Price', $ad->ads_Price) }}"
                       class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition"
                       placeholder="e.g., 50">
                @error('ads_Price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="ads_Description" class="block text-gray-700 font-semibold mb-2">Description <span class="text-red-500">*</span></label>
                <textarea id="ads_Description" name="ads_Description" rows="5"
                          class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition"
                          placeholder="Provide details about your ad...">{{ old('ads_Description', $ad->ads_Description) }}</textarea>
                @error('ads_Description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Conditional: Required Positions -->
            @if($ad->ads_Type === 'Additional Player')
                <div id="positionField">
                    <label class="block text-gray-700 font-semibold mb-3">Required Position <span class="text-red-500">*</span></label>
                    @php
                        $positions = ['GK','RB','LB','CB','CDM','CM','CAM','RM','LM','ST','CF'];
                        $selectedPositions = old('ads_RequiredPosition', $ad->ads_RequiredPosition ?? []);
                    @endphp
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-3 p-4 border border-gray-200 rounded-2xl bg-gray-50">
                        @foreach($positions as $pos)
                            <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-blue-50 transition">
                                <input type="checkbox" name="ads_RequiredPosition[]" value="{{ $pos }}"
                                       class="h-5 w-5 text-blue-600"
                                       {{ in_array($pos, $selectedPositions) ? 'checked' : '' }}>
                                <span class="font-medium text-gray-700">{{ $pos }}</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-gray-400 text-sm mt-1">Select one or more positions.</p>
                    @error('ads_RequiredPosition')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Conditional: Maximum Players -->
                <div id="maxPlayersField">
                    <label for="ads_MaxPlayers" class="block text-gray-700 font-semibold mb-2">Maximum Players <span class="text-red-500">*</span></label>
                    <input type="number" id="ads_MaxPlayers" name="ads_MaxPlayers" 
                           value="{{ old('ads_MaxPlayers', $ad->ads_MaxPlayers) }}"
                           class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition"
                           min="1" placeholder="e.g., 2">
                    @error('ads_MaxPlayers')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Slot Time -->
            <div>
                <label for="ads_SlotTime" class="block text-gray-700 font-semibold mb-2">Slot Time <span class="text-red-500">*</span></label>
                <input type="datetime-local" id="ads_SlotTime" name="ads_SlotTime" 
                       value="{{ old('ads_SlotTime', \Carbon\Carbon::parse($ad->ads_SlotTime)->format('Y-m-d\TH:i')) }}"
                       class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition">
                @error('ads_SlotTime')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                        class="bg-gradient-to-r from-[#1E2A78] to-blue-600 text-white px-8 py-3 rounded-2xl shadow-xl hover:opacity-90 transition font-semibold text-lg">
                    Update Advertisement
                </button>
            </div>
        </form>
    </div>
@endsection
