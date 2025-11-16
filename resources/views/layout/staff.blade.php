<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PadangPro Staff')</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f1f5f9; /* slate-100 */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Sidebar */
        aside {
            width: 230px;
            background: #27272a; /* Zinc-800 */
            color: white;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 25px 20px;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        aside .logo {
            font-size: 1.7rem;
            font-weight: 700;
            color: #a3e635; /* Lime-400 */
            margin-bottom: 40px;
            text-align: center;
        }

        aside nav ul {
            list-style: none;
            padding: 0;
        }

        aside nav ul li {
            margin-bottom: 10px;
        }

        aside nav ul li a {
            display: block;
            padding: 12px 16px;
            border-radius: 8px;
            color: #e5e7eb;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        /* Active/Hover state with Lime */
        aside nav ul li a:hover,
        aside nav ul li a.active {
            background: linear-gradient(135deg, #a3e635, #bef264); /* Lime-400 to Lime-300 */
            color: #18181b; /* Zinc-900 */
            transform: translateX(5px);
            box-shadow: 0 4px 10px rgba(163, 230, 53, 0.25);
        }

        /* Dropdown (Identical to Admin's CSS) */
        .nav-item.dropdown:hover > .dropdown-menu {
            display: block;
        }

        .nav-item .dropdown-menu {
            display: none;
            margin-left: 15px;
            padding-left: 10px;
        }

        .nav-item .dropdown-menu .dropdown-item {
            display: block;
            padding: 8px 12px;
            color: #d1d5db; /* zinc-300 */
            border-radius: 6px;
            transition: 0.3s;
        }

        .nav-item .dropdown-menu .dropdown-item:hover {
            background: linear-gradient(135deg, #a3e635, #bef264);
            color: #18181b;
        }

        /* Main Content */
        main {
            flex: 1;
            margin-left: 230px;
            padding: 30px;
            transition: 0.3s;
            background: #f1f5f9; /* slate-100 */
        }

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 25px;
        }

        .topbar div {
            font-size: 1rem;
            color: #111827;
            background: white;
            padding: 10px 18px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Footer */
        footer {
            margin-left: 230px;
            padding: 15px 0;
            text-align: center;
            font-size: 0.9rem;
            color: #a1a1aa; /* zinc-400 */
            background: #27272a; /* Zinc-800 */
            border-top: 1px solid #3f3f46; /* Zinc-700 */
            box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="bg-slate-100">
    <aside>
        <div class="logo flex items-center justify-center space-x-2">
            <img src="{{ asset('images/logoPadang.png') }}" alt="PadangPro Logo" class="w-10 h-10 object-contain rounded-full bg-white p-1">
            <span>PadangPro</span>
        </div>
        
        {{-- =============================================== --}}
        {{-- NEW: Navigation (matches Admin structure) --}}
        {{-- =============================================== --}}
        <nav>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('staff.dashboard') }}" class="{{ (request()->routeIs('staff.dashboard')) ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill mr-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('staff.profile') }}" class="{{ (request()->routeIs('staff.profile*')) ? 'active' : '' }}">
                        <i class="bi bi-person-fill mr-2"></i> Profile
                    </a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ (request()->routeIs('staff.booking.*')) ? 'active' : '' }}" href="#">
                        <i class="bi bi-calendar-check-fill mr-2"></i> Manage Bookings
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('staff.booking.viewAll') }}">View All Bookings</a></li>
                        <li><a class="dropdown-item" href="{{ route('staff.booking.manage') }}">Book Standard Pitch</a></li>
                        <li><a class="dropdown-item" href="{{ route('staff.booking.mini') }}">Book Mini Pitch</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('staff.rental.main') }}" class="{{ (request()->routeIs('staff.rental.*')) ? 'active' : '' }}">
                        <i class="bi bi-tags-fill mr-2"></i> Manage Rentals
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('staff.rating.view') }}"><i class="bi bi-star-fill mr-2"></i> Ratings & Reviews</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ (request()->routeIs('staff.reports.*')) ? 'active' : '' }}" href="#">
                        <i class="bi bi-graph-up mr-2"></i> Reports
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('staff.reports.index') }}">Reports Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('staff.reports.published') }}">Published Reports</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="{{ route('logout') }}"><i class="bi bi-box-arrow-left mr-2"></i> Logout</a>
                </li>
            </ul>
        </nav>
    </aside>

    <main>
        <div class="topbar">
            <div>Welcome, <strong>{{ $fullName ?? session('full_name', 'Staff') }}</strong></div>
        </div>

        @yield('content')
    </main>

    <footer>
        © {{ date('Y') }} PadangPro Staff Panel. All Rights Reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>