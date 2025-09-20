<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PadangPro')</title>
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
            border-right: 1px solid #ddd;
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
            color: #333;
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
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside>
        <div class="logo">PadangPro</div>
        <nav>
            <ul>
                <li><a href="{{ route('administrator.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.profile') }}">Profile</a></li>
                <li>Manage Bookings</li>
                <li>Manage Rentals</li>
                <li>Rating and Review</li>
                <li>Report</li>
                <li><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <div class="main">
        <div class="topbar">
            <div>Welcome, <strong>{{ $fullName ?? session('full_name', 'Admin') }}</strong></div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
