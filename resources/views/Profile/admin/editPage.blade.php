@extends('layout.admin')

@section('title', 'Edit Admin Profile - PadangPro')

@section('content')

<div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 text-white pt-8 pb-24 px-10 rounded-lg shadow-lg">
    <h1 class="text-2xl md:text-3xl font-bold">Edit Admin Profile</h1>
    <p class="mt-2 text-amber-100">Update your personal information and account settings.</p>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 md:p-10 w-11/12 md:w-4/5 mx-auto -mt-16 relative">

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Display validation errors --}}
        @if ($errors->default->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->default->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Profile Photo --}}
        <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 mb-8 p-4 bg-amber-50 rounded-xl border border-amber-100">
            <div class="flex-shrink-0">
                @if($admin->admin_Photo)
                    <img src="{{ asset('storage/' . $admin->admin_Photo) }}" 
                         class="w-24 h-24 rounded-full object-cover border-2 border-white shadow-md cursor-pointer"
                         onclick="viewImage('{{ asset('storage/' . $admin->admin_Photo) }}')">
                @else
                    <div class="w-24 h-24 rounded-full bg-amber-200 flex items-center justify-center text-amber-700 text-3xl font-bold">
                        {{ substr($admin->admin_FullName, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="flex-grow w-full">
                <label class="block text-sm font-bold text-amber-900 mb-1">Profile Photo</label>
                <input type="file" name="admin_Photo" accept="image/*"
                       class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-full file:border-0
                              file:text-sm file:font-semibold
                              file:bg-amber-600 file:text-white
                              hover:file:bg-amber-700
                              cursor-pointer">
                <p class="text-xs text-gray-500 mt-1">JPG, PNG, or GIF. Max 2MB.</p>
            </div>
        </div>

        {{-- Personal Info --}}
        <h2 class="text-xl font-bold text-gray-800 border-b pb-3">Personal Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="admin_FullName" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="admin_FullName" id="admin_FullName" value="{{ old('admin_FullName', $admin->admin_FullName) }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label for="user_Email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="user_Email" id="user_Email" value="{{ old('user_Email', $user->user_Email) }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label for="admin_PhoneNumber" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="admin_PhoneNumber" id="admin_PhoneNumber" value="{{ old('admin_PhoneNumber', $admin->admin_PhoneNumber) }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div>
                <label for="admin_Age" class="block text-sm font-medium text-gray-700">Age</label>
                <input type="number" name="admin_Age" id="admin_Age" value="{{ old('admin_Age', $admin->admin_Age) }}" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>
        </div>

        <div>
            <label for="admin_Address" class="block text-sm font-medium text-gray-700">Address</label>
            <textarea name="admin_Address" id="admin_Address" rows="3" required
                      class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">{{ old('admin_Address', $admin->admin_Address) }}</textarea>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col md:flex-row items-center gap-4 pt-4 border-t">
            <button type="submit" class="w-full md:w-auto py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all">
                Save Changes
            </button>
            <a href="{{ route('admin.profile') }}" class="w-full md:w-auto py-2 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all text-center">
                Cancel
            </a>
        </div>
    </form>

    {{-- =============================================== --}}
    {{-- Change Password Form --}}
    {{-- =============================================== --}}
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 md:p-10 w-full mt-10 relative">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Change Your Password</h2>
            <p class="text-gray-500">Update your password below.</p>
        </div>

        <form action="{{ route('admin.password.update') }}" method="POST" class="space-y-6 max-w-lg mx-auto">
            @csrf

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" name="current_password" id="current_password" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="new_password" id="new_password" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="w-full md:w-auto py-2 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all">
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
            confirmButtonColor: '#f59e0b',
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

{{-- Image preview function --}}
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
</script>
@endpush
