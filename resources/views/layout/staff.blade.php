<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PadangPro Staff')</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">
<link rel="shortcut icon" type="image/png" href="{{ asset('images/logoPadang.png') }}">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
/* GENERAL */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background-color: #f1f5f9;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    overflow-x: hidden;
}

/* SIDEBAR */
aside {
    width: 230px;
    background: #27272a;
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

/* MOBILE SIDEBAR HIDE */
@media(max-width:1023px){
    aside {
        transform: translateX(-100%);
    }
    aside.show {
        transform: translateX(0);
    }
}

/* LOGO */
aside .logo {
    font-size: 1.7rem;
    font-weight: 700;
    color: #a3e635;
    margin-bottom: 40px;
    text-align: center;
}

/* NAV */
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
aside nav ul li a:hover,
aside nav ul li a.active {
    background: linear-gradient(135deg, #a3e635, #bef264);
    color: #18181b;
    transform: translateX(5px);
    box-shadow: 0 4px 10px rgba(163, 230, 53, 0.25);
}

/* DROPDOWN – DESKTOP HOVER */
@media(min-width:1024px){
    .nav-item.dropdown:hover > .dropdown-menu {
        display: block;
    }
}

/* DROPDOWN MENU */
.nav-item .dropdown-menu {
    display: none;
    margin-left: 15px;
    padding-left: 10px;
}
.nav-item .dropdown-menu .dropdown-item {
    display: block;
    padding: 8px 12px;
    color: #d1d5db;
    border-radius: 6px;
    transition: 0.3s;
}
.nav-item .dropdown-menu .dropdown-item:hover {
    background: linear-gradient(135deg, #a3e635, #bef264);
    color: #18181b;
}

/* MOBILE DROPDOWN CLICK */
@media(max-width:1023px){
    .nav-item.dropdown.open > .dropdown-menu {
        display: block;
    }
}

/* MOBILE OVERLAY */
#sidebarOverlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 20;
    opacity: 0;
    transition: opacity .3s;
}
#sidebarOverlay.show {
    display: block;
    opacity: 1;
}

/* MAIN CONTENT */
main {
    flex: 1;
    margin-left: 230px;
    padding: 30px;
    transition: 0.3s;
}
@media(max-width:1023px){
    main {
        margin-left: 0;
    }
}

/* TOPBAR */
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
@media(max-width:1023px){
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
}

/* FOOTER */
footer {
    margin-left: 230px;
    padding: 15px 0;
    text-align: center;
    font-size: 0.9rem;
    color: #a1a1aa;
    background: #27272a;
    border-top: 1px solid #3f3f46;
    box-shadow: 0 -1px 5px rgba(0,0,0,0.3);
}
@media(max-width:1023px){
    footer {
        margin-left: 0;
    }
}
</style>

</head>

<body>
        @include('layout.partials.page-loader')


<!-- MOBILE OVERLAY -->
<div id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
<aside id="sidebar">

    <div class="logo flex items-center justify-center space-x-2">
        <img src="{{ asset('images/logoPadang.png') }}" class="w-10 h-10 bg-white rounded-full p-1">
        <span>PadangPro</span>
    </div>

    <nav>
        <ul class="nav flex-column">

            <li class="nav-item">
                <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active':'' }}">
                    <i class="bi bi-grid-1x2-fill mr-2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('staff.profile') }}" class="{{ request()->routeIs('staff.profile*') ? 'active':'' }}">
                    <i class="bi bi-person-fill mr-2"></i> Profile
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('staff.booking.*') ? 'active':'' }}" href="#" onclick="toggleDropdown(event, this)">
                    <i class="bi bi-calendar-check-fill mr-2"></i> Manage Bookings
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('staff.booking.viewAll') }}">View All Bookings</a></li>
                    <li><a class="dropdown-item" href="{{ route('staff.booking.manage') }}">Book Standard Pitch</a></li>
                    <li><a class="dropdown-item" href="{{ route('staff.booking.mini') }}">Book Mini Pitch</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('staff.rental.main') }}" class="{{ request()->routeIs('staff.rental.*') ? 'active':'' }}">
                    <i class="bi bi-tags-fill mr-2"></i> Manage Rentals
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('staff.rating.view') }}">
                    <i class="bi bi-star-fill mr-2"></i> Rating & Reviews
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('staff.reports.*') ? 'active':'' }}" href="#" onclick="toggleDropdown(event, this)">
                    <i class="bi bi-graph-up mr-2"></i> Reports
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('staff.reports.index') }}">Reports Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('staff.reports.published') }}">Published Reports</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('logout') }}">
                    <i class="bi bi-box-arrow-left mr-2"></i> Logout
                </a>
            </li>

        </ul>
    </nav>

</aside>

<!-- MAIN -->
<main>
    <div class="topbar">
        <button class="menu-toggle" onclick="toggleSidebar()">☰</button>

        <div>Welcome, <strong>{{ $fullName ?? session('full_name', 'Staff') }}</strong></div>
    </div>

    @yield('content')
</main>

<!-- FOOTER -->
<footer>© {{ date('Y') }} PadangPro Staff Panel. All Rights Reserved.</footer>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function toggleSidebar(){
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
}

/* MOBILE DROPDOWN */
function toggleDropdown(event, element){
    if(window.innerWidth < 1024){
        event.preventDefault();

        const item = element.closest('.nav-item.dropdown');

        document.querySelectorAll('.nav-item.dropdown').forEach(x=>{
            if(x !== item) x.classList.remove('open');
        });

        item.classList.toggle('open');
    }
}

/* CLOSE SIDEBAR ON CLICK */
document.querySelectorAll('aside nav a:not(.dropdown-toggle)').forEach(link=>{
    link.addEventListener('click', function(){
        if(window.innerWidth < 1023){
            toggleSidebar();
        }
    });
});

/* AUTO OPEN DROPDOWN WHEN MATCHING ROUTE */
document.addEventListener('DOMContentLoaded', ()=>{
    if(window.innerWidth < 1024){
        const currentPath = window.location.pathname;

        document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item=>{
            if(item.getAttribute('href') === currentPath){
                const parent = item.closest('.nav-item.dropdown');
                if(parent) parent.classList.add('open');
            }
        });
    }
});


</script>
@include('layout.partials.idle-logout')
@stack('scripts')
</body>
</html>
