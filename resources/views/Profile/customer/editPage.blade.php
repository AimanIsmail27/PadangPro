<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - PadangPro</title>
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
            display: block;
        }
        aside nav ul li:hover {
            background: rgba(255, 255, 255, 0.13);
            border-radius: 8px;
            padding-left: 10px;
            transition: 0.3s;
        }
        .main {
            margin-left: 240px;
            padding: 30px;
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
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .save-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        .cancel-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 10px;
            font-size: 16px;
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
        <div class="profile-section">
            <div class="profile-header">EDIT PROFILE</div>
        </div>

        <div class="profile-card">
            <form action="{{ route('customer.profile.update') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="customer_FullName">Full Name</label>
                    <input type="text" name="customer_FullName" id="customer_FullName" value="{{ $customer->customer_FullName }}" required>
                </div>

                <div class="form-group">
                    <label for="user_Email">Email</label>
                    <input type="email" name="user_Email" id="user_Email" value="{{ $user->user_Email }}" required>
                </div>

                <div class="form-group">
                    <label for="customer_PhoneNumber">Phone Number</label>
                    <input type="text" name="customer_PhoneNumber" id="customer_PhoneNumber" value="{{ $customer->customer_PhoneNumber }}" required>
                </div>

                <div class="form-group">
                    <label for="customer_Age">Age</label>
                    <input type="number" name="customer_Age" id="customer_Age" value="{{ $customer->customer_Age }}" required>
                </div>

                <div class="form-group">
                    <label for="customer_Address">Address</label>
                    <textarea name="customer_Address" id="customer_Address" required>{{ $customer->customer_Address }}</textarea>
                </div>

                <div class="form-group">
                    <label for="customer_Position">Preferred Football Position</label>
                    <input type="text" name="customer_Position" id="customer_Position" value="{{ $customer->customer_Position }} " required>
                </div>

                <button type="submit" class="save-btn">Save Changes</button>
                <a href="{{ route('customer.profile') }}" class="cancel-btn">Cancel</a>
            </form>
            
        </div>
    </div>

</body>
</html>
