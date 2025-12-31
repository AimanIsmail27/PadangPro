<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PadangPro Admin')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">
    @vite('resources/css/app.css')

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Sidebar */
        aside {
            width: 230px;
            background: #111827; /* Dark charcoal */
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
            z-index: 30;
        }

        /* Mobile: Hide sidebar by default */
        @media (max-width: 1023px) {
            aside {
                transform: translateX(-100%);
            }
            aside.show {
                transform: translateX(0);
            }
        }

        aside .logo {
            font-size: 1.7rem;
            font-weight: 700;
            color: #f6c700; /* gold accent */
            margin-bottom: 40px;
            text-align: center;
        }

        aside nav {
            flex-grow: 1;
            overflow-y: auto;
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
            cursor: pointer;
        }

        aside nav ul li a:hover,
        aside nav ul li a.active {
            background: linear-gradient(135deg, #f6c700, #ffde59);
            color: #111827;
            transform: translateX(5px);
            box-shadow: 0 4px 10px rgba(246, 199, 0, 0.25);
        }

        /* Dropdown - Desktop hover, Mobile click */
        @media (min-width: 1024px) {
            .nav-item.dropdown:hover > .dropdown-menu {
                display: block;
            }
        }

        .nav-item .dropdown-menu {
            display: none;
            margin-left: 15px;
            padding-left: 10px;
            margin-top: 5px;
        }

        /* Mobile: Click to toggle */
        @media (max-width: 1023px) {
            .nav-item.dropdown.open > .dropdown-menu {
                display: block;
            }
        }

        .nav-item .dropdown-menu .dropdown-item {
            display: block;
            padding: 8px 12px;
            color: #d1d5db;
            border-radius: 6px;
            transition: 0.3s;
        }

        .nav-item .dropdown-menu .dropdown-item:hover {
            background: linear-gradient(135deg, #f6c700, #ffde59);
            color: #111827;
        }

        /* Overlay for mobile */
        #sidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 20;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #sidebarOverlay.show {
            display: block;
            opacity: 1;
        }

        /* Main Content */
        main {
            flex: 1;
            margin-left: 230px;
            padding: 30px;
            transition: 0.3s;
            background: #f4f4f4;
        }

        @media (max-width: 1023px) {
            main {
                margin-left: 0;
            }
        }

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 25px;
        }

        .topbar .menu-toggle {
            display: none;
            background: white;
            border: none;
            padding: 10px 15px;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 1.5rem;
            margin-right: auto;
        }

        @media (max-width: 1023px) {
            .topbar .menu-toggle {
                display: block;
            }
        }

        .topbar div {
            font-size: 1rem;
            color: #111827;
            background: white;
            padding: 10px 18px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            animation: fadeInRight 1s ease;
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #f6c700, #ffde59);
            color: #111827;
            padding: 70px 40px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            font-size: 1.8rem;
            font-weight: bold;
            text-shadow: none;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Footer */
        footer {
            margin-left: 230px;
            padding: 15px 0;
            text-align: center;
            font-size: 0.9rem;
            color: #ddd;
            background: #111827;
            border-top: 1px solid #222;
            box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 1023px) {
            footer {
                margin-left: 0;
            }
        }

        footer a {
            color: #f6c700;
            text-decoration: none;
            font-weight: 500;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Overlay for mobile -->
    <div id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar">
        <div class="logo">⚽ PadangPro</div>
        <nav>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="{{ route('administrator.dashboard') }}">📊 Dashboard</a></li>
                <li class="nav-item"><a href="{{ route('admin.profile') }}">👤 Profile</a></li>
                <li class="nav-item"><a href="{{ route('staff.register') }}">👥 Register Staff</a></li>
                {{-- UPDATED BOOKING DROPDOWN TO MIRROR CUSTOMER LAYOUT --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" onclick="toggleDropdown(event, this)">📅 Manage Bookings</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.booking.viewAll') }}">View All Bookings</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.booking.manage') }}">Book Standard Pitch</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.booking.mini') }}">Book Mini Pitch</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a href="{{ route('admin.rentals.current') }}" class="{{ (request()->routeIs('admin.rentals.current')) ? 'active' : '' }}"><i class="bi bi-tags-fill mr-2"></i>🏷️ View Rentals</a></li>
                <li class="nav-item"><a href="{{ route('admin.rating.view') }}">⭐ Rating & Review</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" onclick="toggleDropdown(event, this)">📈 Reports</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.index') }}">Reports Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.published') }}">Published Reports</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a href="{{ route('logout') }}">🚪 Logout</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main>
        <div class="topbar">
            <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
            <div>Welcome, <strong>{{ $fullName ?? session('full_name', 'Admin') }}</strong></div>
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        © {{ date('Y') }} PadangPro Admin Panel. All Rights Reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        function toggleDropdown(event, element) {
            // Only handle click on mobile
            if (window.innerWidth < 1024) {
                event.preventDefault();
                const dropdownItem = element.closest('.nav-item.dropdown');
                
                // Close other dropdowns
                document.querySelectorAll('.nav-item.dropdown').forEach(item => {
                    if (item !== dropdownItem) {
                        item.classList.remove('open');
                    }
                });
                
                // Toggle current dropdown
                dropdownItem.classList.toggle('open');
            }
        }

        // Close sidebar when clicking on a non-dropdown link in mobile
        document.querySelectorAll('aside nav a:not(.dropdown-toggle)').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });

        // Auto-open dropdown if current route matches (mobile only)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 1024) {
                const currentPath = window.location.pathname;
                
                // Check all dropdown items
                document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
                    if (item.getAttribute('href') === currentPath) {
                        // Open the parent dropdown
                        const parentDropdown = item.closest('.nav-item.dropdown');
                        if (parentDropdown) {
                            parentDropdown.classList.add('open');
                        }
                    }
                });
            }
        });
    </script>
    @include('layout.partials.idle-logout')
    @stack('scripts')
</body>
</html>