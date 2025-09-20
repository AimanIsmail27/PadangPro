<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Staff - PadangPro</title>
    @vite('resources/css/app.css')
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            display: flex;
        }

        /* Sidebar */
        aside {
            width: 220px;
            background: linear-gradient(180deg, #e9dfc1ff, #e9dfc1ff);
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
            color: inherit;
            display: block;
            padding: 12px 0;
        }

        aside nav ul li:hover {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding-left: 10px;
            transition: 0.3s;
        }

        /* Main Content */
        main {
            margin-left: 220px;
            flex: 1;
            padding: 20px 30px;
        }

        /* Yellow Welcome Banner */
        .welcome-banner {
            background-color: #FFD700;
            color: black;
            padding: 50px 30px;
            border-radius: 10px;
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 25px;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* Form Container */
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            max-width: 700px;
            margin: 0 auto;
        }

        .form-container h2 {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .form-container p {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #FFD700;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            color: black;
            cursor: pointer;
            transition: 0.3s;
        }

        .form-container button:hover {
            background-color: #FFC107;
        }

        .alert {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside>
        <div class="logo">PadangPro</div>
        <nav>
            <ul>
                <li><a href="{{ route('administrator.dashboard') }}">Dashboard</a></li>
                <li>Profile</li>
                <li><a href="{{ route('staff.register') }}">Staff Registration</a></li>
                <li>Booking</li>
                <li>Rental</li>
                <li>Rating and Review</li>
                <li>Report</li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main>
        <!-- Yellow Welcome Banner -->
        <div class="welcome-banner">
            WELCOME, {{ Auth::user()->name ?? 'Administrator' }}
        </div>

        <!-- Registration Form -->
        <div class="form-container">
            <h2>Staff Registration</h2>
            <p>Register a new staff or administrator</p>

            <!-- Display messages -->
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('staff.register.store') }}" method="POST">
                @csrf

                <label for="staff_FullName">Full Name</label>
                <input type="text" name="staff_FullName" id="staff_FullName" value="{{ old('staff_FullName') }}" required>

                <label for="staff_Age">Age</label>
                <input type="number" name="staff_Age" id="staff_Age" value="{{ old('staff_Age') }}" required min="18">

                <label for="staff_PhoneNumber">Phone Number</label>
                <input type="text" name="staff_PhoneNumber" id="staff_PhoneNumber" value="{{ old('staff_PhoneNumber') }}" required>

                <label for="staff_Address">Address</label>
                <input type="text" name="staff_Address" id="staff_Address" value="{{ old('staff_Address') }}" required>

                <label for="user_Email">Staff/Admin Email</label>
                <input type="email" name="user_Email" id="user_Email" value="{{ old('user_Email') }}" required>

                <label for="confirm_email">Confirm Email</label>
                <input type="email" name="confirm_email" id="confirm_email" value="{{ old('confirm_email') }}" required>

                
                <label for="user_Type">User Type</label>
                <select name="user_Type" id="user_Type" required>
                    <option value="">Select User Type</option>
                    <option value="staff" {{ old('user_Type') == 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="administrator" {{ old('user_Type') == 'administrator' ? 'selected' : '' }}>Administrator</option>
                </select>

                <div id="staffJobContainer">
                    <label for="staff_Job">Job Position</label>
                    <input type="text" name="staff_Job" id="staff_Job" value="{{ old('staff_Job') }}">
                </div>
               

                <div>
                    <label for="staffID" class="block text-sm font-medium">Staff ID (Auto-generated)</label>
                    <input type="text" name="staffID" id="staffID" 
                         value="{{ old('staffID', $generatedStaffID ?? '') }}" 
                         readonly
                        class="w-full p-2 border border-gray-300 rounded bg-gray-100">
                </div>


                <div>
                    <label for="adminID" class="block text-sm font-medium">Administrator ID (Auto-generated)</label>
                    <input type="text" name="adminID" id="adminID" 
                         value="{{ old('adminID', $generatedAdminID ?? '') }}" 
                         readonly
                        class="w-full p-2 border border-gray-300 rounded bg-gray-100">
                </div>

                
                <div class="text-gray-600 mb-4">
                    * A default password will be generated and emailed to the new user.
                </div>

                <button type="submit">Register</button>
            </form>
        </div>
    </main>


    <script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeSelect = document.getElementById('user_Type');
    const staffJobContainer = document.getElementById('staffJobContainer');
    const generatedIDField = document.getElementById('generatedID');

    const staffID = "{{ $generatedStaffID ?? '' }}";
    const adminID = "{{ $generatedAdminID ?? '' }}";

    function toggleFields() {
        if (userTypeSelect.value === 'staff') {
            staffJobContainer.style.display = 'block';
            generatedIDField.value = staffID;
        } else if (userTypeSelect.value === 'administrator') {
            staffJobContainer.style.display = 'none';
            generatedIDField.value = adminID;
        } else {
            staffJobContainer.style.display = 'none';
            generatedIDField.value = '';
        }
    }

    userTypeSelect.addEventListener('change', toggleFields);
    toggleFields(); // Run on page load
});

// Force refresh on back/forward navigation to clear old form data
window.addEventListener("pageshow", function (event) {
    if (event.persisted || performance.getEntriesByType("navigation")[0]?.type === "back_forward") {
        window.location.reload();
    }
});
</script>
</body>
</html>
