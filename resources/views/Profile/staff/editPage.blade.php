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

    {{-- Main Profile Update Form --}}
    <form action="{{ route('staff.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Display general validation errors --}}
        @if ($errors->default->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->default->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Profile Picture Upload --}}
        <div class="text-center">
            <div class="flex flex-col items-center gap-3">

                {{-- CURRENT PHOTO --}}
                <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-md mx-auto md:mx-0 flex items-center justify-center bg-slate-200">
                    @if($staff->staff_image && file_exists(storage_path('app/public/' . $staff->staff_image)))
                        <button type="button" id="profileImageBtn" class="p-0 border-none bg-transparent w-full h-full">
                            <img id="previewImage" src="{{ asset('storage/' . $staff->staff_image) }}"
                                 class="w-full h-full object-cover block cursor-pointer">
                        </button>
                    @else
                        <button type="button" id="profileImageBtn" class="p-0 border-none bg-transparent w-full h-full flex items-center justify-center">
                            <i class="bi bi-person-fill text-7xl text-gray-400"></i>
                        </button>
                    @endif
                </div>

                {{-- FILE INPUT --}}
                <label class="cursor-pointer bg-lime-600 hover:bg-lime-700 text-white px-4 py-2 rounded-lg shadow transition">
                    <i class="bi bi-upload mr-1"></i> Upload New Photo
                    <input type="file" name="staff_image" id="staff_image" class="hidden" accept="image/*">
                </label>

                <p class="text-gray-500 text-sm">Accepted formats: JPG, PNG. Max size: 2MB</p>
            </div>
        </div>

        <div>
            <label for="staff_FullName" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="staff_FullName" id="staff_FullName" value="{{ old('staff_FullName', $staff->staff_FullName) }}" required
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-lime-500 focus:border-lime-500">
        </div>

        <div>
            <label for="user_Email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input type="email" name="user_Email" id="user_Email" value="{{ old('user_Email', $user->user_Email) }}" required
                   class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm border-gray-300 focus:ring-lime-500 focus:border-lime-500">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="staff_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="staff_PhoneNumber" id="staff_PhoneNumber" value="{{ old('staff_PhoneNumber', $staff->staff_PhoneNumber) }}" required
                       class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm border-gray-300 focus:ring-lime-500 focus:border-lime-500">
            </div>
            <div>
                <label for="staff_Age" class="block text-sm font-medium text-gray-700">Age</label>
                <input type="number" name="staff_Age" id="staff_Age" value="{{ old('staff_Age', $staff->staff_Age) }}" required
                       class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm border-gray-300 focus:ring-lime-500 focus:border-lime-500">
            </div>
        </div>

        <div>
            <label for="staff_Address" class="block text-sm font-medium text-gray-700">Address</label>
            <textarea name="staff_Address" id="staff_Address" rows="4" required
                      class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm border-gray-300 focus:ring-lime-500 focus:border-lime-500">{{ old('staff_Address', $staff->staff_Address) }}</textarea>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t mt-6">
            <button type="submit"
                class="py-2 px-6 rounded-md shadow-sm text-sm font-medium text-white bg-zinc-700 hover:bg-zinc-800 transition">
                Save Changes
            </button>

            <a href="{{ route('staff.profile') }}"
               class="py-2 px-6 rounded-md border border-gray-300 shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>

    {{-- Password Change Section --}}
    <div class="mt-12 pt-8 border-t">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Change Your Password</h2>
            <p class="text-gray-500">Update your password below.</p>
        </div>

        <form action="{{ route('staff.password.update') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" name="current_password" required
                    class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm border-gray-300 focus:ring-lime-500 focus:border-lime-500">
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="new_password" required
                    class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm border-gray-300 focus:ring-lime-500 focus:border-lime-500">
            </div>

            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" required
                    class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm border-gray-300 focus:ring-lime-500 focus:border-lime-500">
            </div>

            <button type="submit"
                class="py-2 px-6 rounded-md shadow-sm text-sm font-medium text-white bg-lime-600 hover:bg-lime-700 transition">
                Change Password
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')

{{-- SweetAlert success --}}
@if(session('success'))
<script>
Swal.fire({
    title: 'Success!',
    text: "{{ session('success') }}",
    icon: 'success',
    confirmButtonColor: '#166534'
});
</script>
@endif

{{-- SweetAlert password errors --}}
@if ($errors->password->any())
<script>
Swal.fire({
    title: 'Password Change Failed',
    html: `{!! implode('<br>', $errors->password->all()) !!}`,
    icon: 'error',
    confirmButtonColor: '#d33'
});
</script>
@endif

{{-- Image Preview & Click-to-Zoom --}}
<script>
const previewImage = document.getElementById('previewImage');
const fileInput = document.getElementById('staff_image');
const profileBtn = document.getElementById('profileImageBtn');

fileInput.addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        previewImage.src = e.target.result;
    };
    reader.readAsDataURL(file);
});

profileBtn.addEventListener('click', function () {
    @if($staff->staff_image && file_exists(storage_path('app/public/' . $staff->staff_image)))
    Swal.fire({
        imageUrl: previewImage.src,
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
