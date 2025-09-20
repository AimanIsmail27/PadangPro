<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PadangPro')</title>
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

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: flex-end; /* pushes to the right */
            align-items: center;
            margin-bottom: 20px;
        }

        /* Shared Blue Banner */
        .welcome-banner {
            background-color: #1E2A78;
            color: white;
            padding: 85px 30px;
            border-radius: 10px;
            font-size: 1.6rem;
            font-weight: bold;
            margin-bottom: 25px;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Hide submenu by default */
        .nav-item .dropdown-menu {
            display: none;
            position: relative;
            margin-left: 15px;
            background: transparent;
            border: none;
            box-shadow: none;
            padding-left: 10px;
        }

        /* Show submenu when hovering */
        .nav-item.dropdown:hover > .dropdown-menu {
            display: block;
        }

        .nav-item .dropdown-menu .dropdown-item {
            white-space: nowrap;
        }

        .nav-item .dropdown-menu .dropdown-item:hover {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 6px;
            padding-left: 8px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside>
        <div class="logo">PadangPro</div>
        <nav>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a href="{{ route('customer.profile') }}">Profile</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#">Booking</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('booking.view') }}">Your Booking History</a></li>
                        <li><a class="dropdown-item" href="{{ route('booking.page', 'F01') }}">Book Standard Field</a></li>
                        <li><a class="dropdown-item" href="{{ route('booking.mini') }}">Book Mini Pitch</a></li>
                    </ul>
                </li>
                <li class="nav-item">Rental</li>
                <li class="nav-item">Matchmaking</li>
                <li class="nav-item">Rating and Review</li>
                <li class="nav-item"><a href="{{ route('logout') }}">Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main>
        <div class="topbar">
            <div>Welcome, <strong>{{ $fullName ?? session('full_name', 'Customer') }}</strong></div>
        </div>

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
