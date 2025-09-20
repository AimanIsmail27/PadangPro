<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - PadangPro</title>
    @vite('resources/css/app.css')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 220px;
            background: white;
            border-right: 1px solid #ddd;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
        }
        .sidebar h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            padding: 10px 0;
            color: #333;
            text-decoration: none;
            margin-bottom: 8px;
        }
        .sidebar a:hover {
            color: #007bff;
        }
        .main {
            margin-left: 240px;
            padding: 30px;
        }
        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-section {
            background: #1c2d6e;
            border-radius: 8px;
            height: 120px; /* same size as your first design */
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
            top: -40px; /* floating effect: overlap blue section */
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
            text-align: left; /* align details to the left */
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

        aside {
            width: 220px;
            background: white;
            color: #000;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
        }

        aside .logo {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 40px;
        }

        aside nav ul {
            list-style: none;
            padding: 0;
        }

        aside nav ul li {
            padding: 12px 0;
            cursor: pointer;
            font-weight: 500;
        }

        aside nav ul li:hover {
            background: rgba(255, 255, 255, 0.13);
            border-radius: 8px;
            padding-left: 10px;
            transition: 0.3s;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside>
        <div class="logo">PadangPro</div>
        <nav>
            <ul>
                <li><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('customer.profile') }}">Profile</a></li>
                <li>Booking</li>
                <li>Rental</li>
                <li>Matchmaking</li>
                <li>Rating and Review</li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <div class="main">
        <div class="topbar">
            <div>Welcome! <strong>{{ $fullName }}</strong></div>
        </div>

        <div class="profile-section">
            <div class="profile-header">MY PROFILE</div>
        </div>

        <div class="profile-card">
            <a href="{{ route('customer.profile.edit') }}" class="edit-btn" style="text-decoration:none;display:inline-block;text-align:center;">Edit</a>


            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>

            <div class="profile-details">
                <p><strong>Your Name:</strong> {{ $fullName }}</p>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Phone Number:</strong> {{ $phoneNumber }}</p>
                <p><strong>Your Age:</strong> {{ $age }}</p>
                <p><strong>Your Address:</strong> {{ $address }}</p>
                <p><strong>Your Preferred Football Position:</strong> {{ $position }}</p>
            </div>

           <form action="{{ route('customer.profile.delete') }}" method="POST" style="display:inline;" id="deleteForm">
    @csrf
    @method('DELETE')
    <input type="hidden" name="user_id" value="{{ session('user_id') }}">
    <button type="button" class="delete-btn" id="deleteAccountBtn">Delete Your Account</button>
</form>




        </div>
    </div>
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

</body>
</html>
