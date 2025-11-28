@extends('layout.customer')

@section('title', 'Edit My Profile')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Edit Profile</h1>
    <p class="mt-2 text-indigo-100">Update your personal information and matchmaking settings.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative">

    <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        {{-- Display general validation errors (for THIS form only) --}}
        @if ($errors->default->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->default->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- =============================================== --}}
        {{-- NEW: Profile Picture Upload --}}
        {{-- =============================================== --}}
        <div class="flex items-center gap-6 mb-8 p-4 bg-indigo-50 rounded-xl border border-indigo-100">
            <div class="flex-shrink-0">
                @if($customer->customer_Image)
                    <img src="{{ asset('storage/' . $customer->customer_Image) }}" class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-md">
                @else
                    <div class="w-20 h-20 rounded-full bg-indigo-200 flex items-center justify-center text-indigo-600 text-3xl font-bold">
                        {{ substr($customer->customer_FullName, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="flex-grow">
                <label class="block text-sm font-bold text-indigo-900 mb-1">Profile Photo</label>
                <input type="file" name="customer_Image" accept="image/*"
                       class="block w-full text-sm text-slate-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-indigo-600 file:text-white
                              hover:file:bg-indigo-700
                              cursor-pointer">
                <p class="text-xs text-gray-500 mt-1">JPG, PNG or GIF. Max 2MB.</p>
            </div>
        </div>
        {{-- =============================================== --}}

        <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Personal Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="customer_FullName" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="customer_FullName" id="customer_FullName" value="{{ old('customer_FullName', $customer->customer_FullName) }}" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="user_Email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="user_Email" id="user_Email" value="{{ old('user_Email', $user->user_Email) }}" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="customer_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="customer_PhoneNumber" id="customer_PhoneNumber" value="{{ old('customer_PhoneNumber', $customer->customer_PhoneNumber) }}" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="customer_Age" class="block text-sm font-medium text-gray-700">Age</label>
                <input type="number" name="customer_Age" id="customer_Age" value="{{ old('customer_Age', $customer->customer_Age) }}" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
        </div>
        
        <div>
            <label for="customer_Address" class="block text-sm font-medium text-gray-700">Address</label>
            <textarea name="customer_Address" id="customer_Address" rows="3" 
                      class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('customer_Address', $customer->customer_Address) }}</textarea>
        </div>
        
        <hr class="my-8">
        <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Matchmaking Settings</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="customer_Position" class="block text-sm font-medium text-gray-700">Preferred Football Position</label>
                <input type="text" name="customer_Position" id="customer_Position" value="{{ old('customer_Position', $customer->customer_Position) }}" 
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Striker, Goalkeeper" required>
            </div>
            <div>
                <label for="customer_SkillLevel" class="block text-sm font-medium text-gray-700">Skill Level (1–5)</label>
                <select name="customer_SkillLevel" id="customer_SkillLevel" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="" disabled>-- Select Skill Level --</option>
                    <option value="1" {{ old('customer_SkillLevel', $customer->customer_SkillLevel) == 1 ? 'selected' : '' }}>1 - Beginner</option>
                    <option value="2" {{ old('customer_SkillLevel', $customer->customer_SkillLevel) == 2 ? 'selected' : '' }}>2 - Social Player</option>
                    <option value="3" {{ old('customer_SkillLevel', $customer->customer_SkillLevel) == 3 ? 'selected' : '' }}>3 - Intermediate</option>
                    <option value="4" {{ old('customer_SkillLevel', $customer->customer_SkillLevel) == 4 ? 'selected' : '' }}>4 - Semi Professional</option>
                    <option value="5" {{ old('customer_SkillLevel', $customer->customer_SkillLevel) == 5 ? 'selected' : '' }}>5 - Professional</option>
                </select>
            </div>
        </div>

        @php
            $availability = json_decode($customer->customer_Availability, true) ?? ['days' => [], 'time' => []];
            $selectedDays = old('customer_Availability_days', $availability['days']);
            $selectedTimes = old('customer_Availability_times', $availability['time']);
        @endphp

        <div>
            <label class="block text-sm font-medium text-gray-700">Availability - Days</label>
            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                    <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 transition">
                        <input type="checkbox" name="customer_Availability_days[]" value="{{ $day }}" 
                               class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                               {{ in_array($day, $selectedDays) ? 'checked' : '' }}>
                        <span>{{ $day }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Availability - Times</label>
            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach (['Morning','Afternoon','Evening','Night'] as $time)
                    <label class="flex items-center space-x-2 p-3 border rounded-lg hover:bg-gray-50 transition">
                        <input type="checkbox" name="customer_Availability_times[]" value="{{ $time }}" 
                               class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                               {{ in_array($time, $selectedTimes) ? 'checked' : '' }}>
                        <span>{{ $time }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-4 pt-6 border-t">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition-all transform hover:scale-105">
                Save Changes
            </button>
            <a href="{{ route('customer.profile') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                Cancel
            </a>
        </div>
    </form>

    {{-- =============================================== --}}
    {{-- NEW: Change Password Form --}}
    {{-- =============================================== --}}
    <div class="mt-12 pt-8 border-t">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Change Your Password</h2>
            <p class="text-gray-500">Update your password below.</p>
        </div>

        <form action="{{ route('customer.password.update') }}" method="POST" class="space-y-6 max-w-lg mx-auto">
            @csrf
            
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" name="current_password" id="current_password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="new_password" id="new_password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit"
                        class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    Change Password
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for success messages --}}
@if(session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#4f46e5', // Indigo-600
            confirmButtonText: 'OK'
        });
    </script>
@endif

{{-- SweetAlert for PASSWORD errors --}}
@if ($errors->password->any())
    <script>
        Swal.fire({
            title: 'Password Change Failed',
            html: `
                <ul class="text-left list-disc list-inside">
                    @foreach ($errors->password->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Try Again'
        });
    </script>
@endif
@endpush