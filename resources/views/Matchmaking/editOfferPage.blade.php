@extends('layout.customer')

@section('title', 'Edit Matchmaking Advertisement')

@section('content')
    <div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
        <div class="text-white font-bold text-2xl px-8">
            Edit Matchmaking Advertisement
        </div>
    </div>

<div class="bg-white shadow-2xl rounded-3xl p-10 max-w-4xl mx-auto space-y-8">
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-lg text-center font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- NEW: THIS BLOCK WILL DISPLAY ALL VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">Please fix the following issues:</span>
            <ul class="mt-3 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('matchmaking.update', $ad->adsID) }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="ads_Name" class="block text-gray-700 font-semibold mb-2">Ad Title <span class="text-red-500">*</span></label>
            <input type="text" id="ads_Name" name="ads_Name" 
                   value="{{ old('ads_Name', $ad->ads_Name) }}"
                   class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                   placeholder="e.g., Looking for Striker" required>
        </div>

        <div>
            <label for="ads_Type" class="block text-gray-700 font-semibold mb-2">Ad Type</label>
            <input type="text" id="ads_Type" name="ads_Type_display"
                   value="{{ $ad->ads_Type }}"
                   readonly
                   class="w-full px-5 py-3 border bg-gray-100 text-gray-600 rounded-xl shadow-sm">
        </div>

        <div>
            <label for="ads_SlotTime" class="block text-gray-700 font-semibold mb-2">Match Date & Time <span class="text-red-500">*</span></label>
            <input type="datetime-local" id="ads_SlotTime" name="ads_SlotTime" 
                   value="{{ old('ads_SlotTime', \Carbon\Carbon::parse($ad->ads_SlotTime)->format('Y-m-d\TH:i')) }}"
                   class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition" required>
        </div>

        <div>
            <label for="ads_Price" class="block text-gray-700 font-semibold mb-2">Price (per player or team)</label>
            <input type="number" id="ads_Price" name="ads_Price" 
                   value="{{ old('ads_Price', $ad->ads_Price) }}"
                   class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                   placeholder="e.g., 10 (Enter 0 if free)">
        </div>

        <hr>

        <div class="space-y-6">
            <div>
                <label for="ads_TargetSkillLevel" class="block text-gray-700 font-semibold mb-2">Desired Skill Level (1-5) <span class="text-red-500">*</span></label>
                <input type="number" id="ads_TargetSkillLevel" name="ads_TargetSkillLevel" 
                       value="{{ old('ads_TargetSkillLevel', $ad->ads_TargetSkillLevel) }}"
                       class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                       min="1" max="5" placeholder="Enter the skill level you are looking for (1-5)" required>
                <p class="text-gray-400 text-sm mt-1">E.g., if you are Skill 4 but want a fun game, you can look for Skill 2.</p>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Match Intensity <span class="text-red-500">*</span></label>
                <div class="flex gap-6 p-4 border border-gray-200 rounded-2xl bg-gray-50">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="ads_MatchIntensity" value="Fun" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500" 
                               {{ (old('ads_MatchIntensity', $ad->ads_MatchIntensity) == 'Fun') ? 'checked' : '' }} required>
                        <span class="font-medium text-gray-700">Fun / Casual</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="ads_MatchIntensity" value="Competitive" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500" 
                               {{ (old('ads_MatchIntensity', $ad->ads_MatchIntensity) == 'Competitive') ? 'checked' : '' }} required>
                        <span class="font-medium text-gray-700">Competitive</span>
                    </label>
                </div>
            </div>
        </div>

        @if($ad->ads_Type === 'Additional Player')
            <div class="space-y-6">
                <div id="positionField">
                    <label class="block text-gray-700 font-semibold mb-3">Required Position(s) <span class="text-red-500">*</span></label>
                    @php
                        $positions = ['GK','RB','LB','CB','CDM','CM','CAM','RM','LM','ST','CF'];
                        $selectedPositions = old('ads_RequiredPosition', $ad->ads_RequiredPosition ?? []);
                    @endphp
                    <div class="grid grid-cols-3 md:grid-cols-4 gap-3 p-4 border border-gray-200 rounded-2xl bg-gray-50">
                        @foreach($positions as $pos)
                            <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-indigo-50 transition">
                                <input type="checkbox" name="ads_RequiredPosition[]" value="{{ $pos }}"
                                       class="h-5 w-5 text-indigo-600 focus:ring-indigo-500" 
                                       {{ in_array($pos, $selectedPositions) ? 'checked' : '' }}>
                                <span class="font-medium text-gray-700">{{ $pos }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div id="maxPlayersField">
                    <label for="ads_MaxPlayers" class="block text-gray-700 font-semibold mb-2">Players to Recruit <span class="text-red-500">*</span></label>
                    <input type="number" id="ads_MaxPlayers" name="ads_MaxPlayers" 
                           value="{{ old('ads_MaxPlayers', $ad->ads_MaxPlayers) }}"
                           class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                           min="1" placeholder="e.g., 2" required>
                </div>
            </div>
        @endif

        <div>
            <label for="ads_Description" class="block text-gray-700 font-semibold mb-2">Description <span class="text-red-500">*</span></label>
            <textarea id="ads_Description" name="ads_Description" rows="5" required
                      class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                      placeholder="Provide details about your ad...">{{ old('ads_Description', $ad->ads_Description) }}</textarea>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="submit"
                    class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white px-8 py-3 rounded-xl shadow-lg hover:opacity-90 transition font-semibold text-lg">
                Update Advertisement
            </button>
        </div>
    </form>
</div>
@endsection