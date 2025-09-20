@extends('layout.admin') {{-- Your existing admin layout with welcome, fullname, etc. --}}

@section('content')
    {{-- Yellow header for admin --}}
    <div class="profile-section" style="
        background: #dbcf27f8;
        border-radius: 8px;
        height: 120px;
        position: relative;">
        <div class="profile-header" style="
            color: white;
            font-size: 20px;
            font-weight: bold;
            padding: 20px 30px;">
            EDIT ADMIN PROFILE
        </div>
    </div>

    {{-- White container with same design as before --}}
    <div class="profile-card" style="
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 30px;
        width: 80%;
        margin: 0 auto;
        position: relative;
        top: -40px;">
        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf

            @method('PUT') {{-- Add this line to match the route method --}}

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="admin_FullName" style="font-weight: bold; display: block; margin-bottom: 6px;">Full Name</label>
                <input type="text" name="admin_FullName" id="admin_FullName"
                       value="{{ $admin->admin_FullName }}" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="user_Email" style="font-weight: bold; display: block; margin-bottom: 6px;">Email</label>
                <input type="email" name="user_Email" id="user_Email"
                       value="{{ $user->user_Email }}" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="admin_PhoneNumber" style="font-weight: bold; display: block; margin-bottom: 6px;">Phone Number</label>
                <input type="text" name="admin_PhoneNumber" id="admin_PhoneNumber"
                       value="{{ $admin->admin_PhoneNumber }}" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="admin_Age" style="font-weight: bold; display: block; margin-bottom: 6px;">Age</label>
                <input type="number" name="admin_Age" id="admin_Age"
                       value="{{ $admin->admin_Age }}" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="admin_Address" style="font-weight: bold; display: block; margin-bottom: 6px;">Address</label>
                <textarea name="admin_Address" id="admin_Address" required
                          style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">{{ $admin->admin_Address }}</textarea>
            </div>

            <button type="submit" style="
                background: #007bff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 6px;
                cursor: pointer;
                font-size: 16px;">
                Save Changes
            </button>
            <a href="{{ route('admin.profile') }}" style="
                background: #6c757d;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 6px;
                text-decoration: none;
                display: inline-block;
                margin-left: 10px;
                font-size: 16px;">
                Cancel
            </a>
        </form>
    </div>
@endsection
