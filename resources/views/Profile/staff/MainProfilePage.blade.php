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

{{-- Width constrained to 4/5 (or 80%) to match your admin page --}}
<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 md:w-4/5 mx-auto -mt-16 relative">

    {{-- Edit Profile Button --}}
    <a href="{{ route('staff.profile.edit') }}" 
       class="absolute top-6 right-6 bg-zinc-700 hover:bg-zinc-800 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition-all transform hover:scale-105">
        <i class="bi bi-pencil-fill mr-2"></i>Edit Profile
    </a>

    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
        
        <div class="flex-shrink-0">
            <div class="w-32 h-32 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center border-4 border-white shadow-md">
                <i class="bi bi-person-fill text-7xl"></i>
            </div>
        </div>

        <div class="flex-grow space-y-4 w-full">
            <h2 class="text-3xl font-bold text-gray-800">{{ $fullName }}</h2>
            <p class="text-lg font-medium text-lime-600 -mt-2">{{ $job }}</p>
            
            {{-- =============================================== --}}
            {{-- NEW: Single column layout for details --}}
            {{-- =============================================== --}}
            <div class="pt-4 border-t">
                <div class="space-y-3">
                    <p class="text-gray-700 text-lg"><strong class="font-semibold w-32 inline-block">Staff ID:</strong> {{ $staffID }}</p>
                    <p class="text-gray-700 text-lg"><strong class="font-semibold w-32 inline-block">Email:</strong> {{ $email }}</p>
                    <p class="text-gray-700 text-lg"><strong class="font-semibold w-32 inline-block">Phone:</strong> {{ $phoneNumber }}</p>
                    <p class="text-gray-700 text-lg"><strong class="font-semibold w-32 inline-block">Age:</strong> {{ $age }}</p>
                    <p class="text-gray-700 text-lg"><strong class="font-semibold w-32 inline-block">Address:</strong> {{ $address }}</p>
                </div>
            </div>
            {{-- =============================================== --}}
        </div>
    </div>
    
    <div class="mt-10 pt-6 border-t border-gray-200">
        <form method="POST" action="{{ route('staff.profile.delete') }}" class="mt-8 text-left" id="deleteForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="user_id" value="{{ session('user_id') }}">
            <button type="button"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md font-semibold transition-all duration-200"
                    id="deleteAccountBtn">Delete Your Account</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 for success message (e.g., from editing) --}}
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

{{-- SweetAlert2 for delete confirmation --}}
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