@extends('layout.customer')

@section('title', 'My Profile')

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">My Profile</h1>
    <p class="mt-2 text-indigo-100">Manage your personal information and account settings.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative">

    <a href="{{ route('customer.profile.edit') }}" 
       class="absolute top-6 right-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition-all transform hover:scale-105">
        <i class="bi bi-pencil-fill mr-2"></i>Edit Profile
    </a>

    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
        
        <div class="flex-shrink-0">
            <div class="w-32 h-32 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center border-4 border-white shadow-md">
                <i class="bi bi-person-fill text-7xl"></i>
            </div>
        </div>

        <div class="flex-grow space-y-4">
            <h2 class="text-3xl font-bold text-gray-800">{{ $fullName }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 pt-4 border-t">
                
                <div class="space-y-3">
                    <p class="text-gray-600"><strong class="font-medium text-gray-800 block">Email:</strong> {{ $email }}</p>
                    <p class="text-gray-600"><strong class="font-medium text-gray-800 block">Phone Number:</strong> {{ $phoneNumber }}</p>
                    <p class="text-gray-600"><strong class="font-medium text-gray-800 block">Address:</strong> {{ $address }}</p>
                </div>

                <div class="space-y-3">
                    <p class="text-gray-600"><strong class="font-medium text-gray-800 block">Age:</strong> {{ $age }}</p>
                    <p class="text-gray-600"><strong class="font-medium text-gray-800 block">Preferred Position:</strong> {{ $position }}</p>
                    <p class="text-gray-600"><strong class="font-medium text-gray-800 block">Skill Level (1-5):</strong> {{ $skillLevel }}</p>
                    <p class="text-gray-600"><strong class="font-medium text-gray-800 block">Availability (Days):</strong> {{ $availabilityDays }}</p>
                    <p class="text-gray-600"><strong class="font-medium text-gray-800 block">Availability (Times):</strong> {{ $availabilityTimes }}</p>
                </div>

            </div>
        </div>
    </div>

    <div class="mt-10 pt-6 border-t border-gray-200">
        <form action="{{ route('customer.profile.delete') }}" method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="user_id" value="{{ session('user_id') }}">
            <button type="button" id="deleteAccountBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition-all">
                Delete Your Account
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')

{{-- =============================================== --}}
{{-- THIS SCRIPT DISPLAYS THE SUCCESS POPUP --}}
{{-- =============================================== --}}
@if(session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#312e81', // Your theme's dark color
            confirmButtonText: 'OK'
        });
    </script>
@endif
{{-- =============================================== --}}
{{-- THIS SCRIPT HANDLES ACCOUNT DELETION CONFIRMATION --}}
{{-- =============================================== --}}
<script>
    document.getElementById('deleteAccountBtn').addEventListener('click', function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone. Your account will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        });
    });
</script>
@endpush