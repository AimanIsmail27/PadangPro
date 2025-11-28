@extends('layout.admin')

@section('title', 'Admin Profile - PadangPro')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-3xl font-bold">My Profile</h1>
    <p class="mt-2 text-amber-100">Manage your personal information and account settings.</p>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 md:p-10 w-11/12 md:w-4/5 mx-auto -mt-16 relative">

    {{-- PROFILE SECTION --}}
    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">

        <div class="flex-shrink-0">
            @if($adminPhoto)
                <img src="{{ asset('storage/' . $adminPhoto) }}"
                    alt="Profile Photo"
                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md cursor-pointer hover:scale-105 transition-transform"
                    onclick="viewImage('{{ asset('storage/' . $adminPhoto) }}')">
            @else
                <div class="w-32 h-32 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center border-4 border-white shadow-md">
                    <i class="bi bi-person-fill text-7xl"></i>
                </div>
            @endif
        </div>


        <div class="flex-grow space-y-4 w-full text-center md:text-left">
            <h2 class="text-3xl font-bold text-gray-800">{{ $fullName }}</h2>
            <p class="text-lg font-medium text-amber-600 -mt-2">Administrator</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 pt-4 border-t text-left">
                <div class="space-y-3">
                    <p class="text-gray-600">
                        <strong class="font-medium text-gray-800 block">Email:</strong> {{ $email }}
                    </p>
                    <p class="text-gray-600">
                        <strong class="font-medium text-gray-800 block">Phone Number:</strong> {{ $phoneNumber }}
                    </p>
                </div>

                <div class="space-y-3">
                    <p class="text-gray-600">
                        <strong class="font-medium text-gray-800 block">Age:</strong> {{ $age }}
                    </p>
                    <p class="text-gray-600">
                        <strong class="font-medium text-gray-800 block">Address:</strong> {{ $address }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- BOTTOM ACTION AREA --}}
    <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col gap-4">

        {{-- EDIT BUTTON (mobile stacked, desktop absolute top-right) --}}
        <a href="{{ route('admin.profile.edit') }}"
           class="w-full md:w-auto text-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition-all transform hover:scale-105 md:absolute md:top-10 md:right-10">
            <i class="bi bi-pencil-fill mr-2"></i>Edit Profile
        </a>

        {{-- DELETE ACCOUNT --}}
        <form method="POST" action="{{ route('admin.profile.delete') }}" id="deleteForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="user_id" value="{{ session('user_id') }}">
            <button type="button"
                    id="deleteAccountBtn"
                    class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition-all">
                Delete Your Account
            </button>
        </form>

    </div>

</div>
@endsection

@push('scripts')
@if(session('success'))
<script>
    Swal.fire({
        title: 'Success!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonColor: '#f59e0b',
        confirmButtonText: 'OK'
    });
</script>
@endif

<script>
function viewImage(imageUrl) {
    Swal.fire({
        imageUrl: imageUrl,
        imageAlt: 'Profile Photo',
        showConfirmButton: false,
        showCloseButton: true,
        background: 'transparent',
        backdrop: `rgba(0,0,0,0.8)`,
        padding: 0,
        customClass: { image: 'rounded-lg shadow-2xl' }
    });
}


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
