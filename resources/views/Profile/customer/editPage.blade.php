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
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .checkbox-group label {
            font-weight: normal;
            margin-right: 10px;
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
                    <input type="text" name="customer_Position" id="customer_Position" value="{{ $customer->customer_Position }}" required>
                </div>

                <!-- ✅ New Field: Skill Level -->
                <div class="form-group">
                    <label for="customer_SkillLevel">Skill Level (1–5)</label>
                    <select name="customer_SkillLevel" id="customer_SkillLevel" required>
                        <option value="">-- Select Skill Level --</option>
                        <option value="1" {{ $customer->customer_SkillLevel == 1 ? 'selected' : '' }}>
                            1 - Beginner
                        </option>
                        <option value="2" {{ $customer->customer_SkillLevel == 2 ? 'selected' : '' }}>
                            2 - Social Player
                        </option>
                        <option value="3" {{ $customer->customer_SkillLevel == 3 ? 'selected' : '' }}>
                            3 - Intermediate
                        </option>
                        <option value="4" {{ $customer->customer_SkillLevel == 4 ? 'selected' : '' }}>
                            4 - Semi Professional
                        </option>
                        <option value="5" {{ $customer->customer_SkillLevel == 5 ? 'selected' : '' }}>
                            5 - Professional
                        </option>
                    </select>
                    <small class="text-gray-600">
                        1 = Beginner, 2 = Social Player, 3 = Intermediate, 4 = Semi Professional, 5 = Professional
                    </small>
                </div>


                <!-- ✅ Availability Days -->
                @php
                    $availability = json_decode($customer->customer_Availability, true) ?? ['days' => [], 'time' => []];
                @endphp

                <div class="form-group">
                    <label>Availability - Days</label>
                    <div class="checkbox-group">
                        @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                            <label>
                                <input type="checkbox" name="customer_Availability_days[]" value="{{ $day }}"
                                    {{ in_array($day, $availability['days']) ? 'checked' : '' }}>
                                {{ $day }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- ✅ Availability Times -->
                <div class="form-group">
                    <label>Availability - Times</label>
                    <div class="checkbox-group">
                        @foreach (['Morning','Afternoon','Evening','Night'] as $time)
                            <label>
                                <input type="checkbox" name="customer_Availability_times[]" value="{{ $time }}"
                                    {{ in_array($time, $availability['time']) ? 'checked' : '' }}>
                                {{ $time }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="save-btn">Save Changes</button>
                <a href="{{ route('customer.profile') }}" class="cancel-btn">Cancel</a>
            </form>
        </div>
    </div>

</body>
</html>
