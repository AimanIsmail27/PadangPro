<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PadangPro')</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* New active state for the Slate/Indigo theme */
        .sidebar-link.active {
            background-color: #4f46e5; /* Indigo-600 */
            color: #ffffff;
            font-weight: 600;
        }
        .sidebar-link.active i {
            color: #ffffff;
        }
        /* Custom scrollbar */
        .sidebar-nav::-webkit-scrollbar { width: 6px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #475569; border-radius: 10px; } /* Slate-600 */
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        
        /* Dropdown styles */
        .nav-item.dropdown:hover > .dropdown-menu {
            display: block;
        }
        .nav-item .dropdown-menu {
            display: none;
            margin-left: 1.5rem; /* 24px */
            padding-left: 0.5rem; /* 8px */
        }
    </style>
</head>
<body class="bg-slate-100 font-sans flex">

    <aside class="bg-slate-800 text-slate-300 w-64 fixed top-0 left-0 h-full z-10 p-6 flex flex-col shadow-2xl">
        <div class="logo flex items-center space-x-3 mb-12 px-2">
            <img src="{{ asset('images/logoPadang.png') }}" alt="PadangPro Logo" class="w-10 h-10 object-contain rounded-full bg-white p-1">
            <span class="text-white font-bold text-2xl tracking-tight">PadangPro</span>
        </div>
        
        <nav class="flex-grow sidebar-nav overflow-y-auto">
            <ul class="space-y-2">
                <li class="nav-item">
                    <a href="{{ route('customer.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-slate-700 transition-all duration-200
                        {{ (request()->routeIs('customer.dashboard')) ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill text-indigo-400"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('customer.profile') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-slate-700 transition-all duration-200
                        {{ (request()->routeIs('customer.profile')) ? 'active' : '' }}">
                        <i class="bi bi-person-fill text-indigo-400"></i>
                        <span>Profile</span>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="sidebar-link nav-link dropdown-toggle flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-slate-700 transition-all duration-200
                        {{ (request()->routeIs('booking.*')) ? 'active' : '' }}" href="#">
                        <i class="bi bi-calendar-check-fill text-indigo-400"></i>
                        <span>Booking</span>
                    </a>
                    <ul class="dropdown-menu mt-2 space-y-1">
                        <li><a class="sidebar-link dropdown-item block px-4 py-2 rounded-lg hover:bg-slate-700" href="{{ route('booking.view') }}">Your Booking History</a></li>
                        <li><a class="sidebar-link dropdown-item block px-4 py-2 rounded-lg hover:bg-slate-700" href="{{ route('booking.page', 'F01') }}">Book Standard Pitch</a></li>
                        <li><a class="sidebar-link dropdown-item block px-4 py-2 rounded-lg hover:bg-slate-700" href="{{ route('booking.mini') }}">Book Mini Pitch</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('customer.rental.main') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-slate-700 transition-all duration-200
                        {{ (request()->routeIs('customer.rental.*')) ? 'active' : '' }}">
                        <i class="bi bi-tags-fill text-indigo-400"></i>
                        <span>Rental Items</span>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="sidebar-link nav-link dropdown-toggle flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-slate-700 transition-all duration-200
                        {{ (request()->routeIs('matchmaking.*')) ? 'active' : '' }}" href="#">
                        <i class="bi bi-people-fill text-indigo-400"></i>
                        <span>Matchmaking</span>
                    </a>
                    <ul class="dropdown-menu mt-2 space-y-1">
                        <li><a class="sidebar-link dropdown-item block px-4 py-2 rounded-lg hover:bg-slate-700" href="{{ route('matchmaking.personal') }}">Your Advertisement</a></li>
                        <li><a class="sidebar-link dropdown-item block px-4 py-2 rounded-lg hover:bg-slate-700" href="{{ route('matchmaking.other') }}">Other Advertisements</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('customer.rating.main') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-slate-700 transition-all duration-200
                        {{ (request()->routeIs('customer.rating.*')) ? 'active' : '' }}">
                        <i class="bi bi-star-fill text-indigo-400"></i>
                        <span>Rating & Review</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="mt-auto pt-6 border-t border-slate-700">
            <a href="{{ route('logout') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 rounded-lg text-slate-400 hover:bg-red-600 hover:text-white transition-all duration-200">
                <i class="bi bi-box-arrow-left"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <div class="ml-64 flex-1">
        <header class="bg-white shadow-sm p-6 sticky top-0 z-5 border-b border-gray-200">
            <div class="flex justify-end items-center">
                <div class="text-gray-700 font-medium">
                    Welcome, <strong>{{ $fullName ?? session('full_name', 'Customer') }}</strong>
                </div>
            </div>
        </header>

        <main class="p-8">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>