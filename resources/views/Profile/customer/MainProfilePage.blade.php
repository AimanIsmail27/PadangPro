@extends('layout.customer')

@section('title', 'My Profile')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endpush

@section('content')

<div class="bg-gradient-to-r from-indigo-600 to-slate-800 text-white pt-8 pb-24 px-10 rounded-lg shadow-2xl">
    <h1 class="text-3xl font-bold">My Profile</h1>
    <p class="mt-2 text-indigo-100">Manage your personal information and account settings.</p>
</div>

<div class="bg-white rounded-xl shadow-xl border border-gray-100 p-8 md:p-10 w-11/12 mx-auto -mt-16 relative">

    {{-- 
        NOTE: The "Edit Profile" button is physically located at the bottom (in HTML) 
        so it stacks correctly on mobile, but we force it to the top-right on Desktop 
        using 'md:absolute'.
    --}}

    <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
        
        {{-- PROFILE IMAGE SECTION --}}
        <div class="flex-shrink-0">
            @if(isset($customer) && $customer->customer_Image)
                <img src="{{ asset('storage/' . $customer->customer_Image) }}" 
                     alt="Profile Photo" 
                     class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md cursor-pointer hover:scale-105 transition-transform"
                     onclick="viewImage('{{ asset('storage/' . $customer->customer_Image) }}')">
            @else
                <div class="w-32 h-32 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center border-4 border-white shadow-md text-4xl font-bold uppercase">
                    {{ substr($fullName ?? 'U', 0, 1) }}
                </div>
            @endif
        </div>

        <div class="flex-grow space-y-4 w-full text-center md:text-left">
            <h2 class="text-3xl font-bold text-gray-800">{{ $fullName }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 pt-4 border-t w-full text-left">
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

    {{-- BOTTOM ACTIONS AREA --}}
    <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col gap-4">
        
        {{-- 
            Edit Button:
            - Mobile: Block element, stacks above Delete.
            - Desktop: Absolute position (Top Right corner of the card).
        --}}
        <a href="{{ route('customer.profile.edit') }}" 
           class="w-full md:w-auto text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition-all transform hover:scale-105 md:absolute md:top-10 md:right-10">
            <i class="bi bi-pencil-fill mr-2"></i>Edit Profile
        </a>

        {{-- Delete Button --}}
        <form action="{{ route('customer.profile.delete') }}" method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <input type="hidden" name="user_id" value="{{ session('user_id') }}">
            <button type="button" id="deleteAccountBtn" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition-all">
                Delete Your Account
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#312e81', 
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