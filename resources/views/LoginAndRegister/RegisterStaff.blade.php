@extends('layout.admin')

@section('title', 'Register Staff - PadangPro Admin')

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold">REGISTER NEW EMPLOYEE</h1>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 md:p-10 max-w-3xl mx-auto -mt-16 relative">
    
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Employee Registration Form</h2>
        <p class="text-gray-500">Create a new account for a staff member or administrator.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.register.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label for="staff_FullName" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="staff_FullName" id="staff_FullName" value="{{ old('staff_FullName') }}" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="staff_Age" class="block text-sm font-medium text-gray-700">Age</label>
                <input type="number" name="staff_Age" id="staff_Age" value="{{ old('staff_Age') }}" required min="18"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label for="staff_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="staff_PhoneNumber" id="staff_PhoneNumber" value="{{ old('staff_PhoneNumber') }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>

        <div>
            <label for="staff_Address" class="block text-sm font-medium text-gray-700">Address</label>
            <input type="text" name="staff_Address" id="staff_Address" value="{{ old('staff_Address') }}" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="user_Email" class="block text-sm font-medium text-gray-700">Staff/Admin Email</label>
                <input type="email" name="user_Email" id="user_Email" value="{{ old('user_Email') }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label for="confirm_email" class="block text-sm font-medium text-gray-700">Confirm Email</label>
                <input type="email" name="confirm_email" id="confirm_email" value="{{ old('confirm_email') }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>

        <div>
            <label for="user_Type" class="block text-sm font-medium text-gray-700">User Type</label>
            <select name="user_Type" id="user_Type" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
                <option value="">Select User Type</option>
                <option value="staff" {{ old('user_Type') == 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="administrator" {{ old('user_Type') == 'administrator' ? 'selected' : '' }}>Administrator</option>
            </select>
        </div>

        <div id="staffJobContainer" style="display: none;">
            <label for="staff_Job" class="block text-sm font-medium text-gray-700">Job Position</label>
            <input type="text" name="staff_Job" id="staff_Job" value="{{ old('staff_Job') }}"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
        </div>

        <div id="staffIdContainer" style="display: none;">
            <label for="staffID" class="block text-sm font-medium text-gray-700">Staff ID (Auto-generated)</label>
            <input type="text" name="staffID" id="staffID" value="{{ $generatedStaffID ?? '' }}" readonly
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
        </div>

        <div id="adminIdContainer" style="display: none;">
            <label for="adminID" class="block text-sm font-medium text-gray-700">Administrator ID (Auto-generated)</label>
            <input type="text" name="adminID" id="adminID" value="{{ $generatedAdminID ?? '' }}" readonly
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
        </div>

        <div class="text-sm text-gray-600 pt-2">
            * A default password will be generated and emailed to the new user upon successful registration.
        </div>

        <div>
            <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-gray-900 bg-amber-400 hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all duration-300">
                Register Employee
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('user_Type');
    const staffJobContainer = document.getElementById('staffJobContainer');
    const staffIdContainer = document.getElementById('staffIdContainer');
    const adminIdContainer = document.getElementById('adminIdContainer');

    function toggleFields() {
        const selectedType = userTypeSelect.value;

        // Hide all conditional fields initially
        staffJobContainer.style.display = 'none';
        staffIdContainer.style.display = 'none';
        adminIdContainer.style.display = 'none';

        if (selectedType === 'staff') {
            staffJobContainer.style.display = 'block';
            staffIdContainer.style.display = 'block';
        } else if (selectedType === 'administrator') {
            adminIdContainer.style.display = 'block';
        }
    }

    userTypeSelect.addEventListener('change', toggleFields);
    
    // Run on page load to set initial state based on old input
    toggleFields(); 
});

// Force refresh on back/forward navigation to ensure correct script execution
window.addEventListener("pageshow", function (event) {
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        window.location.reload();
    }
});
</script>
@endpush