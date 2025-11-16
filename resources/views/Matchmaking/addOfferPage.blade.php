@extends('layout.customer')

@section('title', 'Create Matchmaking Advertisement')

@section('content')
    <div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white rounded-lg h-[120px] relative shadow-md mb-8 flex items-center">
        <div class="text-white font-bold text-2xl px-8">
            Create New Matchmaking Advertisement
        </div>
    </div>

<div class="bg-white shadow-2xl rounded-3xl p-10 max-w-4xl mx-auto space-y-8">
    
    {{-- Session Error Display --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded-lg text-center font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- =============================================== --}}
    {{-- NEW: THIS BLOCK WILL DISPLAY VALIDATION ERRORS --}}
    {{-- =============================================== --}}
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
    {{-- =============================================== --}}

    <form action="{{ route('matchmaking.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="ads_Name" class="block text-gray-700 font-semibold mb-2">Ad Title <span class="text-red-500">*</span></label>
            <input type="text" id="ads_Name" name="ads_Name" value="{{ old('ads_Name') }}"
                   class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                   placeholder="e.g., Looking for Striker or Friendly Match vs Us" required>
        </div>

        <div>
            <label for="ads_Type" class="block text-gray-700 font-semibold mb-2">Ad Type <span class="text-red-500">*</span></label>
            <select id="ads_Type" name="ads_Type" required
                    class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition">
                <option value="" disabled {{ old('ads_Type') ? '' : 'selected' }}>-- Select Ad Type --</option>
                <option value="Additional Player" {{ old('ads_Type')=='Additional Player' ? 'selected' : '' }}>I'm looking for players to join my team</option>
                <option value="Opponent Search" {{ old('ads_Type')=='Opponent Search' ? 'selected' : '' }}>My team is looking for an opponent</option>
            </select>
        </div>

        <div>
            <label for="ads_SlotTime" class="block text-gray-700 font-semibold mb-2">Match Date & Time <span class="text-red-500">*</span></label>
            <input type="datetime-local" id="ads_SlotTime" name="ads_SlotTime" value="{{ old('ads_SlotTime') }}" required
                   class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition">
        </div>

        <div>
            <label for="ads_Price" class="block text-gray-700 font-semibold mb-2">Price (per player or team)</label>
            <input type="number" id="ads_Price" name="ads_Price" value="{{ old('ads_Price', 0) }}"
                   class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                   placeholder="e.g., 10 (Enter 0 if free)">
        </div>

        <hr>
        
        <div id="aiMatchingFields" class="space-y-6" style="display: none;">
            <div>
                <label for="ads_TargetSkillLevel" class="block text-gray-700 font-semibold mb-2">Desired Skill Level (1-5) <span class="text-red-500">*</span></label>
                <input type="number" id="ads_TargetSkillLevel" name="ads_TargetSkillLevel" value="{{ old('ads_TargetSkillLevel') }}"
                       class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                       min="1" max="5" placeholder="Enter the skill level you are looking for (1-5)">
                <p class="text-gray-400 text-sm mt-1">E.g., if you are Skill 4 but want a fun game, you can look for Skill 2.</p>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Match Intensity <span class="text-red-500">*</span></label>
                <div class="flex gap-6 p-4 border border-gray-200 rounded-2xl bg-gray-50">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="ads_MatchIntensity" value="Fun" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500" {{ old('ads_MatchIntensity') == 'Fun' ? 'checked' : '' }}>
                        <span class="font-medium text-gray-700">Fun / Casual</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="ads_MatchIntensity" value="Competitive" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500" {{ old('ads_MatchIntensity') == 'Competitive' ? 'checked' : '' }}>
                        <span class="font-medium text-gray-700">Competitive</span>
                    </label>
                </div>
            </div>
        </div>

        <div id="additionalPlayerFields" class="space-y-6" style="display: none;">
            <div id="positionField">
                <label class="block text-gray-700 font-semibold mb-3">Required Position(s) <span class="text-red-500">*</span></label>
                @php $positions = ['GK','RB','LB','CB','CDM','CM','CAM','RM','LM','ST','CF']; $oldPositions = old('ads_RequiredPosition', []); @endphp
                <div class="grid grid-cols-3 md:grid-cols-4 gap-3 p-4 border border-gray-200 rounded-2xl bg-gray-50">
                    @foreach($positions as $pos)
                        <label class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-indigo-50 transition">
                            <input type="checkbox" name="ads_RequiredPosition[]" value="{{ $pos }}"
                                   class="h-5 w-5 text-indigo-600 focus:ring-indigo-500" {{ in_array($pos, $oldPositions) ? 'checked' : '' }}>
                            <span class="font-medium text-gray-700">{{ $pos }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div id="maxPlayersField">
                <label for="ads_MaxPlayers" class="block text-gray-700 font-semibold mb-2">Players to Recruit <span class="text-red-500">*</span></label>
                <input type="number" id="ads_MaxPlayers" name="ads_MaxPlayers" value="{{ old('ads_MaxPlayers') }}"
                       class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                       min="1" placeholder="e.g., 2">
            </div>
        </div>

        <div>
            <label for="ads_Description" class="block text-gray-700 font-semibold mb-2">Description <span class="text-red-500">*</span></label>
            <textarea id="ads_Description" name="ads_Description" rows="5" required
                      class="w-full px-5 py-3 border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-400 transition"
                      placeholder="Provide details about your ad...">{{ old('ads_Description') }}</textarea>
        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="submit"
                    class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white px-8 py-3 rounded-xl shadow-lg hover:opacity-90 transition font-semibold text-lg">
                Post Advertisement
            </button>
        </div>
    </form>
</div>



<script>
    const adsTypeSelect = document.getElementById('ads_Type');
    const aiMatchingFields = document.getElementById('aiMatchingFields');
    const additionalPlayerFields = document.getElementById('additionalPlayerFields');

    // Get the inputs that we need to make required/not required
    const targetSkillInput = document.getElementById('ads_TargetSkillLevel');
    const maxPlayersInput = document.getElementById('ads_MaxPlayers');
    // Find the radio buttons
    const intensityRadios = document.querySelectorAll('input[name="ads_MatchIntensity"]');
    
    function toggleFields() {
        const selectedType = adsTypeSelect.value;
        
        // Hide all conditional fields first
        aiMatchingFields.style.display = 'none';
        additionalPlayerFields.style.display = 'none';

        // Set all conditional inputs to 'not required'
        targetSkillInput.required = false;
        maxPlayersInput.required = false;
        intensityRadios.forEach(radio => radio.required = false);

        if (selectedType === 'Additional Player') {
            aiMatchingFields.style.display = 'block';
            additionalPlayerFields.style.display = 'block';
            
            // Set fields for "Additional Player" as required
            targetSkillInput.required = true;
            maxPlayersInput.required = true;
            intensityRadios[0].required = true; // Make at least one radio button required
            
        } else if (selectedType === 'Opponent Search') {
            aiMatchingFields.style.display = 'block';
            
            // Set fields for "Opponent Search" as required
            targetSkillInput.required = true;
            intensityRadios[0].required = true;
        }
    }

    adsTypeSelect.addEventListener('change', toggleFields);
    toggleFields();
</script>

@if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonColor: '#312e81',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('matchmaking.personal') }}";
        }
    });
</script>
@endif
@endsection