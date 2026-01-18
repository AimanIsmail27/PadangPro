<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PadangPro')</title>

    <!-- Favicon / Tab Icon -->
<link rel="icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">

    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        /* Custom scrollbar for sidebar */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #475569; border-radius: 10px; } 
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        
        /* Active link styling */
        .sidebar-link.active {
            background-color: #4f46e5; 
            color: #ffffff;
            font-weight: 600;
        }
        .sidebar-link.active i { color: #ffffff; }

        /* Transitions for mobile sidebar */
        #sidebar { transition: transform 0.3s ease-in-out; }
        
        /* Mobile Overlay */
        #sidebarOverlay { transition: opacity 0.3s ease-in-out; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 font-sans flex min-h-screen">

    @include('layout.partials.page-loader')


    <div id="sidebarOverlay" onclick="toggleSidebar()" 
         class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden opacity-0 pointer-events-none transition-opacity"></div>

    <aside id="sidebar" 
           class="bg-slate-800 text-slate-300 w-64 fixed top-0 left-0 h-full z-30 p-6 flex flex-col shadow-2xl transform -translate-x-full lg:translate-x-0">
        
        <div class="flex items-center justify-between mb-10 px-2">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logoPadang.png') }}" alt="Logo" class="w-9 h-9 object-contain rounded-full bg-white p-1">
                <span class="text-white font-bold text-xl tracking-tight">PadangPro</span>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        
        <nav class="flex-grow sidebar-nav overflow-y-auto">
            <ul class="space-y-2">
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('customer.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 transition-all {{ (request()->routeIs('customer.dashboard')) ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill text-indigo-400"></i> <span>Dashboard</span>
                    </a>
                </li>
                
                {{-- Profile --}}
                <li class="nav-item">
                    <a href="{{ route('customer.profile') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 transition-all {{ (request()->routeIs('customer.profile')) ? 'active' : '' }}">
                        <i class="bi bi-person-fill text-indigo-400"></i> <span>Profile</span>
                    </a>
                </li>

                {{-- Booking Dropdown --}}
                <li class="nav-item" x-data="{ open: {{ request()->routeIs('booking.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full sidebar-link flex items-center justify-between px-4 py-3 rounded-lg hover:bg-slate-700 transition-all {{ (request()->routeIs('booking.*')) ? 'active' : '' }}">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-calendar-check-fill text-indigo-400"></i> <span>Booking</span>
                        </div>
                        <i class="bi bi-chevron-down text-xs transition-transform" :class="{'rotate-180': open}"></i>
                    </button>
                    {{-- Simple Alpine logic for dropdown animation --}}
                    <ul x-show="open" class="mt-1 space-y-1 bg-slate-900/50 rounded-lg overflow-hidden" x-cloak>
                        <li><a class="block px-10 py-2 text-sm hover:text-white hover:bg-slate-700" href="{{ route('booking.view') }}">History</a></li>
                        <li><a class="block px-10 py-2 text-sm hover:text-white hover:bg-slate-700" href="{{ route('booking.page', 'F01') }}">Standard Pitch</a></li>
                        <li><a class="block px-10 py-2 text-sm hover:text-white hover:bg-slate-700" href="{{ route('booking.mini') }}">Mini Pitch</a></li>
                    </ul>
                </li>

                {{-- Rental --}}
                <li class="nav-item">
                    <a href="{{ route('customer.rental.main') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 transition-all {{ (request()->routeIs('customer.rental.*')) ? 'active' : '' }}">
                        <i class="bi bi-tags-fill text-indigo-400"></i> <span>Rental Items</span>
                    </a>
                </li>

                {{-- Matchmaking Dropdown --}}
                <li class="nav-item" x-data="{ open: {{ request()->routeIs('matchmaking.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full sidebar-link flex items-center justify-between px-4 py-3 rounded-lg hover:bg-slate-700 transition-all {{ (request()->routeIs('matchmaking.*')) ? 'active' : '' }}">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-people-fill text-indigo-400"></i> <span>Matchmaking</span>
                        </div>
                        <i class="bi bi-chevron-down text-xs transition-transform" :class="{'rotate-180': open}"></i>
                    </button>
                    <ul x-show="open" class="mt-1 space-y-1 bg-slate-900/50 rounded-lg overflow-hidden" x-cloak>
                        <li><a class="block px-10 py-2 text-sm hover:text-white hover:bg-slate-700" href="{{ route('matchmaking.personal') }}">My Ads</a></li>
                        <li><a class="block px-10 py-2 text-sm hover:text-white hover:bg-slate-700" href="{{ route('matchmaking.other') }}">Find Match</a></li>
                    </ul>
                </li>

                {{-- Rating --}}
                <li class="nav-item">
                    <a href="{{ route('customer.rating.main') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-700 transition-all {{ (request()->routeIs('customer.rating.*')) ? 'active' : '' }}">
                        <i class="bi bi-star-fill text-indigo-400"></i> <span>Reviews</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="mt-auto pt-6 border-t border-slate-700">
            <a href="{{ route('logout') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-red-600 hover:text-white transition-all">
                <i class="bi bi-box-arrow-left"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 w-full lg:ml-64">
        
        <header class="bg-white shadow-sm p-4 sticky top-0 z-20 border-b border-gray-200 flex justify-between items-center">
            
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-600 focus:outline-none p-2 rounded-md hover:bg-gray-100">
                <i class="bi bi-list text-2xl"></i>
            </button>

            <div class="flex items-center gap-3 ml-auto">
                <div class="text-right hidden sm:block">
                    <span class="block text-sm text-gray-500">Welcome back,</span>
                    <span class="block text-sm font-bold text-gray-800">{{ $fullName ?? session('full_name', 'Customer') }}</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold border border-indigo-200">
                    {{ substr($fullName ?? 'C', 0, 1) }}
                </div>
            </div>
        </header>

        <main class="p-4 md:p-8 flex-grow">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} PadangPro. All Rights Reserved.
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Alpine.js for dropdown animations (Optional but recommended) --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            // Toggle the translate class to show/hide
            sidebar.classList.toggle('-translate-x-full');
            
            // Toggle Overlay
            if (sidebar.classList.contains('-translate-x-full')) {
                // Sidebar is hidden
                overlay.classList.add('opacity-0', 'pointer-events-none');
                setTimeout(() => overlay.classList.add('hidden'), 300); // Wait for fade out
            } else {
                // Sidebar is visible
                overlay.classList.remove('hidden');
                // Small delay to allow display:block to apply before opacity transition
                setTimeout(() => overlay.classList.remove('opacity-0', 'pointer-events-none'), 10);
            }
        }
    </script>
    @include('layout.partials.idle-logout')
    @stack('scripts')
</body>
</html>
