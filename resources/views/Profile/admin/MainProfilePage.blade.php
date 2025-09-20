@extends('layout.admin')

@section('title', 'Admin Profile - PadangPro')

@section('content')
<style>
    .profile-section {
        background: #dbcf27f8;
        border-radius: 8px;
        height: 120px;
        position: relative;
    }
    .profile-header {
        color: white;
        font-size: 20px;
        font-weight: bold;
        padding: 20px 30px;
    }
    .profile-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 30px;
        width: 80%;
        margin: 0 auto;
        position: relative;
        top: -40px;
    }
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #f0f0f0;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 40px;
        margin-bottom: 20px;
    }
    .profile-details {
        text-align: left;
    }
    .profile-details p {
        margin: 6px 0;
    }
    .edit-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
    }
    .delete-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 20px;
    }
</style>

<div class="profile-section">
    <div class="profile-header">ADMIN PROFILE</div>
</div>

<div class="profile-card">
    <a class="edit-btn" href="{{ route('admin.profile.edit') }}">Edit</a>

    <div class="profile-avatar">
        <i class="fas fa-user"></i>
    </div>

    <div class="profile-details">
        <p><strong>Full Name:</strong> {{ $fullName }}</p>
        <p><strong>Email:</strong> {{ $email }}</p>
        <p><strong>Phone Number:</strong> {{ $phoneNumber }}</p>
        <p><strong>Age:</strong> {{ $age }}</p>
        <p><strong>Address:</strong> {{ $address }}</p>
    </div>

    <form method="POST" action="{{ route('admin.profile.delete') }}" style="display:inline;" id="deleteForm">
        @csrf
        @method('DELETE')
        <input type="hidden" name="user_id" value="{{ session('user_id') }}">
        <button type="button" class="delete-btn" id="deleteAccountBtn">Delete Account</button>
    </form>
</div>

{{-- SweetAlert2 Script --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
@endsection
