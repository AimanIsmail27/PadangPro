@extends('layout.staff')

@section('title', 'Edit Staff Profile - PadangPro')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">Edit Profile</h1>
    <p class="mt-2 text-lime-100">Update your personal information and password.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-10 w-11/12 md:w-4/5 mx-auto -mt-16 relative">

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Update Your Information</h2>
        <p class="text-gray-500">Make changes to your profile details below.</p>
    </div>

    {{-- =============================================== --}}
    {{-- Main Profile Update Form --}}
    {{-- =============================================== --}}
    <form action="{{ route('staff.profile.update') }}" method="POST" class="space-y-6">
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

        <div>
            <label for="staff_FullName" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="staff_FullName" id="staff_FullName" value="{{ old('staff_FullName', $staff->staff_FullName) }}" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
        </div>

        <div>
            <label for="user_Email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input type="email" name="user_Email" id="user_Email" value="{{ old('user_Email', $user->user_Email) }}" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="staff_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="staff_PhoneNumber" id="staff_PhoneNumber" value="{{ old('staff_PhoneNumber', $staff->staff_PhoneNumber) }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
            </div>
            <div>
                <label for="staff_Age" class="block text-sm font-medium text-gray-700">Age</label>
                <input type="number" name="staff_Age" id="staff_Age" value="{{ old('staff_Age', $staff->staff_Age) }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
            </div>
        </div>

        <div>
            <label for="staff_Address" class="block text-sm font-medium text-gray-700">Address</label>
            <textarea name="staff_Address" id="staff_Address" rows="4" required
                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">{{ old('staff_Address', $staff->staff_Address) }}</textarea>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t">
            <button type="submit"
                    class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-zinc-700 hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 transition-all">
                Save Changes
            </button>
            <a href="{{ route('staff.profile') }}"
               class="py-2 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                Cancel
            </a>
        </div>
    </form>


    {{-- =============================================== --}}
    {{-- Change Password Form --}}
    {{-- =============================================== --}}
    <div class="mt-12 pt-8 border-t">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Change Your Password</h2>
            <p class="text-gray-500">Update your password below.</p>
        </div>

        {{-- Password text error block is REMOVED --}}

        <form action="{{ route('staff.password.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" name="current_password" id="current_password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="new_password" id="new_password" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
            </div>

            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500">
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit"
                        class="py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 transition-all">
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
            confirmButtonColor: '#166534', // green-800
            confirmButtonText: 'OK'
        });
    </script>
@endif

{{-- =============================================== --}}
{{-- NEW: SweetAlert for PASSWORD errors --}}
{{-- =============================================== --}}
@if ($errors->password->any())
    <script>
        Swal.fire({
            title: 'Password Change Failed',
            // Get all errors from the 'password' bag and join them
            html: `
                <ul classclass="text-left list-disc list-inside">
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