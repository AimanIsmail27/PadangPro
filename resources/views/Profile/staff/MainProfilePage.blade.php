<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - PadangPro (Staff)</title>
    @vite('resources/css/app.css')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
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
        aside nav ul li a {
            text-decoration: none;
            color: #000;
        }
        aside nav ul li a:hover {
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
        .profile-details {
            text-align: left;
        }
        .profile-details p {
            margin: 6px 0;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside>
        <div class="logo">PadangPro</div>
        <nav>
            <ul>
                <li><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('staff.profile') }}">Profile</a></li>
                <li>Booking</li>
                <li>Rental</li>
                <li>Matchmaking</li>
                <li>Rating and Review</li>
                <li>Logout</li>
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
            <div class="profile-details">
                <p><strong>Your Name:</strong> {{ $fullName }}</p>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Phone Number:</strong> {{ $phoneNumber }}</p>
                <p><strong>Your Age:</strong> {{ $age }}</p>
                <p><strong>Your Address:</strong> {{ $address }}</p>
                <p><strong>Your Job:</strong> {{ $job }}</p>
            </div>
        </div>
    </div>

</body>
</html>
