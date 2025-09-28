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
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        aside {
            width: 220px;
            background: #f1f5f9; /* Neutral Gray */
            color: #111827; /* Dark Gray for readability */
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
            color: #16a34a; /* Green logo accent */
        }

        aside nav ul {
            list-style: none;
            padding: 0;
        }

        aside nav ul li {
            cursor: pointer;
            font-weight: 500;
        }

        aside nav ul li a {
            text-decoration: none;
            color: inherit;
            display: block;
            padding: 12px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        /* Hover / Active state */
        aside nav ul li a:hover {
            background: #22c55e; /* Green hover */
            color: #fff;
            padding-left: 12px;
        }

        /* Main Content */
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

        /* Optional welcome banner (can adapt to green theme later) */
        .welcome-banner {
            background-color: #bbf7d0; /* Soft green */
            color: #111827;
            padding: 50px 30px;
            border-radius: 10px;
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
<aside>
    <div class="logo flex items-center space-x-2 mb-10">
        <img src="{{ asset('images/logoPadang.png') }}" alt="PadangPro Logo" class="w-10 h-10 object-contain">
        <span class="text-green-600 font-bold text-xxl">PadangPro</span>
    </div>
    <nav>
        <ul>
            <li><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('staff.profile') }}">Profile</a></li>
            <li><a href="#">Booking</a></li>
            <li><a href="{{ route('staff.rental.main') }}">Manage Rentals</a></li>
            <li><a href="#">Rating and Review</a></li>
            <li><a href="#">Report</a></li>
            <li><a href="{{ route('logout') }}">Logout</a></li>
        </ul>
    </nav>
</aside>



    <!-- Main Content -->
    <div class="main">
        <div class="topbar">
           Welcome, <strong>{{ $fullName ?? session('full_name', 'Staff') }}</strong>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')

</body>
</html>
