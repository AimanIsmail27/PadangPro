@extends('layout.staff')

@section('title', 'My Profile')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-lime-500 to-emerald-600 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">My Profile</h1>
    <p class="mt-2 text-lime-100">Manage your personal information and account settings.</p>
</div>

{{-- Main container (keeps desktop width like original) --}}
<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 md:w-4/5 mx-auto -mt-16 relative">

    {{-- DESKTOP: Edit Profile Button --}}
    <a href="{{ route('staff.profile.edit') }}"
       class="hidden md:block absolute top-6 right-6 bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition-all transform hover:scale-105">
        <i class="bi bi-pencil-fill mr-2"></i>Edit Profile
    </a>

    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">

        {{-- PROFILE PHOTO (clickable) --}}
        <div class="flex-shrink-0">
            @if($staff->staff_image)
                <button id="profileImageBtn" type="button" class="p-0 border-none bg-transparent">
                    <img src="{{ asset('storage/' . $staff->staff_image) }}" 
                         alt="Profile image" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md cursor-pointer">
                </button>
            @else
                <button id="profileImageBtn" type="button" class="p-0 border-none bg-transparent">
                    <div class="w-32 h-32 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center border-4 border-white shadow-md cursor-pointer">
                        <i class="bi bi-person-fill text-7xl"></i>
                    </div>
                </button>
            @endif
        </div>

        {{-- PROFILE INFORMATION --}}
        <div class="flex-grow space-y-4 w-full">

            <h2 class="text-3xl font-bold text-gray-800">{{ $fullName }}</h2>
            <p class="text-lg font-medium text-lime-600 -mt-2">{{ $job }}</p>

            {{-- DESKTOP: inline/details layout --}}
            <div class="hidden md:block pt-4 border-t">
                <div class="space-y-3">
                    <p class="text-gray-700 text-lg">
                        <strong class="font-semibold w-32 inline-block">Staff ID:</strong>
                        {{ $staffID }}
                    </p>
                    <p class="text-gray-700 text-lg">
                        <strong class="font-semibold w-32 inline-block">Email:</strong>
                        {{ $email }}
                    </p>
                    <p class="text-gray-700 text-lg">
                        <strong class="font-semibold w-32 inline-block">Phone:</strong>
                        {{ $phoneNumber }}
                    </p>
                    <p class="text-gray-700 text-lg">
                        <strong class="font-semibold w-32 inline-block">Age:</strong>
                        {{ $age }}
                    </p>
                    <p class="text-gray-700 text-lg">
                        <strong class="font-semibold w-32 inline-block">Address:</strong>
                        {{ $address }}
                    </p>
                </div>
            </div>

            {{-- MOBILE: stacked layout --}}
            <div class="md:hidden pt-4 border-t">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <p class="text-gray-800 font-semibold text-lg">Staff ID</p>
                        <p class="text-gray-600 text-lg mt-1">{{ $staffID }}</p>
                    </div>

                    <div>
                        <p class="text-gray-800 font-semibold text-lg">Email</p>
                        <p class="text-gray-600 text-lg mt-1">{{ $email }}</p>
                    </div>

                    <div>
                        <p class="text-gray-800 font-semibold text-lg">Phone Number</p>
                        <p class="text-gray-600 text-lg mt-1">{{ $phoneNumber }}</p>
                    </div>

                    <div>
                        <p class="text-gray-800 font-semibold text-lg">Age</p>
                        <p class="text-gray-600 text-lg mt-1">{{ $age }}</p>
                    </div>

                    <div>
                        <p class="text-gray-800 font-semibold text-lg">Address</p>
                        <p class="text-gray-600 text-lg mt-1">{{ $address }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ACTION AREA --}}
    <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col gap-4">

        {{-- MOBILE ONLY: Blue Edit button (on top of delete) --}}
        <a href="{{ route('staff.profile.edit') }}" 
           class="md:hidden block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition-all transform hover:scale-105">
            <i class="bi bi-pencil-fill mr-2"></i>Edit Profile
        </a>

        {{-- DELETE BUTTON --}}
        <form method="POST" action="{{ route('staff.profile.delete') }}" id="deleteForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="user_id" value="{{ session('user_id') }}">

            <button type="button"
                    id="deleteAccountBtn"
                    class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md font-semibold transition-all duration-200">
                Delete Your Account
            </button>
        </form>

    </div>

</div>
@endsection

@push('scripts')
{{-- Success toast/modal --}}
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

{{-- Delete confirmation --}}
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

{{-- Profile Image Lightbox --}}
<script>
document.getElementById('profileImageBtn').addEventListener('click', function () {
    @if($staff->staff_image)
        Swal.fire({
            imageUrl: '{{ asset('storage/' . $staff->staff_image) }}',
            imageAlt: 'Profile Image',
            showCloseButton: true,
            showConfirmButton: false,
            background: '#f9fafb',
            width: 'auto',
            customClass: {
                image: 'max-h-[80vh] max-w-full object-contain'
            }
        });
    @else
        Swal.fire({
            html: `<div class="w-48 h-48 flex items-center justify-center bg-gray-200 rounded-full">
                       <i class="bi bi-person-fill text-7xl text-gray-400"></i>
                   </div>`,
            showCloseButton: true,
            showConfirmButton: false,
            background: '#f9fafb'
        });
    @endif
});
</script>
@endpush
